@extends('layouts.account_status')

@section('content')
    <x-status-card icon="hourglass-half" icon-bg-color="bg-warning">
        <x-slot name="title" class="text-dark">
            Your Account Is Almost Ready!
        </x-slot>

        <x-slot name="message">
            Thank you for creating your account. Our team is currently reviewing your details and will notify you once approved. In the meantime, you can start a free trial to get instant access.
        </x-slot>

        <x-slot name="actions">
            <a href="{{ route('trial.start') }}" class="btn btn-dark btn-lg-rounded">
                <i class="fa fa-play-circle me-2"></i> Start Your Free Trial
            </a>
            <a href="{{ route('support.form') }}" class="btn btn-outline-secondary btn-lg-rounded">
                <i class="fa fa-headset me-2"></i> Contact Support
            </a>
        </x-slot>

        <x-slot name="footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-muted">Log Out</button>
            </form>
        </x-slot>
    </x-status-card>
@endsection