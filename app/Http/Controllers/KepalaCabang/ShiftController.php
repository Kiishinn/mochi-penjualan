<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $shifts = Shift::with('user')
            ->where('branch_id', $branchId)
            ->latest('start_time')
            ->paginate(15);
            
        return view('kepala-cabang.shifts.index', compact('shifts'));
    }
}
