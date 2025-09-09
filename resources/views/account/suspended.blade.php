@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg border-0 rounded-3 p-4 text-center" style="max-width: 500px; width: 100%;">
        
        <!-- Icon -->
        <div class="mb-3">
            <div class="bg-danger text-white rounded-circle d-inline-flex justify-content-center align-items-center shadow"
                 style="width:80px; height:80px; font-size:2rem;">
                <i class="fa fa-ban"></i>
            </div>
        </div>

        <!-- Title / Message -->
        <h2 class="text-danger fw-bold mb-3">Account Suspended</h2>
        <p class="text-muted mb-2">
            Your account has been suspended due to billing or policy issues.
        </p>
        <p class="text-muted mb-4">
            Please contact our support team to resolve this.
        </p>

        <!-- Primary Action -->
        <div class="d-grid">
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
