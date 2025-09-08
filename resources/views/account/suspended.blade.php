@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h1 class="text-danger">🚫 Account Suspended</h1>
    <p>Your account has been suspended due to billing or policy issues.</p>
    <p>Please contact our support team to resolve this.</p>
    <a href="{{ url('/support') }}" class="btn btn-outline-danger mt-3">Contact Support</a>
</div>
@endsection
