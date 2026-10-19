<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ServicePaymentController extends Controller
{
    /**
     * Display the Manage Service Payment placeholder page.
     */
    public function index(): View
    {
        return view('master-data.service-payments.index');
    }
}
