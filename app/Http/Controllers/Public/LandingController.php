<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CommissionRule;

class LandingController extends Controller
{
    public function index()
    {
        $services = CommissionRule::where('status', 'active')
            ->whereNotNull('price')
            ->get();

        return view('public.landing', compact('services'));
    }
}
