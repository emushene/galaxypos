@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="fw-bold mb-4 text-center">My Subscription</h1>

    {{-- ✅ Current Active Plan Card --}}
    @if($currentSubscription)
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">{{ $currentSubscription->plan->name }}</h4>
                    <p class="mb-0 text-muted">
                        ${{ number_format($currentSubscription->plan->price, 2) }} / {{ $currentSubscription->plan->interval }}
                    </p>
                    <small class="text-secondary">
                        Started: {{ $currentSubscription->starts_at ? $currentSubscription->starts_at->format('M d, Y') : '-' }}
                        | Ends: {{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('M d, Y') : 'Ongoing' }}
                    </small>
                </div>

                <div class="mt-3 mt-md-0">
                    @if($currentSubscription->status === 'active')
                        <form action="{{ route('subscriptions.cancel', $currentSubscription->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                Cancel Subscription
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center mb-5">
            You don’t have an active subscription.
            <a href="{{ route('pricing') }}" class="btn btn-sm btn-primary ms-2">Choose a Plan</a>
        </div>
    @endif

    {{-- ✅ Subscription History Table --}}
    <h3 class="fw-bold mb-3">Subscription History</h3>
    @if($subscriptions->isEmpty())
        <p class="text-muted">No subscription history available.</p>
    @else
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Started</th>
                        <th>Ends</th>
                        <th>Trial Ends</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $subscription)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $subscription->plan->name }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $subscription->status === 'active' ? 'success' : 
                                    ($subscription->status === 'trialing' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td>{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : '-' }}</td>
                            <td>{{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : '-' }}</td>
                            <td>{{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('M d, Y') : '-' }}</td>
                            <td>
                                @if($subscription->status === 'expired')
                                    <a href="{{ route('pricing') }}" class="btn btn-sm btn-primary">
                                        Renew
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
