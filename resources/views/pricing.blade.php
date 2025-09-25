@extends('layouts.app')

@section('content')
<div class="w-100 py-5">
    <h1 class="text-center fw-bold my-5">Choose Your POS Plan</h1>

    <div class="row justify-content-center">
        @foreach($plans as $plan)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-lg border-0 rounded-4">
                    <div class="card-body text-center py-4 px-3">
                        <h4 class="fw-bold mb-3">{{ $plan->name }}</h4>
                        <h2 class="mb-4 text-primary fw-bold">
                            ${{ number_format($plan->price, 2) }}
                            <small class="text-muted fs-6">/ {{ $plan->interval }}</small>
                        </h2>

                        <ul class="list-unstyled text-start mb-4 px-3">
                            <li><i class="fa fa-check text-success me-2"></i> {{ $plan->registers }} Register(s)</li>
                            <li><i class="fa fa-check text-success me-2"></i> {{ $plan->users }} Staff User(s)</li>
                            @if($plan->inventory_management)
                                <li><i class="fa fa-check text-success me-2"></i> Inventory Management</li>
                            @endif
                            @if($plan->reports)
                                <li><i class="fa fa-check text-success me-2"></i> Advanced Reports</li>
                            @endif
                        </ul>

                        <form action="{{ route('subscribe', $plan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">
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