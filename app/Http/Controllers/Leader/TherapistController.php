<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TherapistRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class TherapistController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $therapists = User::where('role', 'therapist')
                ->where('leader_id', Auth::id());

            return DataTables::of($therapists)
                ->addColumn('status_badge', function ($row) {
                    $color = match ($row->status) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = route('leader.therapists.show', $row->id);
                    $toggleUrl = route('leader.therapists.toggle-status', $row->id);
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

        return view('leader.therapists.index');
    }

    public function create()
    {
        return view('leader.therapists.create');
    }

    public function store(Request $request)
    {
        $leader = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'ic_number' => 'required|string|max:20|unique:users,ic_number',
            'password' => 'required|string|min:8|confirmed',
            'district' => 'required|string|max:100',
            'kkm_cert_no' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'therapist';
        $validated['status'] = 'pending';
        $validated['leader_id'] = $leader->id;
        $validated['state'] = $leader->state;

        $therapist = User::create($validated);
        $therapist->assignRole('therapist');

        $hqAdmins = User::where('role', 'hq')->get();
        Notification::send($hqAdmins, new TherapistRegistered($therapist, $leader));

        return redirect()->route('leader.therapists.index')->with('success', 'Therapist registered successfully. Pending HQ approval.');
    }

    public function show(User $therapist)
    {
        $this->authorizeTeamMember($therapist);
        return view('leader.therapists.show', compact('therapist'));
    }

    public function toggleStatus(User $therapist)
    {
        $this->authorizeTeamMember($therapist);
        $therapist->status = $therapist->status === 'active' ? 'inactive' : 'active';
        $therapist->save();

        return back()->with('success', 'Therapist status updated to ' . $therapist->status . '.');
    }

    private function authorizeTeamMember(User $therapist)
    {
        abort_if($therapist->leader_id !== Auth::id(), 403, 'This therapist is not in your team.');
    }
}
