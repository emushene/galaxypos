@extends('layouts.account_status')

@section('content')
<div class="status-card">
    
    <!-- Icon -->
    <div class="status-icon bg-secondary">
        <i class="fa fa-user-slash"></i>
    </div>

    <!-- Title / Message -->
    <h2 class="status-title text-secondary">Account Inactive</h2>
    <p class="status-message">
        Your account is currently inactive. This may be due to non-payment, expiry, or administrative action.
    </p>

    <!-- Primary Actions -->
    <div class="d-grid gap-3">
        <a href="{{ url('/pricing') }}" class="btn btn-success btn-lg-rounded">
            <i class="fa fa-redo"></i> Reactivate Account
        </a>

        <a href="{{ route('support.form') }}" class="btn btn-outline-primary btn-lg-rounded">
            <i class="fa fa-headset"></i> Contact Support
        </a>
    </div>

    <!-- Secondary Action -->
    <div class="text-center mt-4">
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-link text-danger fw-semibold">
                <i class="fa fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</div>
@endsection
