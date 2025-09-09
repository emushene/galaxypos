@extends('layouts.app')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card p-5 text-center bg-light border-0 shadow-sm" style="max-width: 500px; border-radius: 0.75rem;">

        <div class="mb-4">
            <div class="text-danger d-inline-flex justify-content-center align-items-center" style="font-size: 3.5rem;">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
        </div>

        <h2 class="text-dark fw-bold mb-3">Your Free Trial Has Expired</h2>
        <p class="text-secondary mb-4">
            It looks like your trial period has ended. We hope you enjoyed exploring all the features of our platform! To continue your work and maintain access to all of our tools, you'll need to choose a plan.
        </p>

        <p class="text-secondary fw-normal mb-4">
            Your data and account settings are safe. Simply subscribe to one of our plans, and you'll be able to pick up right where you left off.
        </p>

        <div class="d-grid gap-3">
            <a href="{{ url('/pricing') }}" class="btn btn-dark btn-lg rounded-pill shadow-sm">
                <i class="fa fa-credit-card me-2"></i> View Plans and Subscribe
            </a>

            <a href="{{ route('support.form') }}" class="btn btn-outline-secondary btn-lg rounded-pill">
                <i class="fa fa-headset me-2"></i> Contact Support
            </a>
        </div>

        <div class="mt-4">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-muted">Log Out</button>
            </form>
        </div>
    </div>
</div>
@endsection