<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\RewardTier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RewardTierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(RewardTier::query())
                ->addColumn('status_badge', function ($row) {
                    $color = $row->status === 'active' ? 'success' : 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $toggle = $row->status === 'active' ? 'Deactivate' : 'Activate';
                    $toggleColor = $row->status === 'active' ? 'warning' : 'success';
                    return '
                        <a href="' . route('hq.reward-tiers.edit', $row) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="' . route('hq.reward-tiers.toggle-status', $row) . '" class="d-inline">
                            ' . csrf_field() . method_field('PATCH') . '
                            <button class="btn btn-sm btn-outline-' . $toggleColor . '">' . $toggle . '</button>
                        </form>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('hq.reward-tiers.index');
    }

    public function create()
    {
        return view('hq.reward-tiers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'min_points' => 'required|integer|min:0',
            'reward_description' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        RewardTier::create($validated);

        return redirect()->route('hq.reward-tiers.index')->with('success', 'Reward tier created.');
    }

    public function edit(RewardTier $reward_tier)
    {
        return view('hq.reward-tiers.edit', ['tier' => $reward_tier]);
    }

    public function update(Request $request, RewardTier $reward_tier)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'min_points' => 'required|integer|min:0',
            'reward_description' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $reward_tier->update($validated);

        return redirect()->route('hq.reward-tiers.index')->with('success', 'Reward tier updated.');
    }

    public function toggleStatus(RewardTier $reward_tier)
    {
        $reward_tier->update([
            'status' => $reward_tier->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Status updated.');
    }
}
