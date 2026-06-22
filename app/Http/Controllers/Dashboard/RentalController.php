<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\GeneratorStatus;
use App\Http\Controllers\Controller;
use App\Models\Generator;
use App\Models\Quotation;

class RentalController extends Controller
{
    public function index()
    {
        $rentalQuotations = Quotation::with('customer')
            ->where('type', 'rental')
            ->latest()
            ->paginate(10);

        $rentedGenerators = Generator::with('customer')
            ->where('status', GeneratorStatus::RENTED)
            ->orderBy('serial_number')
            ->get();

        return view('dashboard.rentals.index', compact('rentalQuotations', 'rentedGenerators'));
    }
}