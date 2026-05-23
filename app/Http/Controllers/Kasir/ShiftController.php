<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function create()
    {
        $currentShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($currentShift) {
            return redirect()->route('kasir.dashboard')->with('info', 'Anda masih memiliki shift yang terbuka.');
        }

        return view('kasir.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        $currentShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($currentShift) {
            return redirect()->route('kasir.dashboard')->with('error', 'Shift sudah terbuka.');
        }

        Shift::create([
            'branch_id' => Auth::user()->branch_id,
            'user_id' => Auth::id(),
            'start_time' => now(),
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
        ]);

        return redirect()->route('kasir.dashboard')->with('success', 'Shift berhasil dibuka. Selamat bekerja!');
    }

    public function edit()
    {
        $shift = Shift::where('user_id', Auth::id())->where('status', 'open')->firstOrFail();
        
        // Calculate expected cash
        // expected_cash = starting_cash + total_paid_amount_from_sales - total_change_amount_from_sales
        $sales = $shift->sales;
        $totalSalesCash = $sales->sum('paid_amount') - $sales->sum('change_amount');
        $expectedCash = $shift->starting_cash + $totalSalesCash;

        return view('kasir.shifts.close', compact('shift', 'expectedCash', 'totalSalesCash', 'sales'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ending_cash_actual' => 'required|numeric|min:0',
        ]);

        $shift = Shift::where('user_id', Auth::id())->where('status', 'open')->firstOrFail();

        $sales = $shift->sales;
        $totalSalesCash = $sales->sum('paid_amount') - $sales->sum('change_amount');
        $expectedCash = $shift->starting_cash + $totalSalesCash;

        $shift->update([
            'end_time' => now(),
            'ending_cash_expected' => $expectedCash,
            'ending_cash_actual' => $request->ending_cash_actual,
            'status' => 'closed',
        ]);

        return redirect()->route('kasir.dashboard')->with('success', 'Shift berhasil ditutup.');
    }
}
