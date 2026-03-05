@extends('layouts.app')

@section('title', 'Edit Reward Tier')
@section('page-title', 'Edit Reward Tier')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('hq.reward-tiers.update', $tier) }}" method="POST">
            @csrf
            @method('PUT')
            @include('hq.reward-tiers._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update</button>
                <a href="{{ route('hq.reward-tiers.index') }}" class="btn btn-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
