<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PointController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $points = Point::with(['user', 'serviceJob']);

            if ($request->filled('filter_month')) {
                $points->where('month', $request->filter_month);
            }

            return DataTables::of($points)
                ->addColumn('user_name', fn($row) => $row->user->name ?? '-')
                ->addColumn('user_role', fn($row) => ucfirst($row->user->role ?? '-'))
                ->addColumn('job_code', fn($row) => $row->serviceJob->job_code ?? '-')
                ->addColumn('service_type', fn($row) => $row->serviceJob->service_type ?? '-')
                ->make(true);
        }

        return view('hq.points.index');
    }
}
