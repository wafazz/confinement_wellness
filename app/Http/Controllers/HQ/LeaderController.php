<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LeaderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $leaders = User::where('role', 'leader')
                ->withCount('therapists');

            return DataTables::of($leaders)
                ->addColumn('team_size', fn($row) => $row->therapists_count)
                ->addColumn('status_badge', function ($row) {
                    $color = match ($row->status) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = route('hq.leaders.show', $row->id);
                    $editUrl = route('hq.leaders.edit', $row->id);
                    $teamUrl = route('hq.leaders.team', $row->id);
                    $toggleUrl = route('hq.leaders.toggle-status', $row->id);
                    $statusLabel = $row->status === 'active' ? 'Deactivate' : 'Activate';
                    $statusIcon = $row->status === 'active' ? 'fa-ban' : 'fa-check';
                    $statusColor = $row->status === 'active' ? 'warning' : 'success';

                    return '
                        <a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                        <a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="' . $teamUrl . '" class="btn btn-sm btn-secondary" title="Team"><i class="fas fa-users"></i></a>
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

        return view('hq.leaders.index');
    }

    public function create()
    {
        return view('hq.leaders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'ic_number' => 'required|string|max:20|unique:users,ic_number',
            'password' => 'required|string|min:8|confirmed',
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'kkm_cert_no' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'leader';
        $validated['status'] = 'active';

        $leader = User::create($validated);
        $leader->assignRole('leader');

        return redirect()->route('hq.leaders.index')->with('success', 'Leader created successfully.');
    }

    public function show(User $leader)
    {
        $leader->loadCount('therapists');
        return view('hq.leaders.show', compact('leader'));
    }

    public function edit(User $leader)
    {
        return view('hq.leaders.edit', compact('leader'));
    }

    public function update(Request $request, User $leader)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $leader->id,
            'phone' => 'required|string|max:20',
            'ic_number' => 'required|string|max:20|unique:users,ic_number,' . $leader->id,
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'kkm_cert_no' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $validated['password'] = bcrypt($request->password);
        }

        $leader->update($validated);

        return redirect()->route('hq.leaders.index')->with('success', 'Leader updated successfully.');
    }

    public function destroy(User $leader)
    {
        $leader->delete();
        return redirect()->route('hq.leaders.index')->with('success', 'Leader deleted successfully.');
    }

    public function toggleStatus(User $leader)
    {
        $leader->status = $leader->status === 'active' ? 'inactive' : 'active';
        $leader->save();

        return back()->with('success', 'Leader status updated to ' . $leader->status . '.');
    }

    public function team(User $leader)
    {
        $therapists = $leader->therapists()->get();
        return view('hq.leaders.team', compact('leader', 'therapists'));
    }
}
