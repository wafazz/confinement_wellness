<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TherapistController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $therapists = User::where('role', 'therapist')
                ->with('leader:id,name');

            return DataTables::of($therapists)
                ->addColumn('leader_name', fn($row) => $row->leader->name ?? '-')
                ->addColumn('status_badge', function ($row) {
                    $color = match ($row->status) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = route('hq.therapists.show', $row->id);
                    $toggleUrl = route('hq.therapists.toggle-status', $row->id);
                    $statusLabel = $row->status === 'active' ? 'Deactivate' : 'Activate';
                    $statusIcon = $row->status === 'active' ? 'fa-ban' : 'fa-check';
                    $statusColor = $row->status === 'active' ? 'warning' : 'success';

                    return '
                        <a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                        <form action="' . $toggleUrl . '" method="POST" class="d-inline">
                            ' . csrf_field() . method_field('PATCH') . '
                            <button type="submit" class="btn btn-sm btn-' . $statusColor . '" title="' . $statusLabel . '" onclick="return confirm(\'Are you sure?\')">
                                <i class="fas ' . $statusIcon . '"></i>
                            </button>
                        </form>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('hq.therapists.index');
    }

    public function show(User $therapist)
    {
        $therapist->load('leader:id,name');
        $therapist->loadCount([
            'assignedJobs as total_jobs',
            'assignedJobs as completed_jobs' => fn($q) => $q->where('status', 'completed'),
        ]);
        $therapist->loadSum([
            'commissions as total_commission' => fn($q) => $q->where('type', 'direct'),
        ], 'amount');
        $therapist->loadSum('points as total_points', 'points');

        return view('hq.therapists.show', compact('therapist'));
    }

    public function create()
    {
        $leaders = User::where('role', 'leader')->where('status', 'active')->orderBy('name')->get();
        return view('hq.therapists.create', compact('leaders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'ic_number' => 'required|string|max:20|unique:users,ic_number',
            'password' => 'required|string|min:8|confirmed',
            'leader_id' => 'required|exists:users,id',
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'kkm_cert_no' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'therapist';
        $validated['status'] = 'active';

        $therapist = User::create($validated);
        $therapist->assignRole('therapist');

        return redirect()->route('hq.therapists.index')->with('success', 'Therapist created successfully.');
    }

    public function toggleStatus(User $therapist)
    {
        $therapist->status = $therapist->status === 'active' ? 'inactive' : 'active';
        $therapist->save();

        return back()->with('success', 'Therapist status updated to ' . $therapist->status . '.');
    }
}
