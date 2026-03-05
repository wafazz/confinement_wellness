<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reviews = Review::with(['client', 'serviceJob', 'user']);

            if ($request->filled('filter_status')) {
                $reviews->where('status', $request->filter_status);
            }

            return DataTables::of($reviews)
                ->addColumn('client_name', fn($row) => $row->client->name ?? '-')
                ->addColumn('service_type', fn($row) => $row->serviceJob->service_type ?? '-')
                ->addColumn('staff_name', fn($row) => $row->user->name ?? '-')
                ->addColumn('staff_role', fn($row) => ucfirst($row->user->role ?? '-'))
                ->addColumn('stars', function ($row) {
                    $html = '';
                    for ($i = 1; $i <= 5; $i++) {
                        $html .= '<i class="fas fa-star ' . ($i <= $row->rating ? 'text-warning' : 'text-muted') . '"></i>';
                    }
                    return $html;
                })
                ->addColumn('comment_preview', fn($row) => $row->comment ? \Illuminate\Support\Str::limit($row->comment, 50) : '-')
                ->addColumn('status_badge', function ($row) {
                    $color = match($row->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '';
                    if ($row->status === 'pending') {
                        $html .= '<form action="' . route('hq.reviews.approve', $row->id) . '" method="POST" class="d-inline">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm(\'Approve this review?\')"><i class="fas fa-check"></i></button>'
                            . '</form> ';
                        $html .= '<form action="' . route('hq.reviews.reject', $row->id) . '" method="POST" class="d-inline">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" class="btn btn-sm btn-danger" title="Reject" onclick="return confirm(\'Reject this review?\')"><i class="fas fa-times"></i></button>'
                            . '</form>';
                    } else {
                        $html = '<span class="text-muted small">' . ucfirst($row->status) . '</span>';
                    }
                    return $html;
                })
                ->rawColumns(['stars', 'status_badge', 'action'])
                ->make(true);
        }

        return view('hq.reviews.index');
    }

    public function approve(Review $review)
    {
        $review->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Review approved successfully.');
    }

    public function reject(Review $review)
    {
        $review->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Review rejected.');
    }
}
