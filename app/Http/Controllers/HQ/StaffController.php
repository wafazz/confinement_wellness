<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $staff = User::where('role', 'staff');

            return DataTables::of($staff)
                ->addColumn('permissions_list', function ($row) {
                    return $row->permissions->pluck('name')->map(fn($p) => '<span class="badge bg-info me-1">' . str_replace('access-', '', $p) . '</span>')->implode('');
                })
                ->addColumn('status_badge', function ($row) {
                    $color = match ($row->status) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                        default => 'secondary',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = route('hq.staff.show', $row->id);
                    $editUrl = route('hq.staff.edit', $row->id);
                    $toggleUrl = route('hq.staff.toggle-status', $row->id);
                    $statusLabel = $row->status === 'active' ? 'Deactivate' : 'Activate';
                    $statusIcon = $row->status === 'active' ? 'fa-ban' : 'fa-check';
                    $statusColor = $row->status === 'active' ? 'warning' : 'success';

                    return '
                        <a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                        <a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                        <form action="' . $toggleUrl . '" method="POST" class="d-inline">
                            ' . csrf_field() . method_field('PATCH') . '
                            <button type="submit" class="btn btn-sm btn-' . $statusColor . '" title="' . $statusLabel . '" onclick="return confirm(\'Are you sure?\')">
                                <i class="fas ' . $statusIcon . '"></i>
                            </button>
                        </form>';
                })
                ->rawColumns(['permissions_list', 'status_badge', 'action'])
                ->make(true);
        }

        return view('hq.staff.index');
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('hq.staff.create', compact('permissions'));
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
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $staff = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'ic_number' => $validated['ic_number'],
            'password' => bcrypt($validated['password']),
            'state' => $validated['state'],
            'district' => $validated['district'],
            'role' => 'staff',
            'status' => 'active',
        ]);
        $staff->assignRole('staff');
        $staff->syncPermissions($validated['permissions']);

        return redirect()->route('hq.staff.index')->with('success', 'Staff created successfully.');
    }

    public function show(User $staff)
    {
        return view('hq.staff.show', compact('staff'));
    }

    public function edit(User $staff)
    {
        $permissions = Permission::orderBy('name')->get();
        return view('hq.staff.edit', compact('staff', 'permissions'));
    }

    public function update(Request $request, User $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id,
            'phone' => 'required|string|max:20',
            'ic_number' => 'required|string|max:20|unique:users,ic_number,' . $staff->id,
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $staff->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'ic_number' => $validated['ic_number'],
            'state' => $validated['state'],
            'district' => $validated['district'],
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $staff->update(['password' => bcrypt($request->password)]);
        }

        $staff->syncPermissions($validated['permissions']);

        return redirect()->route('hq.staff.index')->with('success', 'Staff updated successfully.');
    }

    public function toggleStatus(User $staff)
    {
        $staff->status = $staff->status === 'active' ? 'inactive' : 'active';
        $staff->save();

        return back()->with('success', 'Staff status updated to ' . $staff->status . '.');
    }
}
