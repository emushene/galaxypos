@extends('layouts.account_status')

@section('content')
    <x-status-card icon="exclamation-triangle" icon-bg-color="bg-danger">
        <x-slot name="title" class="text-dark">
            Your Free Trial Has Expired
        </x-slot>

        <x-slot name="message">
            Your trial period has ended, but your data is safe. To continue using our platform, please choose a subscription plan.
        </x-slot>

        <x-slot name="actions">
            <a href="{{ url('/pricing') }}" class="btn btn-dark btn-lg-rounded">
                <i class="fa fa-credit-card me-2"></i> View Plans and Subscribe
            </a>
            <a href="{{ route('support.form') }}" class="btn btn-outline-secondary btn-lg-rounded">
                <i class="fa fa-headset me-2"></i> Contact Support
            </a>
        </x-slot>

        <x-slot name="footer">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-muted">Log Out</button>
            </form>
        </x-slot>
    </x-status-card>
@endsection