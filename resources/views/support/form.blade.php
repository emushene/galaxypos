@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 650px; width: 100%;">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center shadow" 
                 style="width:80px; height:80px; font-size:2rem;">
                <i class="fa fa-headset"></i>
            </div>
            <h3 class="mt-3 fw-bold text-primary">Contact Support</h3>
            <p class="text-muted">Need help? Fill out the form below and our support team will get back to you.</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success text-center rounded-3">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Support Form -->
        <form action="{{ route('support.send') }}" method="POST" class="needs-validation" novalidate>
            @csrf

            @guest
                <div class="mb-3">
                    <label class="form-label fw-semibold">Your Email</label>
                    <input type="email" name="email" class="form-control form-control-lg rounded-3" value="{{ old('email') }}" required>
                </div>
            @endguest

            <div class="mb-3">
                <label class="form-label fw-semibold">Subject</label>
                <input type="text" name="subject" class="form-control form-control-lg rounded-3" value="{{ old('subject') }}" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Message</label>
                <textarea name="message" class="form-control form-control-lg rounded-3" rows="5" required>{{ old('message') }}</textarea>
            </div>

            <!-- Main Action -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg rounded-3 shadow-sm">
                    <i class="fa fa-paper-plane"></i> Send Message
                </button>
            </div>
        </form>

        <!-- Secondary Action (Logout) -->
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
