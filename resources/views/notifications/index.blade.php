@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@push('styles')
<style>
    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .notif-item:hover { background: #f8fafc; }
    .notif-item:last-child { border-bottom: none; }
    .notif-item.unread { background: #fdf6f0; border-left: 3px solid #c8956c; }
    .notif-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.9rem;
    }
    .notif-icon.job { background: #dbeafe; color: #2563eb; }
    .notif-icon.commission { background: #dcfce7; color: #16a34a; }
    .notif-icon.therapist { background: #fef3c7; color: #d97706; }
    .notif-icon.default { background: #f1f5f9; color: #64748b; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0">Notifications</h5>
        @if($unreadCount > 0)
            <small class="text-muted">{{ $unreadCount }} unread</small>
        @endif
    </div>
    @if($unreadCount > 0)
    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
        @csrf
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-check-double me-1"></i> Mark All as Read</button>
    </form>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($notifications->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fas fa-bell fa-3x mb-3 opacity-50"></i>
                <p class="mb-0">No notifications yet.</p>
            </div>
        @else
            @foreach($notifications as $notif)
            @php
                $type = $notif->data['type'] ?? 'default';
                $iconClass = match($type) {
                    'job_assigned' => 'job',
                    'job_completed' => 'job',
                    'commission_approved', 'commission_paid' => 'commission',
                    'therapist_registered' => 'therapist',
                    default => 'default',
                };
                $icon = match($type) {
                    'job_assigned' => 'fa-briefcase',
                    'job_completed' => 'fa-check-circle',
                    'commission_approved' => 'fa-check',
                    'commission_paid' => 'fa-money-bill-wave',
                    'therapist_registered' => 'fa-user-plus',
                    default => 'fa-bell',
                };
            @endphp
            <div class="notif-item {{ $notif->read_at ? '' : 'unread' }}">
                <div class="notif-icon {{ $iconClass }}">
                    <i class="fas {{ $icon }}"></i>
                </div>
                <div class="flex-grow-1">
                    <strong style="font-size:0.9rem;">{{ $notif->data['title'] ?? 'Notification' }}</strong>
                    <p class="mb-1" style="font-size:0.85rem; color:#475569;">{{ $notif->data['message'] ?? '' }}</p>
                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                </div>
                <div class="d-flex gap-1">
                    @if(!$notif->read_at)
                    <form method="POST" action="{{ route('notifications.mark-read', $notif->id) }}">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm btn-outline-secondary" title="Mark as read"><i class="fas fa-check"></i></button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}" onsubmit="return confirm('Delete?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
            <div class="p-3">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>
@endsection
