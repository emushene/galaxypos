@extends('backend.layout.main') @section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
@endif
<section>
    <div class="container-fluid">
        @if(in_array("license", $all_permission))
        <a href="{{route('license.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{'Add License'}}</a>
        @endif
    </div>
    <div class="table-responsive">
        <table id="license-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{'Client Details'}}</th>
                    <th>{{'License Number'}}</th>
                    <th>{{'Valid From'}}</th>
                    <th>{{'Valid Till'}}</th>
                    <th>{{'License Status'}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_license_all as $key => $license)
                <tr data-id="{{$license->id}}">
                    <td>{{$key}}</td>
                    <td>
                        {{'Name: '.$license->name}}
                        <br>{{'Company Name: '.$license->company_name}}
                        <br>{{'Email: '.$license->email}}
                        <br>{{'Number: '.$license->phone_number}}
                    </td>
                    <td>{{$license->license_number}}</td>
                    <td>{{$license->valid_start}}</td>
                    <td>{{$license->valid_end}}</td>
                    <td>
                        <div class="btn-group">
                            @if($license->is_active)
                                <button type="button" class="btn btn-default btn-lg" aria-haspopup="true" aria-expanded="false"> {{'Active'}} </button>
                            @else
                                <button type="button" class="btn btn-default btn-lg" aria-haspopup="true" aria-expanded="false"> {{'In-Active'}} </button>
                            @endif
                        </div>
                    </td>
                    
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                @if(in_array("license", $all_permission))
                                <li>
                                	<a href="{{ route('license.edit', $license->id) }}" class="btn btn-link"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</a>
                                </li>
                                @endif
                                
                                <li class="divider"></li>
                                @if(in_array("license", $all_permission))
                                {{ Form::open(['route' => ['license.destroy', $license->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{'In-Active'}}</button>
                                </li>
                                {{ Form::close() }}
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>



@endsection

@push('scripts')
<script type="text/javascript">

    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #license-menu").addClass("active");

    var all_permission = <?php echo json_encode($all_permission) ?>;
    var license_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	function confirmDelete() {
	    if (confirm("Are you sure want to delete?")) {
	        return true;
	    }
	    return false;
	}

    $('#license-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 2, 3]
            },
            {
                'checkboxes': {
                   'selectRow': true
                },
                'targets': 0
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        // dom: '<"row"lfB>rtip',
    } );

    if(all_permission.indexOf("license") == -1)
        $('.buttons-delete').addClass('d-none');

</script>
@endpush
