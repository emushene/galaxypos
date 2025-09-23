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
    #preview-section {
        border: 1px solid #ccc;
        padding: 1rem;
        margin-top: 1rem;
        min-height: 300px;
        background-color: #f8f9fa;
        overflow: auto;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-content: flex-start;
    }

    .barcode-item {
        border: 1px dashed #999;
        text-align: center;
        box-sizing: border-box;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .barcode-item img {
        display: block;
        margin: 0 auto;
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
                                <div class="table-responsive">
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
                                                <td><input type="number" class="form-control qty" name="qty[]" value="1" min="1" /></td>
                                                <td><button type="button" class="ibtnDel btn btn-md btn-danger">Delete</button></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
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
                    <div class="card-header">
                        <h4>Barcode & Label Settings</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Label Width (mm) *</strong></label>
                                    <input type="number" class="form-control" id="label-width" value="60" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Label Height (mm) *</strong></label>
                                    <input type="number" class="form-control" id="label-height" value="40" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Font Size (pt) *</strong></label>
                                    <input type="number" class="form-control" id="font-size" value="10" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Paper Size *</strong></label>
                                    <select class="form-control" name="paper_size" required id="paper-size">
                                        <option value="a4">A4</option>
                                        <option value="letter">US Letter</option>
                                        <option value="roll">Roll Paper</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Barcode Height (px)</strong></label>
                                    <input type="number" class="form-control" id="barcode-height" value="30">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Barcode Width (%)</strong></label>
                                    <input type="number" class="form-control" id="barcode-width" value="80" min="10" max="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-4">
                                    <strong>Show on Label: </strong>&nbsp;
                                    <input type="checkbox" name="name" id="show-name" checked /> <label for="show-name" class="mr-2">{{trans('file.Product Name')}}</label>
                                    <input type="checkbox" name="price" id="show-price" checked/> <label for="show-price" class="mr-2">{{trans('file.Price')}}</label>
                                    <input type="checkbox" name="promo_price" id="show-promo-price"/> <label for="show-promo-price">{{trans('file.Promotional Price')}}</label>
                                </div>
                            </div>
                        </div>
                        <div id="roll-paper-info" class="alert alert-info" style="display: none;">
                            <strong>Note:</strong> For roll paper printing, ensure your printer settings are configured for your specific paper roll width.
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" id="print-button-main">
                                    <i class="dripicons-print"></i> {{trans('Print Barcodes')}}
                                </button>
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
                            <!-- Live preview will be rendered here -->
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

    function mmToPx(mm) {
        return mm * 3.7795275591;
    }

    function generateBarcodeHtml(products, settings) {
        let htmlText = '';
        
        if (products.length === 0) {
            return '<p class="text-center text-muted w-100">No products selected for preview.</p>';
        }

        const labelWidthPx = mmToPx(settings.width);
        const labelHeightPx = mmToPx(settings.height);

        products.forEach(product => {
            const qty = parseInt(product.qty) || 1;
            for (let i = 0; i < qty; i++) {
                let itemHtml = `<div class="barcode-item" style="width: ${labelWidthPx}px; height: ${labelHeightPx}px; font-size: ${settings.fontSize}pt;">`;

                if (settings.showName) {
                    itemHtml += `<div class="product-name">${product.name}</div>`;
                }

                itemHtml += `<img style="width: ${settings.barcodeWidth}%; height: ${settings.barcodeHeight}px;" src="data:image/png;base64,${product.barcodeImage}" alt="barcode" />`;
                
                itemHtml += `<div class="product-code">${product.code}</div>`;
                
                if (settings.showPromoPrice && product.promoPrice) {
                    itemHtml += `<div class="price">Price: R <span style="text-decoration: line-through;">${product.price}</span> ${product.promoPrice}</div>`;
                } else if (settings.showPrice) {
                    itemHtml += `<div class="price">Price: R ${product.price}</div>`;
                }
                itemHtml += '</div>';
                htmlText += itemHtml;
            }
        });

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

        var paperSize = $('#paper-size').val();
        var settings = {
            width: parseFloat($('#label-width').val()) || 60,
            height: parseFloat($('#label-height').val()) || 40,
            paperSize: paperSize,
            fontSize: parseFloat($('#font-size').val()) || 10,
            barcodeHeight: parseFloat($('#barcode-height').val()) || 30,
            barcodeWidth: parseFloat($('#barcode-width').val()) || 80,
            showName: $('#show-name').is(":checked"),
            showPrice: $('#show-price').is(":checked"),
            showPromoPrice: $('#show-promo-price').is(":checked")
        };

        var htmlContent = generateBarcodeHtml(products, settings);
        var $preview = $('#preview-section');
        $preview.html(htmlContent);

        if (paperSize === 'roll') {
            $preview.css({
                'flex-direction': 'column',
                'align-items': 'center'
            });
            $('#roll-paper-info').show();
        } else {
            $preview.css({
                'flex-direction': 'row',
                'align-items': 'flex-start'
            });
            $('#roll-paper-info').hide();
        }
    }

    $(document).ready(function() {
        updatePreview();

        // Event listeners
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
                                    <td><input type="number" class="form-control qty" name="qty[]" value="1" min="1" /></td>
                                    <td><button type="button" class="ibtnDel btn btn-md btn-danger">Delete</button></td>
                                </tr>
                            `);
                            $("table.order-list tbody").append(newRow);
                            updatePreview();
                        }
                        $("input[name='product_code_name']").val('');
                    }
                });
                return false;
            }
        });

        $("table.order-list tbody").on("input", ".qty", updatePreview);
        $("table.order-list tbody").on("click", ".ibtnDel", function() {
            $(this).closest("tr").remove();
            updatePreview();
        });

        $('#label-width, #label-height, #font-size, #paper-size, #barcode-height, #barcode-width').on('change input', updatePreview);
        $('input[type="checkbox"]').on('change', updatePreview);

        $("#clear-all-btn").on("click", function() {
            $("table.order-list tbody").empty();
            updatePreview();
        });

        $("#print-button-main").on("click", function() {
            const settings = {
                paperSize: $('#paper-size').val(),
            };

            let pageStyle = '';
            if (settings.paperSize === 'roll') {
                pageStyle = `@page { margin: 2mm; }`;
            } else {
                pageStyle = `@page { size: ${settings.paperSize}; margin: 10mm; }`;
            }

            let containerStyle = '';
            if (settings.paperSize === 'roll') {
                containerStyle = `
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 0;
                `;
            } else {
                containerStyle = `
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0;
                    justify-content: flex-start;
                    align-content: flex-start;
                    width: 100%;
                    height: 100%;
                    overflow: hidden;
                `;
            }

            let printContents = document.getElementById('preview-section').innerHTML;
            
            let printWindow = window.open('', 'Print-Window');
            printWindow.document.open();
            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Barcodes</title>
                    <style>
                        ${pageStyle}
                        body {
                            margin: 0;
                            font-family: sans-serif;
                        }
                        .barcode-container {
                           ${containerStyle}
                        }
                        .barcode-item {
                            border: none;
                            text-align: center;
                            box-sizing: border-box;
                            overflow: hidden;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            page-break-inside: avoid;
                        }
                        .barcode-item img {
                            display: block;
                            margin: 0 auto;
                        }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    <div class="barcode-container">${printContents}</div>
                </body>
                </html>
            `);
            printWindow.document.close();
        });

        $('#duplicate-alert .close').on('click', function() {
            $(this).closest('.alert').fadeOut();
        });
    });
</script>
@endpush