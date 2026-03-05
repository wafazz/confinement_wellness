<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\CommissionRule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CommissionRuleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rules = CommissionRule::query();

            return DataTables::of($rules)
                ->addColumn('category_badge', function ($row) {
                    $map = [
                        'stay_in' => ['Stay In', 'warning'],
                        'daily_visit' => ['Daily Visit', 'info'],
                        'wellness' => ['Wellness', 'primary'],
                    ];
                    $cat = $map[$row->service_category] ?? ['Unknown', 'secondary'];
                    return '<span class="badge bg-' . $cat[1] . '">' . $cat[0] . '</span>';
                })
                ->addColumn('work_days_fmt', fn($row) => $row->work_days ? $row->work_days . ' days' : '-')
                ->addColumn('status_badge', function ($row) {
                    $color = $row->status === 'active' ? 'success' : 'danger';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('therapist_amt', fn($row) => $row->therapist_commission_type === 'percentage'
                    ? number_format($row->therapist_commission, 2) . '%'
                    : 'RM ' . number_format($row->therapist_commission, 2))
                ->addColumn('leader_amt', fn($row) => $row->leader_override_type === 'percentage'
                    ? number_format($row->leader_override, 2) . '%'
                    : 'RM ' . number_format($row->leader_override, 2))
                ->addColumn('affiliate_amt', fn($row) => $row->affiliate_commission > 0
                    ? ($row->affiliate_commission_type === 'percentage'
                        ? number_format($row->affiliate_commission, 2) . '%'
                        : 'RM ' . number_format($row->affiliate_commission, 2))
                    : '-')
                ->addColumn('price_fmt', fn($row) => $row->price ? 'RM ' . number_format($row->price, 2) : '-')
                ->addColumn('action', function ($row) {
                    $editUrl = route('hq.commission-rules.edit', $row->id);
                    $toggleUrl = route('hq.commission-rules.toggle-status', $row->id);
                    $statusLabel = $row->status === 'active' ? 'Deactivate' : 'Activate';
                    $statusIcon = $row->status === 'active' ? 'fa-ban' : 'fa-check';
                    $statusColor = $row->status === 'active' ? 'warning' : 'success';

                    return '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a> '
                        . '<form action="' . $toggleUrl . '" method="POST" class="d-inline">'
                        . csrf_field() . method_field('PATCH')
                        . '<button type="submit" class="btn btn-sm btn-' . $statusColor . '" title="' . $statusLabel . '" onclick="return confirm(\'Are you sure?\')"><i class="fas ' . $statusIcon . '"></i></button>'
                        . '</form>';
                })
                ->rawColumns(['category_badge', 'status_badge', 'action'])
                ->make(true);
        }

        return view('hq.commission-rules.index');
    }

    public function create()
    {
        return view('hq.commission-rules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_category' => 'required|in:stay_in,daily_visit,wellness',
            'work_days' => 'nullable|required_if:service_category,stay_in|required_if:service_category,daily_visit|integer|min:1|max:90',
            'service_type' => 'required|string|max:100|unique:commission_rules,service_type',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'therapist_commission' => 'required|numeric|min:0',
            'therapist_commission_type' => 'required|in:fixed,percentage',
            'leader_override' => 'required|numeric|min:0',
            'leader_override_type' => 'required|in:fixed,percentage',
            'affiliate_commission' => 'nullable|numeric|min:0',
            'affiliate_commission_type' => 'required|in:fixed,percentage',
            'customer_referral_points' => 'nullable|integer|min:0',
            'points_per_job' => 'required|integer|min:0',
        ]);

        if ($validated['service_category'] === 'wellness') {
            $validated['work_days'] = null;
        }

        $validated['affiliate_commission'] = $validated['affiliate_commission'] ?? 0;
        $validated['customer_referral_points'] = $validated['customer_referral_points'] ?? 0;
        $validated['status'] = 'active';
        $validated['requires_review'] = $request->has('requires_review');
        CommissionRule::create($validated);

        return redirect()->route('hq.commission-rules.index')->with('success', 'Commission rule created successfully.');
    }

    public function edit(CommissionRule $commission_rule)
    {
        return view('hq.commission-rules.edit', ['rule' => $commission_rule]);
    }

    public function update(Request $request, CommissionRule $commission_rule)
    {
        $validated = $request->validate([
            'service_category' => 'required|in:stay_in,daily_visit,wellness',
            'work_days' => 'nullable|required_if:service_category,stay_in|required_if:service_category,daily_visit|integer|min:1|max:90',
            'service_type' => 'required|string|max:100|unique:commission_rules,service_type,' . $commission_rule->id,
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'therapist_commission' => 'required|numeric|min:0',
            'therapist_commission_type' => 'required|in:fixed,percentage',
            'leader_override' => 'required|numeric|min:0',
            'leader_override_type' => 'required|in:fixed,percentage',
            'affiliate_commission' => 'nullable|numeric|min:0',
            'affiliate_commission_type' => 'required|in:fixed,percentage',
            'customer_referral_points' => 'nullable|integer|min:0',
            'points_per_job' => 'required|integer|min:0',
        ]);

        if ($validated['service_category'] === 'wellness') {
            $validated['work_days'] = null;
        }

        $validated['affiliate_commission'] = $validated['affiliate_commission'] ?? 0;
        $validated['customer_referral_points'] = $validated['customer_referral_points'] ?? 0;
        $validated['requires_review'] = $request->has('requires_review');
        $commission_rule->update($validated);

        return redirect()->route('hq.commission-rules.index')->with('success', 'Commission rule updated successfully.');
    }

    public function toggleStatus(CommissionRule $commission_rule)
    {
        $commission_rule->status = $commission_rule->status === 'active' ? 'inactive' : 'active';
        $commission_rule->save();

        return back()->with('success', 'Commission rule status updated to ' . $commission_rule->status . '.');
    }
}
