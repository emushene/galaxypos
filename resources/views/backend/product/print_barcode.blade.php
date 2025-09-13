@extends('backend.layout.main')
@section('content')

@if(session()->has('not_permitted'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    {{ session()->get('not_permitted') }}
</div>
@endif

<style>
    .barcodelist {
        max-width: 378px;
        text-align: center;
    }

    .barcodelist img {
        max-width: 150px;
    }

    @media print {
        * {
            font-size: 12px;
            line-height: 20px;
        }
        td, th {
            padding: 5px 0;
        }
        .hidden-print {
            display: none !important;
        }
        @page {
            size: landscape;
            margin: 0 !important;
        }
        .barcodelist {
            max-width: 378px;
        }
        .barcodelist img {
            max-width: 150px;
        }
    }
    #preview-section {
        border: 1px solid #ccc;
        padding: 1rem;
        margin-top: 2rem;
        min-height: 200px;
        background-color: #f8f9fa;
        max-height: 500px;
        overflow-y: auto;
    }
</style>

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.print_barcode')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>

                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="duplicate-alert" style="display: none;">
                            <strong>Oops!</strong> This product has already been added.
                            <button type="button" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{trans('file.add_product')}} *</label>
                                        <div class="search-box input-group">
                                            <button type="button" class="btn btn-secondary btn-lg"><i class="fa fa-barcode"></i></button>
                                            <input type="text" name="product_code_name" id="lims_productcodeSearch" placeholder="Please type product code and select..." class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <button type="button" class="btn btn-warning" id="clear-all-btn">
                                            <i class="dripicons-trash"></i> {{trans('Clear All')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="table-responsive mt-3">
                                            <table id="myTable" class="table table-hover order-list">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('file.name')}}</th>
                                                        <th>{{trans('file.Code')}}</th>
                                                        <th>{{trans('file.Quantity')}}</th>
                                                        <th><i class="dripicons-trash"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($preLoadedproduct)
                                                    <tr data-imagedata="{{$preLoadedproduct[3]}}" data-price="{{$preLoadedproduct[2]}}" data-promo-price="{{$preLoadedproduct[4]}}" data-currency="{{$preLoadedproduct[5]}}" data-currency-position="{{$preLoadedproduct[6]}}">
                                                        <td>{{$preLoadedproduct[0]}}</td>
                                                        <td class="product-code">{{$preLoadedproduct[1]}}</td>
                                                        <td><input type="number" class="form-control qty" name="qty[]" value="1" /></td>
                                                        <td><button type="button" class="ibtnDel btn btn-md btn-danger">Delete</button></td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-2">
                                    <strong>{{trans('file.Print')}}: </strong>&nbsp;
                                    <strong><input type="checkbox" name="name" checked /> {{trans('file.Product Name')}}</strong>&nbsp;
                                    <strong><input type="checkbox" name="price" checked/> {{trans('file.Price')}}</strong>&nbsp;
                                    <strong><input type="checkbox" name="promo_price"/> {{trans('file.Promotional Price')}}</strong>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label><strong>Paper Size *</strong></label>
                                        <select class="form-control" name="paper_size" required id="paper-size">
                                            <option value="0">Select paper size...</option>
                                            <option value="36">36 mm (1.4 inch)</option>
                                            <option value="24">24 mm (0.94 inch)</option>
                                            <option value="18">18 mm (0.7 inch)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="button" class="btn btn-primary" id="print-button-main">
                                        <i class="dripicons-print"></i> {{trans('Print Barcodes')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>Live Barcode Preview</h4>
                    </div>
                    <div class="card-body">
                        <div id="preview-section">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#product").siblings('a').attr('aria-expanded', 'true');
    $("ul#product").addClass("show");
    $("ul#product #printBarcode-menu").addClass("active");

    var lims_product_code = [
        @foreach($lims_product_list_without_variant as $product)
        "{{ htmlspecialchars($product->code . ' (' . $product->name . ')') }}",
        @endforeach
        @foreach($lims_product_list_with_variant as $product)
        "{{ htmlspecialchars($product->item_code . ' (' . $product->name . ')') }}",
        @endforeach
    ];

    function generateBarcodeHtml(products, paperSize, printOptions) {
        let htmlText = '<table class="barcodelist" style="width:378px;" cellpadding="5px" cellspacing="10px">';
        
        if (products.length === 0) {
            return '<p class="text-center text-muted">No products selected for preview.</p>';
        }

        products.forEach(product => {
            const qty = parseInt(product.qty) || 1;
            for (let i = 0; i < qty; i++) {
                if (i % 2 === 0) {
                    htmlText += '<tr>';
                }
                
                let tdStyle = '';
                if (paperSize == 36) {
                    tdStyle = 'width:164px;height:88%;padding-top:7px;vertical-align:middle;text-align:center';
                } else if (paperSize == 24) {
                    tdStyle = 'width:164px;height:100%;font-size:12px;text-align:center';
                } else if (paperSize == 18) {
                    tdStyle = 'width:164px;height:100%;font-size:10px;text-align:center';
                }
                htmlText += `<td style="${tdStyle}">`;

                if (printOptions.name) {
                    htmlText += product.name + '<br>';
                }

                htmlText += `<img style="max-width:150px;" src="data:image/png;base64,${product.barcodeImage}" alt="barcode" /><br>`;
                
                htmlText += product.code + '<br>';
                
                if (printOptions.promoPrice && product.promoPrice) {
                    htmlText += `Price: R<span style="text-decoration: line-through;"> ${product.price}</span> ${product.promoPrice}<br>`;
                } else if (printOptions.price) {
                    htmlText += `Price: R ${product.price}`;
                }
                htmlText += '</td>';

                if (i % 2 !== 0) {
                    htmlText += '</tr>';
                }
            }
        });

        htmlText += '</table>';
        return htmlText;
    }

    function updatePreview() {
        var products = [];
        $('table.order-list tbody tr').each(function() {
            var row = $(this);
            products.push({
                name: row.find('td:nth-child(1)').text(),
                code: row.find('td:nth-child(2)').text(),
                qty: row.find('.qty').val(),
                price: row.data('price'),
                promoPrice: row.data('promo-price'),
                barcodeImage: row.data('imagedata'),
                currency: row.data('currency'),
                currencyPosition: row.data('currency-position')
            });
        });

        var paper_size = $("#paper-size").val();
        var printOptions = {
            name: $('input[name="name"]').is(":checked"),
            price: $('input[name="price"]').is(":checked"),
            promoPrice: $('input[name="promo_price"]').is(":checked")
        };

        var htmlContent = generateBarcodeHtml(products, paper_size, printOptions);
        $('#preview-section').html(htmlContent);
    }

    $(document).ready(function() {
        updatePreview();
    });

    $('#lims_productcodeSearch').autocomplete({
        source: function(request, response) {
            var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegex(request.term), "i");
            response($.grep(lims_product_code, function(item) {
                return matcher.test(item);
            }));
        },
        select: function(event, ui) {
            var data = ui.item.value;
            $.ajax({
                type: 'GET',
                url: 'lims_product_search',
                data: { data: data },
                success: function(data) {
                    var isDuplicate = false;
                    $(".product-code").each(function() {
                        if ($(this).text() == data[1]) {
                            isDuplicate = true;
                            $('#duplicate-alert').fadeIn().delay(3000).fadeOut();
                            return false;
                        }
                    });

                    if (!isDuplicate) {
                        var newRow = $(`
                            <tr data-imagedata="${data[3]}" data-price="${data[2]}" data-promo-price="${data[4]}" data-currency="${data[5]}" data-currency-position="${data[6]}">
                                <td>${data[0]}</td>
                                <td class="product-code">${data[1]}</td>
                                <td><input type="number" class="form-control qty" name="qty[]" value="1" /></td>
                                <td><button type="button" class="ibtnDel btn btn-md btn-danger">Delete</button></td>
                            </tr>
                        `);
                        $("table.order-list tbody").append(newRow);
                        updatePreview();
                    }
                    $("input[name='product_code_name']").val('');
                }
            });
        }
    });

    $("table.order-list tbody").on("input", ".qty", updatePreview);
    $("table.order-list tbody").on("click", ".ibtnDel", function() {
        $(this).closest("tr").remove();
        updatePreview();
    });
    $('input[type="checkbox"][name="name"], input[type="checkbox"][name="price"], input[type="checkbox"][name="promo_price"]').on('change', updatePreview);
    $('#paper-size').on('change', updatePreview);
    $("#clear-all-btn").on("click", function() {
        $("table.order-list tbody").empty();
        updatePreview();
    });

    $("#print-button-main").on("click", function() {
        var paper_size = $("#paper-size").val();
        if (paper_size === "0") {
            $('#duplicate-alert').text('Please select a paper size.').fadeIn().delay(3000).fadeOut();
            return;
        }
        
        var divToPrint = document.getElementById('preview-section');
        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write('<style type="text/css">@media print { #modal_header, #print-btn, #close-btn { display: none; } } table.barcodelist { page-break-inside: auto; } table.barcodelist tr { page-break-inside: avoid; page-break-after: auto; }</style><body onload="window.print()">' + divToPrint.innerHTML + '</body>');
        newWin.document.close();
        setTimeout(function() { newWin.close(); }, 10);
    });

    $('#duplicate-alert .close').on('click', function() {
        $(this).closest('.alert').fadeOut();
    });
</script>
@endpush