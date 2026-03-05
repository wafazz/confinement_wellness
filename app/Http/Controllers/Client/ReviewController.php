<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ServiceJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Auth::guard('client')->user()
            ->reviews()
            ->with(['serviceJob', 'user'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('client.reviews.index', compact('reviews'));
    }

    public function create(ServiceJob $job)
    {
        $client = Auth::guard('client')->user();

        if ($job->client_id !== $client->id) {
            abort(403);
        }

        if ($job->status !== 'completed') {
            return redirect()->route('client.bookings.index')
                ->with('error', __('client.review_job_not_completed'));
        }

        if ($job->review) {
            return redirect()->route('client.reviews.index')
                ->with('error', __('client.review_already_submitted'));
        }

        $job->load('assignee');

        return view('client.reviews.create', compact('job'));
    }

    public function store(Request $request, ServiceJob $job)
    {
        $client = Auth::guard('client')->user();

        if ($job->client_id !== $client->id) {
            abort(403);
        }

        if ($job->status !== 'completed') {
            return redirect()->route('client.bookings.index')
                ->with('error', __('client.review_job_not_completed'));
        }

        if ($job->review) {
            return redirect()->route('client.reviews.index')
                ->with('error', __('client.review_already_submitted'));
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'client_id' => $client->id,
            'service_job_id' => $job->id,
            'user_id' => $job->assigned_to,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending',
        ]);

        return redirect()->route('client.reviews.index')
            ->with('success', __('client.review_success'));
    }
}
