@extends('backend.layout.main') @section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{'Create License'}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'license.store', 'method' => 'post', 'files' => true]) !!}
                        <div class="row">
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.name')}} *</strong> </label>
                                    <input type="text" name="name" required class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Company Name')}} *</label>
                                    <input type="text" name="company_name" required class="form-control">
                                    @if($errors->has('company_name'))
                                   <span>
                                       <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{'License Number'}} *</strong> </label>
                                    <div class="input-group">
                                        <input type="text" name="license_number" required id="license_number" aria-describedby="code" class="form-control" readonly>
                                        <div class="input-group-append">
                                            <button id="genbutton" type="button" class="btn btn-sm btn-default" title="{{trans('file.Generate')}}"><i class="fa fa-refresh"></i></button>
                                        </div>
                                        @if($errors->has('license_number'))
                                       <span>
                                           <strong>{{ $errors->first('license_number') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Email')}} *</label>
                                    <input type="email" name="email" placeholder="example@example.com" required class="form-control">
                                    @if($errors->has('email'))
                                   <span>
                                       <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Phone Number')}} *</label>
                                    <input type="text" name="phone_number" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{'Start Date'}} *</label>
                                    <input type="text" name="valid_start" id="valid_start" required class="form-control datetimepicker">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{'End Date'}} *</label>
                                    <input type="text" name="valid_end" id="valid_end" required class="form-control ">
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group mt-4">
                                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #license-menu").addClass("active");
    // $(".customer-group-section").hide();

    $('input[name="both"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('.customer-group-section').show(300);
            $('select[name="customer_group_id"]').prop('required',true);
        }
        else{
            $('.customer-group-section').hide(300);
            $('select[name="customer_group_id"]').prop('required',false);
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#genbutton').on("click", function(){
      $.get('gencode', function(data){
        $("input[name='license_number']").val(data);
      });
    });

    /*$(document).ready(function() {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            // Customize the format based on your needs
        });
    });
*/
    var valid_start = $('#valid_start');
        valid_start.datepicker({
        format: "yyyy-mm-dd",
        startDate: "<?php echo date('Y-m-d'); ?>",
        autoclose: true,
        todayHighlight: true
    });

    var valid_end = $('#valid_end');
        valid_end.datepicker({
        format: "yyyy-mm-dd",
        startDate: "<?php echo date('Y-m-d'); ?>",
        autoclose: true,
        todayHighlight: true
    });


</script>
@endpush
