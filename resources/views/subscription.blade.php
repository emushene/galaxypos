@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="text-center fw-bold mb-5">Choose Your POS Plan</h1>

    <div class="row justify-content-center">
        @foreach($plans as $plan)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-3">
                    <div class="card-body text-center p-4">
                        <h4 class="fw-bold">{{ $plan->name }}</h4>
                        <h2 class="my-3 text-primary fw-bold">
                            ${{ number_format($plan->price, 2) }}
                            <small class="text-muted fs-6">/ {{ $plan->interval }}</small>
                        </h2>

                        <ul class="list-unstyled text-start mb-4">
                            <li><i class="fa fa-check text-success"></i> {{ $plan->registers }} Register(s)</li>
                            <li><i class="fa fa-check text-success"></i> {{ $plan->users }} Staff User(s)</li>
                            @if($plan->inventory_management)
                                <li><i class="fa fa-check text-success"></i> Inventory Management</li>
                            @endif
                            @if($plan->reports)
                                <li><i class="fa fa-check text-success"></i> Advanced Reports</li>
                            @endif
                        </ul>

                        <form action="{{ route('subscribe', $plan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                Subscribe Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
