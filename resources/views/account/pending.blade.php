@extends('layouts.app')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card p-5 text-center border-0 shadow-lg" style="max-width: 500px; border-radius: 1rem;">

        <div class="mb-4">
            <div class="text-muted d-inline-flex justify-content-center align-items-center" style="font-size: 3rem;">
                <i class="fa fa-hourglass-half"></i>
            </div>
        </div>

        <h2 class="text-dark fw-bold mb-3">Your Account Is Almost Ready!</h2>
        <p class="text-secondary mb-4">
            Thank you for creating your account. Our team is currently reviewing your details and will notify you as soon as everything is approved. This usually takes just a few business hours.
        </p>

        <p class="text-secondary fw-semibold mb-4">
            Don't want to wait? You can start exploring the platform right now by activating your free trial. This gives you instant access to all features and helps you get a head start.
        </p>

        <div class="d-grid gap-3">
            <a href="{{ route('trial.start') }}" class="btn btn-dark btn-lg rounded-pill shadow-sm">
                <i class="fa fa-play-circle me-2"></i> Start Your Free Trial Instantly
            </a>
            <a href="{{ route('support.form') }}" class="btn btn-outline-secondary btn-lg rounded-pill">
                <i class="fa fa-headset me-2"></i> Have Questions? Contact Support
            </a>
        </div>

        <div class="mt-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-muted">Log Out</button>
            </form>
        </div>
    </div>
</div>
@endsection