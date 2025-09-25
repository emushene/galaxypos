@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Your Subscriptions</h1>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($subscriptions->isEmpty())
        <p>You don't have any subscriptions yet.</p>
        <a href="{{ url('/pricing') }}" class="btn btn-primary">Browse Plans</a>
    @else
        <div class="row">
            @foreach ($subscriptions as $subscription)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $subscription->plan->name }}</h5>
                            <p class="card-text">Status: <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($subscription->status) }}</span></p>
                            <p class="card-text">Interval: {{ ucfirst($subscription->plan->interval) }}</p>
                            <p class="card-text">Starts: {{ $subscription->starts_at->format('M d, Y') }}</p>
                            <p class="card-text">Ends: {{ $subscription->ends_at->format('M d, Y') }}</p>
                            @if ($subscription->status === 'active')
                                <form action="{{ route('subscriptions.cancel', $subscription->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this subscription?');">Cancel Subscription</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection