@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h1 class="text-danger">⚠️ Trial Expired</h1>
    <p>Your free trial has ended. To continue using the system, please subscribe to a plan.</p>
    <a href="{{ url('/pricing') }}" class="btn btn-primary mt-3">View Pricing</a>
</div>
@endsection
