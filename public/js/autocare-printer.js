/**
 * AutoCare System - Universal Two-Printer Engine with QZ Tray
 * Handles automatic document routing for:
 *  - Printer 1: BILL PRINTER (Bills, Direct Sales POS, 80mm Invoices, Payment Receipts)
 *  - Printer 2: JOB CARD PRINTER (Job Cards, Selected Items, Workshop Instructions)
 */

(function (window, document) {
    'use strict';

    const STORAGE_KEY_WORKSTATION = 'autocare_workstation_name';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const AutoCarePrinter = {
        isInitialized: false,
        isConnected: false,
        isConnecting: false,
        isPrinting: false,
        cachedPrinters: [],
        currentConfig: null,
        activeBranchId: null,

        /**
         * Initialize Printer Engine
         */
        async init() {
            if (this.isInitialized) return this;
            this.isInitialized = true;

            // Load stored workstation name or fallback
            this.ensureWorkstationIdentifier();

            // Set up QZ Tray Security Promises
            this.setupSecurity();

            // Attempt silent background connection to local QZ Tray
            await this.connect().catch(() => {
                console.info('[AutoCarePrinter] Local QZ Tray not running or waiting for start.');
            });

            // Fetch active routing configuration for this workstation
            await this.refreshRoutingConfig();

            return this;
        },

        /**
         * Get client workstation identifier from localStorage or generate sensible default
         */
        getWorkstation() {
            let ws = localStorage.getItem(STORAGE_KEY_WORKSTATION);
            if (!ws || ws.trim() === '') {
                // Generate a persistent local ID e.g., "Workstation-A1B2" or "FrontDesk-PC"
                const rand = Math.random().toString(36).substring(2, 6).toUpperCase();
                ws = 'Workstation-' + rand;
                localStorage.setItem(STORAGE_KEY_WORKSTATION, ws);
            }
            return ws;
        },

        /**
         * Set custom workstation identifier
         */
        setWorkstation(name) {
            if (!name || name.trim() === '') return;
            localStorage.setItem(STORAGE_KEY_WORKSTATION, name.trim());
            this.refreshRoutingConfig();
        },

        /**
         * Configure QZ Tray Security (Certificate & Backend SHA-512 Signing)
         */
        setupSecurity() {
            if (typeof qz === 'undefined') return;

            // 1. Digital Certificate Promise
            qz.security.setCertificatePromise(function (resolve, reject) {
                fetch('/print/certificate', {
                    headers: { 'Accept': 'text/plain' },
                    cache: 'no-cache'
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Certificate endpoint returned HTTP ' + res.status);
                        return res.text();
                    })
                    .then(resolve)
                    .catch(err => {
                        console.warn('[AutoCarePrinter] Failed to load certificate:', err);
                        reject(err);
                    });
            });

            // 2. Signature Algorithm & Signing Promise
            qz.security.setSignatureAlgorithm("SHA512");
            qz.security.setSignaturePromise(function (toSign) {
                return function (resolve, reject) {
                    fetch('/print/sign', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({ request: toSign })
                    })
                        .then(res => {
                            if (!res.ok) throw new Error('Signing endpoint returned HTTP ' + res.status);
                            return res.json();
                        })
                        .then(data => {
                            if (data.signature) {
                                resolve(data.signature);
                            } else {
                                reject(data.error || 'Signing failed');
                            }
                        })
                        .catch(err => {
                            console.warn('[AutoCarePrinter] Signing error:', err);
                            reject(err);
                        });
                };
            });
        },

        /**
         * Connect to local QZ Tray WebSocket
         */
        async connect(retries = 1) {
            if (typeof qz === 'undefined') {
                this.isConnected = false;
                throw new Error('QZ Tray library not loaded.');
            }

            if (qz.websocket.isActive()) {
                this.isConnected = true;
                return true;
            }

            if (this.isConnecting) return false;
            this.isConnecting = true;

            try {
                await qz.websocket.connect({ retries: retries, delay: 1 });
                this.isConnected = true;
                this.isConnecting = false;
                // Query installed local printers
                await this.listPrinters();
                this.dispatchStatusEvent(true);
                return true;
            } catch (err) {
                this.isConnected = false;
                this.isConnecting = false;
                this.dispatchStatusEvent(false, err.message);
                throw err;
            }
        },

        /**
         * Disconnect from QZ Tray
         */
        async disconnect() {
            if (typeof qz !== 'undefined' && qz.websocket.isActive()) {
                await qz.websocket.disconnect();
            }
            this.isConnected = false;
            this.dispatchStatusEvent(false);
        },

        /**
         * Query local printers installed on this workstation
         */
        async listPrinters() {
            if (!this.isConnected && typeof qz !== 'undefined' && !qz.websocket.isActive()) {
                try {
                    await this.connect();
                } catch {
                    return this.cachedPrinters || [];
                }
            }

            try {
                const printers = await qz.printers.find();
                this.cachedPrinters = Array.isArray(printers) ? printers : [];
                return this.cachedPrinters;
            } catch (err) {
                console.warn('[AutoCarePrinter] Could not list printers:', err);
                return this.cachedPrinters || [];
            }
        },

        /**
         * Fetch current workstation & branch routing config from backend
         */
        async refreshRoutingConfig(branchId = null) {
            const ws = this.getWorkstation();
            const url = new URL('/print/config', window.location.origin);
            url.searchParams.set('workstation', ws);
            if (branchId) {
                url.searchParams.set('branch_id', branchId);
                this.activeBranchId = branchId;
            }

            try {
                const res = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-cache'
                });
                if (res.ok) {
                    this.currentConfig = await res.json();
                }
            } catch (err) {
                console.warn('[AutoCarePrinter] Failed to fetch printer config:', err);
            }
            return this.currentConfig;
        },

        /**
         * Main Print Dispatcher with Automatic Two-Printer Routing
         *
         * @param {Object} opts
         *  - type: 'bill' | 'job_card' | 'invoice_80mm' | 'payment_receipt' | 'advance_receipt' | 'selected_items'
         *  - url: thermal page URL to fetch HTML from
         *  - html: raw HTML string (if already loaded)
         *  - title: document title
         *  - documentId: ID of invoice / jobCard / payment
         *  - documentNumber: Human-readable invoice # / job #
         *  - fallbackUrl: URL for preview / manual print
         */
        async printDocument(opts = {}) {
            if (this.isPrinting) {
                this.showToast('Printing already in progress. Please wait...', 'info');
                return false;
            }

            this.isPrinting = true;
            this.setButtonsBusy(true);

            const docType = (opts.type || 'bill').toLowerCase();
            const isBillDoc = ['bill', 'direct_sale', 'invoice_80mm', 'sales_invoice_80mm', 'payment_receipt', 'advance_receipt'].includes(docType);
            const role = isBillDoc ? 'bill' : 'job_card';
            const roleTitle = isBillDoc ? 'Bill Printer' : 'Job Card Printer';

            try {
                // 1. Refresh latest config if not yet loaded
                if (!this.currentConfig) {
                    await this.refreshRoutingConfig(opts.branchId || this.activeBranchId);
                }

                const config = this.currentConfig || {};
                const autoRoute = config.auto_route !== false; // default true
                const assignedPrinter = isBillDoc ? config.bill_printer : config.job_card_printer;

                // 2. Check if Auto Route is disabled or printer is not configured
                if (!assignedPrinter || assignedPrinter.trim() === '') {
                    this.logStatus({
                        document_type: docType,
                        document_id: opts.documentId,
                        document_number: opts.documentNumber,
                        printer_name: null,
                        workstation_name: this.getWorkstation(),
                        status: 'failed',
                        error_message: `${roleTitle} is not configured for this workstation.`
                    });

                    this.showConnectionError(roleTitle, null, () => {
                        this.printDocument(opts);
                    }, opts.url || opts.fallbackUrl);

                    return false;
                }

                // 3. Check QZ Tray connection
                if (!this.isConnected) {
                    try {
                        await this.connect();
                    } catch {
                        this.logStatus({
                            document_type: docType,
                            document_id: opts.documentId,
                            document_number: opts.documentNumber,
                            printer_name: assignedPrinter,
                            workstation_name: this.getWorkstation(),
                            status: 'failed',
                            error_message: `QZ Tray is not running on workstation '${this.getWorkstation()}'.`
                        });

                        this.showConnectionError(roleTitle, assignedPrinter, () => {
                            this.printDocument(opts);
                        }, opts.url || opts.fallbackUrl);

                        return false;
                    }
                }

                // 4. Verify that assigned printer is available on this workstation
                const printers = await this.listPrinters();
                const matchedPrinter = printers.find(p => p.toLowerCase() === assignedPrinter.toLowerCase()) ||
                    printers.find(p => p.toLowerCase().includes(assignedPrinter.toLowerCase()));

                if (!matchedPrinter) {
                    this.logStatus({
                        document_type: docType,
                        document_id: opts.documentId,
                        document_number: opts.documentNumber,
                        printer_name: assignedPrinter,
                        workstation_name: this.getWorkstation(),
                        status: 'failed',
                        error_message: `${roleTitle} '${assignedPrinter}' is not connected or offline.`
                    });

                    this.showConnectionError(roleTitle, assignedPrinter, () => {
                        this.printDocument(opts);
                    }, opts.url || opts.fallbackUrl);

                    return false;
                }

                // 5. Retrieve HTML Content
                let htmlContent = opts.html;
                if (!htmlContent && opts.url) {
                    this.showToast(`Loading document for ${roleTitle}...`, 'info');
                    const fetchRes = await fetch(opts.url, {
                        headers: { 'Accept': 'text/html' },
                        cache: 'no-cache'
                    });
                    if (!fetchRes.ok) {
                        throw new Error(`Failed to load document content (HTTP ${fetchRes.status})`);
                    }
                    htmlContent = await fetchRes.text();
                }

                if (!htmlContent || htmlContent.trim() === '') {
                    throw new Error('No printable content available.');
                }

                // 6. Clean and Prepare 80mm Print Payload
                const sanitizedHtml = this.cleanHtmlForThermal(htmlContent);

                // 7. Configure QZ Pixel/HTML 80mm Print Job
                const printConfig = qz.configs.create(matchedPrinter, {
                    size: { width: 80, height: null },
                    units: 'mm',
                    colorType: 'grayscale',
                    margins: { top: 0, right: 0, bottom: 0, left: 0 },
                    rasterize: false
                });

                const printData = [
                    {
                        type: 'pixel',
                        format: 'html',
                        flavor: 'plain',
                        data: sanitizedHtml
                    }
                ];

                this.showToast(`Sending to ${roleTitle} (${matchedPrinter})...`, 'info');

                // 8. Execute Silent Print
                await qz.print(printConfig, printData);

                // 9. Log Successful Print Status
                this.logStatus({
                    document_type: docType,
                    document_id: opts.documentId,
                    document_number: opts.documentNumber,
                    printer_name: matchedPrinter,
                    workstation_name: this.getWorkstation(),
                    status: 'printed'
                });

                this.showToast(`Document sent to ${roleTitle} successfully.`, 'success');
                return true;

            } catch (err) {
                console.error('[AutoCarePrinter] Print error:', err);

                this.logStatus({
                    document_type: docType,
                    document_id: opts.documentId,
                    document_number: opts.documentNumber,
                    printer_name: assignedPrinter || null,
                    workstation_name: this.getWorkstation(),
                    status: 'failed',
                    error_message: err.message
                });

                this.showConnectionError(roleTitle, assignedPrinter, () => {
                    this.printDocument(opts);
                }, opts.url || opts.fallbackUrl);

                return false;

            } finally {
                this.isPrinting = false;
                this.setButtonsBusy(false);
            }
        },

        /**
         * Convenience helper to print via URL
         */
        printUrl(url, opts = {}) {
            return this.printDocument({ ...opts, url: url });
        },

        /**
         * Clean HTML to strip toolbars and ensure optimal 80mm thermal rendering
         */
        cleanHtmlForThermal(html) {
            // Strip out .no-print-bar elements
            let clean = html.replace(/<div class="no-print-bar"[\s\S]*?<\/div>\s*<\/div>/gi, '');
            clean = clean.replace(/<div class="no-print-bar"[\s\S]*?<\/div>/gi, '');

            // Ensure 80mm body width and zero margins
            if (!clean.includes('@media print') && !clean.includes('thermal-receipt')) {
                clean = clean.replace('<head>', '<head><style>@page { size: 80mm auto; margin: 0; } body { margin: 0; padding: 2mm; width: 80mm; }</style>');
            }

            return clean;
        },

        /**
         * Render Connection Error Modal matching Requirement 7:
         * "Bill Printer is not connected." or "Job Card Printer is not connected."
         * Actions:
         *  - Retry
         *  - Preview
         *  - Open Browser Print Dialog
         *  - Cancel
         */
        showConnectionError(roleTitle, printerName, retryCallback, previewUrl) {
            // Remove any existing error modal
            const existing = document.getElementById('autocare-printer-error-modal');
            if (existing) existing.remove();

            const printerDetail = printerName ? ` (${printerName})` : ' (No printer assigned)';
            const headline = `${roleTitle} is not connected.`;
            const subtext = printerName
                ? `The assigned printer "${printerName}" on workstation "${this.getWorkstation()}" could not be reached via QZ Tray.`
                : `No printer has been configured for ${roleTitle} on workstation "${this.getWorkstation()}". Please check Printer Settings under Administration.`;

            const modalHtml = `
            <div id="autocare-printer-error-modal" class="fixed inset-0 z-9999 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-fade-in" style="font-family: inherit;">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-gray-100 transform transition-all">
                    
                    <!-- Header -->
                    <div class="bg-rose-50 border-b border-rose-100 px-6 py-4 flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-rose-900 leading-snug">${headline}</h3>
                            <p class="text-xs text-rose-700 font-medium">${roleTitle}${printerDetail}</p>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5 space-y-3">
                        <p class="text-xs text-gray-600 leading-relaxed">${subtext}</p>
                        
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 text-xs space-y-1">
                            <div class="flex justify-between text-gray-500">
                                <span>Workstation:</span>
                                <span class="font-bold text-gray-800">${this.getWorkstation()}</span>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>QZ Tray Service:</span>
                                <span class="font-bold ${this.isConnected ? 'text-emerald-600' : 'text-rose-600'}">
                                    ${this.isConnected ? 'Connected' : 'Offline / Not Running'}
                                </span>
                            </div>
                        </div>

                        <p class="text-[11px] text-gray-500 italic">
                            Select an alternative action below:
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row gap-2">
                        <button 
                            type="button" 
                            id="btn-printer-retry"
                            class="flex-1 inline-flex items-center justify-center px-3.5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Retry
                        </button>

                        ${previewUrl ? `
                        <button 
                            type="button" 
                            id="btn-printer-preview"
                            class="inline-flex items-center justify-center px-3.5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl transition cursor-pointer"
                        >
                            Preview
                        </button>
                        ` : ''}

                        <button 
                            type="button" 
                            id="btn-printer-browser"
                            class="inline-flex items-center justify-center px-3.5 py-2.5 bg-sky-50 border border-sky-200 hover:bg-sky-100 text-sky-800 text-xs font-semibold rounded-xl transition cursor-pointer"
                        >
                            Browser Print
                        </button>

                        <button 
                            type="button" 
                            id="btn-printer-cancel"
                            class="inline-flex items-center justify-center px-3 py-2.5 text-gray-500 hover:text-gray-700 text-xs font-medium rounded-xl transition cursor-pointer"
                        >
                            Cancel
                        </button>
                    </div>

                </div>
            </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Bind handlers
            document.getElementById('btn-printer-retry')?.addEventListener('click', () => {
                const modal = document.getElementById('autocare-printer-error-modal');
                if (modal) modal.remove();
                if (typeof retryCallback === 'function') retryCallback();
            });

            document.getElementById('btn-printer-preview')?.addEventListener('click', () => {
                const modal = document.getElementById('autocare-printer-error-modal');
                if (modal) modal.remove();
                if (previewUrl) window.open(previewUrl, '_blank');
            });

            document.getElementById('btn-printer-browser')?.addEventListener('click', () => {
                const modal = document.getElementById('autocare-printer-error-modal');
                if (modal) modal.remove();
                this.logStatus({
                    document_type: role,
                    printer_name: printerName,
                    workstation_name: this.getWorkstation(),
                    status: 'fallback_browser'
                });
                if (previewUrl) {
                    const printWindow = window.open(previewUrl, '_blank');
                    if (printWindow) {
                        printWindow.focus();
                    }
                } else {
                    window.print();
                }
            });

            document.getElementById('btn-printer-cancel')?.addEventListener('click', () => {
                const modal = document.getElementById('autocare-printer-error-modal');
                if (modal) modal.remove();
                this.logStatus({
                    document_type: role,
                    printer_name: printerName,
                    workstation_name: this.getWorkstation(),
                    status: 'cancelled'
                });
            });
        },

        /**
         * Log print outcome / errors to server
         */
        logStatus(data) {
            fetch('/print/log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify(data)
            }).catch(() => { });
        },

        /**
         * UI Helper: Set button busy state to prevent double clicks
         */
        setButtonsBusy(busy) {
            const printButtons = document.querySelectorAll('[data-print-trigger], .btn-print-thermal');
            printButtons.forEach(btn => {
                if (busy) {
                    btn.classList.add('opacity-60', 'pointer-events-none');
                } else {
                    btn.classList.remove('opacity-60', 'pointer-events-none');
                }
            });
        },

        /**
         * Display lightweight toast notification
         */
        showToast(message, type = 'info') {
            const existing = document.getElementById('autocare-print-toast');
            if (existing) existing.remove();

            const bgClass = type === 'success' ? 'bg-emerald-800 text-white' :
                type === 'error' ? 'bg-rose-800 text-white' : 'bg-[#0B1849] text-white';

            const toast = document.createElement('div');
            toast.id = 'autocare-print-toast';
            toast.className = `fixed bottom-5 right-5 z-9999 px-4 py-3 rounded-xl shadow-xl flex items-center space-x-2 text-xs font-semibold transition-all transform duration-300 ease-out ${bgClass}`;
            toast.innerHTML = `
                <svg class="w-4 h-4 shrink-0 animate-spin mr-1.5 ${type !== 'info' ? 'hidden' : ''}" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>${message}</span>
            `;

            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 400);
            }, type === 'info' ? 3000 : 4000);
        },

        dispatchStatusEvent(connected, error = null) {
            window.dispatchEvent(new CustomEvent('autocare:printer-status', {
                detail: { connected, error, workstation: this.getWorkstation() }
            }));
        },

        ensureWorkstationIdentifier() {
            return this.getWorkstation();
        }
    };

    // Auto-init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => AutoCarePrinter.init());
    } else {
        AutoCarePrinter.init();
    }

    // Expose to window
    window.AutoCarePrinter = AutoCarePrinter;

})(window, document);
