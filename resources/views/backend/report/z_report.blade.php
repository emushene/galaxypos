@extends('backend.layout.main') @section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.Z Report')}}</h3>
            </div>
            <div class="card-body">
                {!! Form::open(['route' => 'report.zReport', 'method' => 'get']) !!}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="d-tc"><strong>{{trans('file.Choose Warehouse')}}</strong> &nbsp;</label>
                            <select id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins">
                                <option value="0">{{trans('file.All Warehouse')}}</option>
                                @foreach($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="d-tc"><strong>{{trans('file.Choose Cash Register')}}</strong> &nbsp;</label>
                            <select id="cash_register_id" name="cash_register_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" required>
                                <option value="">{{trans('file.Select Cash Register')}}</option>
                                @foreach($cash_registers as $register)
                                <option value="{{$register->id}}">
                                    {{ $register->user->name }} ({{ \Carbon\Carbon::parse($register->created_at)->format(config('date_format') . ' H:i') }} - {{ \Carbon\Carbon::parse($register->updated_at)->format(config('date_format') . ' H:i') }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit" style="margin-top: 30px;">{{trans('file.submit')}}</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    @if(isset($total_sales))
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Report Details for {{ $cash_register_data->user->name }}</h4>
                        <p>Date Range: {{ \Carbon\Carbon::parse($start_date)->format(config('date_format') . ' H:i') }} to {{ \Carbon\Carbon::parse($end_date)->format(config('date_format') . ' H:i') }}</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card-box p-3">
                                    <h5>Sales</h5>
                                    <p>Total Sales: <strong>{{ $total_sales }}</strong></p>
                                    <p>Total Amount: <strong>{{ number_format((float)$total_amount, 2, '.', '') }}</strong></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box p-3">
                                    <h5>Payments</h5>
                                    <p>Total Payments: <strong>{{ $total_payments }}</strong></p>
                                    <p>Cash: {{ number_format((float)$cash_payment, 2, '.', '') }}</p>
                                    <p>Card: {{ number_format((float)$card_payment, 2, '.', '') }}</p>
                                    <p>Cheque: {{ number_format((float)$cheque_payment, 2, '.', '') }}</p>
                                    <p>Gift Card: {{ number_format((float)$gift_card_payment, 2, '.', '') }}</p>
                                    <p>Paypal: {{ number_format((float)$paypal_payment, 2, '.', '') }}</p>
                                    <p>Deposit: {{ number_format((float)$deposit_payment, 2, '.', '') }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box p-3">
                                    <h5>Returns</h5>
                                    <p>Total Returns: <strong>{{ $total_returns }}</strong></p>
                                    <p>Total Return Amount: <strong>{{ number_format((float)$total_return_amount, 2, '.', '') }}</strong></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box p-3">
                                    <h5>Cash Register</h5>
                                    <p>Cash in Hand: <strong>{{ number_format((float)$cash_in_hand, 2, '.', '') }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #z-report-menu").addClass("active");

    var warehouse_id = {{ $warehouse_id ?? 0 }};
    var cash_register_id = {{ $cash_register_id ?? 0 }};

    if (warehouse_id > 0) {
        $('#warehouse_id').val(warehouse_id);
    }
    if (cash_register_id > 0) {
        $('#cash_register_id').val(cash_register_id);
    }
    $('.selectpicker').selectpicker('refresh');

    $('#warehouse_id').on('change', function() {
        var warehouse_id = $(this).val();
        var cashRegisterSelect = $('#cash_register_id');
        cashRegisterSelect.empty().append('<option value="">{{trans("file.Select Cash Register")}}</option>');

        if (warehouse_id > 0) {
            $.get('/report/get-cash-registers/' + warehouse_id, function(data) {
                $.each(data, function(key, value) {
                    cashRegisterSelect.append($('<option>', { value: value.id, text: value.text }));
                });
                $('.selectpicker').selectpicker('refresh');
            });
        } else {
            $('.selectpicker').selectpicker('refresh');
        }
    });

    $('#print-btn').on('click', function() {
        window.print();
    });
</script>
@endpush
<style>
    .card-box {
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    @media print {
        nav.side-navbar,
        header.header,
        footer.main-footer,
        .forms > .container-fluid:first-child,
        #print-btn {
            display: none;
        }
        .page {
            margin-left: 0;
            width: 100%;
            padding: 0;
        }
        #report-details .card {
            box-shadow: none;
            border: none;
        }
        #report-details {
            padding: 0;
        }
    }
</style>
