/**
 * AutoCare System - Smooth Partial Page Navigation
 * Provides persistent topbar and sidebar with smooth main content updates.
 */

let abortController = null;
let loadingTimeout = null;

function getProgressBar() {
    let bar = document.getElementById('page-loading-bar');
    if (!bar) {
        bar = document.createElement('div');
        bar.id = 'page-loading-bar';
        bar.className = 'fixed top-0 left-0 right-0 z-50 h-[3px] bg-gradient-to-r from-blue-500 via-indigo-500 to-[#0B1849] opacity-0 pointer-events-none transition-all duration-300 ease-out';
        bar.style.width = '0%';
        document.body.appendChild(bar);
    }
    return bar;
}

function startLoading() {
    const bar = getProgressBar();
    clearTimeout(loadingTimeout);
    bar.style.transition = 'width 300ms ease-out, opacity 150ms ease-in';
    bar.style.opacity = '1';
    bar.style.width = '20%';

    loadingTimeout = setTimeout(() => {
        bar.style.width = '70%';
    }, 150);
}

function stopLoading() {
    const bar = getProgressBar();
    clearTimeout(loadingTimeout);
    bar.style.transition = 'width 150ms ease-out, opacity 250ms ease-out';
    bar.style.width = '100%';

    loadingTimeout = setTimeout(() => {
        bar.style.opacity = '0';
        setTimeout(() => {
            bar.style.width = '0%';
        }, 250);
    }, 200);
}

/**
 * Synchronize sidebar menu links and parent section highlighting without unmounting the sidebar
 */
function syncSidebar(newDoc) {
    const currentSidebar = document.getElementById('app-sidebar');
    const newSidebar = newDoc.getElementById('app-sidebar');
    if (!currentSidebar || !newSidebar) return;

    // 1. Get the new active section and notify sidebar Alpine state
    const newActiveSection = newSidebar.getAttribute('data-active-section') || '';
    window.dispatchEvent(new CustomEvent('navigated', {
        detail: { activeSection: newActiveSection }
    }));

    // Close mobile drawer on navigation
    window.dispatchEvent(new CustomEvent('close-mobile-sidebar'));

    // 2. Synchronize navigation links classes & indicator dots
    const currentContainer = document.getElementById('sidebar-nav-container');
    const newContainer = newDoc.getElementById('sidebar-nav-container');
    if (currentContainer && newContainer) {
        const newLinks = newContainer.querySelectorAll('a[href]');
        newLinks.forEach(newA => {
            const href = newA.getAttribute('href');
            if (!href) return;
            const currentA = currentContainer.querySelector(`a[href="${CSS.escape(href)}"]`);
            if (currentA) {
                currentA.className = newA.className;
                const curDot = currentA.querySelector('span[class*="rounded-full"]');
                const newDot = newA.querySelector('span[class*="rounded-full"]');
                if (curDot && newDot) {
                    curDot.className = newDot.className;
                }
            }
        });

        // 3. Synchronize parent section button classes
        const currentButtons = currentContainer.querySelectorAll('button[type="button"]');
        const newButtons = newContainer.querySelectorAll('button[type="button"]');
        newButtons.forEach(newBtn => {
            const label = newBtn.innerText.trim();
            currentButtons.forEach(curBtn => {
                if (curBtn.innerText.trim() === label) {
                    curBtn.className = newBtn.className;
                }
            });
        });
    }

    // 4. Synchronize topbar shortcut buttons
    const curTopShortcuts = document.getElementById('topbar-shortcuts');
    const newTopShortcuts = newDoc.getElementById('topbar-shortcuts');
    if (curTopShortcuts && newTopShortcuts) {
        curTopShortcuts.innerHTML = newTopShortcuts.innerHTML;
    }
}

/**
 * Perform partial navigation to a target URL
 */
export async function navigateTo(targetUrl, pushState = true) {
    const url = new URL(targetUrl, window.location.origin);

    // If same page without hash change, ignore
    if (url.href === window.location.href) {
        return;
    }

    // Abort prior request
    if (abortController) {
        abortController.abort();
    }
    abortController = new AbortController();

    const mainContent = document.getElementById('main-content');
    if (!mainContent) {
        window.location.href = url.href;
        return;
    }

    startLoading();
    mainContent.style.transition = 'opacity 150ms ease';
    mainContent.style.opacity = '0.4';

    try {
        const response = await fetch(url.href, {
            signal: abortController.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-AutoCare-Navigation': '1',
            },
        });

        // If redirected to login or external
        if (response.redirected) {
            const redirectedUrl = new URL(response.url);
            if (redirectedUrl.origin !== window.location.origin) {
                window.location.href = response.url;
                return;
            }
        }

        if (!response.ok) {
            window.location.href = url.href;
            return;
        }

        const htmlText = await response.text();
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(htmlText, 'text/html');

        const newMainContent = newDoc.getElementById('main-content');
        if (!newMainContent) {
            // Not a compatible partial layout (e.g. auth login, print invoice)
            window.location.href = response.url || url.href;
            return;
        }

        // 1. Update Document Title
        document.title = newDoc.title;

        // 2. Sync Persistent Sidebar & Topbar
        syncSidebar(newDoc);

        // 3. Clear any page-specific unsaved guards
        window.__posCartHasItems = null;

        // 4. Remove any stray elements appended to document.body that do not belong to layout
        Array.from(document.body.children).forEach(child => {
            if (
                child.id !== 'page-loading-bar' &&
                !child.classList.contains('min-h-full') &&
                child.tagName !== 'SCRIPT' &&
                child.tagName !== 'STYLE' &&
                child.tagName !== 'LINK'
            ) {
                try {
                    child.remove();
                } catch (e) {
                    // Ignore removal error
                }
            }
        });

        // 5. Cleanup old Alpine components in main
        if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
            try {
                window.Alpine.destroyTree(mainContent);
            } catch (e) {
                console.warn('Alpine destroyTree error:', e);
            }
        }

        // 6. Swap Main Content HTML
        mainContent.innerHTML = newMainContent.innerHTML;

        // 7. Execute newly delivered inline/page scripts
        const incomingScripts = newDoc.querySelectorAll('#main-content script, body > script:not([src*="vite"]):not([src*="app.js"])');
        incomingScripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
            newScript.textContent = oldScript.textContent;
            document.body.appendChild(newScript);
            newScript.remove();
        });

        // 8. Initialize Alpine on new content
        if (window.Alpine && typeof window.Alpine.initTree === 'function') {
            try {
                window.Alpine.initTree(mainContent);
            } catch (e) {
                console.warn('Alpine initTree error:', e);
            }
        }

        // 9. Update browser history
        if (pushState) {
            history.pushState({ url: response.url || url.href }, '', response.url || url.href);
        }

        // 10. Scroll smoothly to top
        window.scrollTo({ top: 0, behavior: 'instant' });

        // 11. Dispatch page change event for any page-specific components
        window.dispatchEvent(new CustomEvent('page-navigated', {
            detail: { url: response.url || url.href }
        }));

    } catch (err) {
        if (err.name !== 'AbortError') {
            console.error('Navigation fetch failed, falling back to full reload:', err);
            window.location.href = url.href;
        }
    } finally {
        mainContent.style.opacity = '1';
        stopLoading();
    }
}

let isNavigationInitialized = false;

/**
 * Initialize link click interceptor and popstate listener
 */
export function initNavigation() {
    if (isNavigationInitialized) {
        return;
    }
    isNavigationInitialized = true;

    document.addEventListener('click', (e) => {
        // Find closest link
        const link = e.target.closest('a');
        if (!link) return;

        // Must have an href
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return;
        }

        // Bypass for new tab, downloads, modifier keys
        if (link.target === '_blank' || link.hasAttribute('download') || link.closest('[data-no-nav]')) {
            return;
        }
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) {
            return;
        }

        const url = new URL(link.href, window.location.origin);

        // Same origin only
        if (url.origin !== window.location.origin) {
            return;
        }

        // Bypass print and file export URLs
        if (url.pathname.includes('/print') || url.pathname.includes('/export') || url.pathname.includes('/download')) {
            return;
        }

        // Same URL hash jump
        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) {
            return;
        }

        // Guard active POS cart or unsaved items
        if (typeof window.__posCartHasItems === 'function' && window.__posCartHasItems()) {
            if (!confirm('You have active items in your Direct Sale cart. Navigating away will discard your current sale. Do you want to continue?')) {
                e.preventDefault();
                return;
            }
        }

        e.preventDefault();
        navigateTo(link.href, true);
    });

    // Handle Browser Back / Forward buttons
    window.addEventListener('popstate', () => {
        navigateTo(window.location.href, false);
    });
}

// Auto-initialize when loaded
if (typeof window !== 'undefined') {
    initNavigation();
}
