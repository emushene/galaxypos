@extends('backend.layout.main')
@section('content')

@if(session()->has('message'))
<div class="alert alert-success alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    {{ session()->get('message') }}
</div>
@endif

@if(session()->has('not_permitted'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    {{ session()->get('not_permitted') }}
</div>
@endif

<section>
    <div class="container-fluid mb-2">
        <button class="btn btn-info" data-toggle="modal" data-target="#createModal">
            <i class="dripicons-plus"></i> {{trans('file.Count Stock')}}
        </button>
    </div>

    <div class="table-responsive">
        <table id="stock-count-table" class="table stock-count-list">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>{{trans('file.Warehouse')}}</th>
                    <th>{{trans('file.category')}}</th>
                    <th>{{trans('file.Brand')}}</th>
                    <th>{{trans('file.Type')}}</th>
                    <th class="not-exported">{{trans('file.Initial File')}}</th>
                    <th class="not-exported">{{trans('file.Final File')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_stock_count_all as $key => $stock_count)
                @php
                    $warehouse = DB::table('warehouses')->find($stock_count->warehouse_id);
                    $category_name = [];
                    $brand_name = [];
                @endphp
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{ date($general_setting->date_format, strtotime($stock_count->created_at->toDateString())) . ' '. $stock_count->created_at->toTimeString() }}</td>
                    <td>{{ $stock_count->reference_no }}</td>
                    <td>{{ $warehouse->name ?? '' }}</td>
                    <td>
                        @if($stock_count->category_id)
                        @foreach(explode(",",$stock_count->category_id) as $cat_key=>$category_id)
                            @php $category = \DB::table('categories')->find($category_id); $category_name[] = $category->name ?? ''; @endphp
                            @if($cat_key){{', ' . ($category->name ?? '')}}@else{{$category->name ?? ''}}@endif
                        @endforeach
                        @endif
                    </td>
                    <td>
                        @if($stock_count->brand_id)
                        @foreach(explode(",",$stock_count->brand_id) as $brand_key=>$brand_id)
                            @php $brand = \DB::table('brands')->find($brand_id); $brand_name[] = $brand->title ?? ''; @endphp
                            @if($brand_key){{', '. ($brand->title ?? '')}}@else{{$brand->title ?? ''}}@endif
                        @endforeach
                        @endif
                    </td>
                    <td>
                        <div class="badge badge-{{ $stock_count->type=='full'?'primary':'info' }}">
                            {{ $stock_count->type=='full'?trans('file.Full'):trans('file.Partial') }}
                        </div>
                    </td>
                    <td class="text-center">
                        <a download href="{{asset('public/stock_count/'.$stock_count->initial_file)}}" title="{{trans('file.Download')}}">
                            <i class="dripicons-copy"></i>
                        </a>
                    </td>
                    <td class="text-center">
                        @if($stock_count->final_file)
                        <a download href="{{asset('public/stock_count/'.$stock_count->final_file)}}" title="{{trans('file.Download')}}">
                            <i class="dripicons-copy"></i>
                        </a>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-warning adjust-stock-btn" data-id="{{$stock_count->id}}" data-warehouse="{{$stock_count->warehouse_id}}">Adjust</button>
                        <button class="btn btn-danger delete-stock" data-id="{{$stock_count->id}}">Delete</button>
                        @if($stock_count->final_file)
                            <span class="badge badge-success final-report" data-id="{{$stock_count->id}}">{{trans('file.Final Report')}}</span>
                        @else
                            <span class="badge badge-primary finalize" data-id="{{$stock_count->id}}">{{trans('file.Finalize')}}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $lims_stock_count_all->links() }}
        </div>

    </div>
</section>

<!-- Create Stock Modal -->
<div id="createModal" class="modal fade text-left" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['route' => 'stock-count.store', 'method' => 'post', 'files' => true]) !!}
            <div class="modal-header">
                <h5 class="modal-title">{{trans('file.Count Stock')}}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Warehouse')}} *</label>
                        <select required name="warehouse_id" class="selectpicker form-control" data-live-search="true" title="Select warehouse...">
                            @foreach($lims_warehouse_list as $warehouse)
                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Type')}} *</label>
                        <select class="form-control" name="type">
                            <option value="full">{{trans('file.Full')}}</option>
                            <option value="partial">{{trans('file.Partial')}}</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group" id="category-div">
                        <label>{{trans('file.category')}}</label>
                        <select name="category_id[]" class="selectpicker form-control" data-live-search="true" multiple>
                            @foreach($lims_category_list as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group" id="brand-div">
                        <label>{{trans('file.Brand')}}</label>
                        <select name="brand_id[]" class="selectpicker form-control" data-live-search="true" multiple>
                            @foreach($lims_brand_list as $brand)
                            <option value="{{$brand->id}}">{{$brand->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary" value="{{trans('file.submit')}}">
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Finalize Modal -->
<div id="finalizeModal" class="modal fade text-left" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(['route' => 'stock-count.finalize', 'method' => 'POST', 'files'=>true]) }}
            <div class="modal-header">
                <h5 class="modal-title">{{trans('file.Finalize Stock Count')}}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>{{trans('file.Upload File')}} *</label>
                    <input type="file" name="final_file" class="form-control" required>
                </div>
                <input type="hidden" name="stock_count_id">
                <div class="form-group">
                    <label>{{trans('file.Note')}}</label>
                    <textarea name="note" rows="3" class="form-control"></textarea>
                </div>
                <input type="submit" class="btn btn-primary" value="{{trans('file.submit')}}">
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div id="adjustStockModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="adjust-stock-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Expected</th>
                            <th>Counted</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-stock-adjustments">Save Changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Show/hide category & brand for partial
    $('#category-div, #brand-div').hide();
    $('select[name=type]').on('change', function() {
        if ($(this).val() == 'partial') {
            $('#category-div, #brand-div').show(300);
        } else {
            $('#category-div, #brand-div').hide(300);
        }
    });

    // Finalize modal
    $(document).on('click', '.finalize', function() {
        $('input[name="stock_count_id"]').val($(this).data('id'));
        $('#finalizeModal').modal('show');
    });

    // Delete stock count
    $(document).on('click', '.delete-stock', function() {
        if (!confirm('Are you sure?')) return;
        let id = $(this).data('id');
        $.post('/stock-count/' + id + '/delete', {
            _token: '{{csrf_token()}}'
        }, function(res) {
            alert(res.success);
            location.reload();
        });
    });

    // DataTable with export buttons
    $('#stock-count-table').DataTable({
        order: [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
            "info": '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search": '{{trans("file.Search")}}',
            'paginate': {
                'previous': '<i class="dripicons-chevron-left"></i>',
                'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [{
                "orderable": false,
                'targets': [0, 7, 8, 9]
            },
            {
                'checkboxes': {
                    'selectRow': true,
                    'selectAllRender': '<div class="checkbox"><input type="checkbox"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': {
            style: 'multi',
            selector: 'td:first-child'
        },
        'lengthMenu': [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        dom: '<"row"lfB>rtip',
        buttons: [{
                extend: 'pdf',
                exportOptions: { columns: ':visible:Not(.not-exported)', rows: ':visible' }
            },
            {
                extend: 'excel',
                exportOptions: { columns: ':visible:Not(.not-exported)', rows: ':visible' }
            },
            {
                extend: 'csv',
                exportOptions: { columns: ':visible:Not(.not-exported)', rows: ':visible' }
            },
            {
                extend: 'print',
                exportOptions: { columns: ':visible:Not(.not-exported)', rows: ':visible' }
            },
            {
                extend: 'colvis',
                columns: ':gt(0)'
            }
        ],
    });

    // Adjust Stock modal
    $(document).on('click', '.adjust-stock-btn', function() {
        var stockCountId = $(this).data('id');
        var warehouseId = $(this).data('warehouse');
        $('#adjust-stock-table tbody').html('');
        $('#save-stock-adjustments').data('stock-id', stockCountId);

        $.get('/internal-stock-count/' + warehouseId, function(products) {
            if(products.length === 0){
                $('#adjust-stock-table tbody').html('<tr><td colspan="4" class="text-center">No products found</td></tr>');
                $('#adjustStockModal').modal('show');
                return;
            }
            $.each(products, function(index, product) {
                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + product.name + '</td>' +
                    '<td>' + product.expected + '</td>' +
                    '<td><input type="number" class="form-control counted-qty" data-code="' + product.code + '" value="' + product.expected + '"></td>' +
                    '</tr>';
                $('#adjust-stock-table tbody').append(row);
            });
            $('#adjustStockModal').modal('show');
        });
    });

    // Save adjustments
    $('#save-stock-adjustments').on('click', function() {
        var stockCountId = $(this).data('stock-id');
        var counted_qty = {};
        $('#adjust-stock-table tbody tr').each(function() {
            var code = $(this).find('.counted-qty').data('code');
            var qty = $(this).find('.counted-qty').val();
            counted_qty[code] = qty;
        });
        $.ajax({
            url: '/stock-count/update/' + stockCountId,
            method: 'POST',
            data: {
                counted_qty: counted_qty,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert(response.success);
                $('#adjustStockModal').modal('hide');
                location.reload();
            },
            error: function(xhr){
                alert('Something went wrong!');
                console.log(xhr.responseText);
            }
        });
    });

});
</script>
@endpush
