@extends('layouts.account_status')

@section('content')
    <x-status-card icon="ban" icon-bg-color="bg-danger">
        <x-slot name="title" class="text-danger">
            Account Suspended
        </x-slot>

        <x-slot name="message">
            Your account has been suspended due to billing or policy issues. Please contact our support team to resolve this.
        </x-slot>

        <x-slot name="actions">
            <a href="{{ route('support.form') }}" class="btn btn-outline-primary btn-lg-rounded">
                <i class="fa fa-headset"></i> Contact Support
            </a>
        </x-slot>

        <x-slot name="footer">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-danger fw-semibold">
                    <i class="fa fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </x-slot>
    </x-status-card>
@endsection
