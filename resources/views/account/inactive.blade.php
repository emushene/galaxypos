@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h1 class="text-secondary">🔒 Account Inactive</h1>
    <p>Your account is currently inactive.</p>
    <p>This may be due to non-payment, expiry, or administrative action.</p>
    <a href="{{ url('/reactivate') }}" class="btn btn-success mt-3">Reactivate Account</a>
</div>
@endsection
