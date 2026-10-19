<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientSmsController extends Controller
{
    /**
     * Display the Search Client SMS page structure.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $filterType = $request->input('filter_type', 'all');

        // Customer records module is scheduled for the upcoming phase.
        // We provide a realistic empty collection and interactive structure.
        $customers = collect([]);

        return view('master-data.client-sms.index', compact('search', 'filterType', 'customers'));
    }
}
