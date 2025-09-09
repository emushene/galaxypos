@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg border-0 rounded-3 p-4 text-center" style="max-width: 500px; width: 100%;">
        
        <!-- Icon -->
        <div class="mb-3">
            <div class="bg-secondary text-white rounded-circle d-inline-flex justify-content-center align-items-center shadow"
                 style="width:80px; height:80px; font-size:2rem;">
                <i class="fa fa-user-slash"></i>
            </div>
        </div>

        <!-- Title / Message -->
        <h2 class="text-secondary fw-bold mb-3">Account Inactive</h2>
        <p class="text-muted mb-2">
            Your account is currently inactive.
        </p>
        <p class="text-muted mb-4">
            This may be due to non-payment, expiry, or administrative action.
        </p>

        <!-- Primary Actions -->
        <div class="d-grid gap-3">
            <a href="{{ url('/reactivate') }}" class="btn btn-success btn-lg rounded-3 shadow-sm">
                <i class="fa fa-redo"></i> Reactivate Account
            </a>

            <a href="{{ route('support.form') }}" class="btn btn-outline-primary btn-lg rounded-3 shadow-sm">
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
</div>
@endsection
