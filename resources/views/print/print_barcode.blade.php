<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Label — {{ $stock->name }}</title>
    <style>
        @page {
            size:
                {{ $selectedSize['w'] }}
                mm
                {{ $selectedSize['h'] }}
                mm;
            margin: 2mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width:
                {{ $selectedSize['w'] }}
                mm;
            background: #fff;
            font-family: Arial, sans-serif;
        }

        /* Preview toolbar */
        .preview-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1a1a2e;
            color: #fff;
            padding: 10px 16px;
            font-size: 13px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .preview-bar strong {
            color: #e94560;
            font-size: 15px;
        }

        .preview-bar .btn-print {
            background: #e94560;
            color: #fff;
            border: none;
            padding: 8px 22px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap;
        }

        .preview-bar .btn-print:hover {
            background: #c73652;
        }

        .preview-info {
            font-size: 12px;
            color: #ccc;
            margin-top: 3px;
        }

        /* Label */
        .label-page {
            width:
                {{ $selectedSize['w'] }}
                mm;
            min-height:
                {{ $selectedSize['h'] }}
                mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2mm;
            page-break-after: always;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .label-page:last-child {
            page-break-after: avoid;
        }

        .label-product-name {
            font-size:
                {{ $selectedSize['h'] <= 30 ? '5pt' : '7pt' }}
            ;
            font-weight: bold;
            text-align: center;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 1mm;
            color: #000;
        }

        .label-barcode-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
            overflow: hidden;
            transform: rotate(180deg);
        }

        .label-barcode-wrap>div,
        .label-barcode-wrap svg {
            max-width: 100%;
        }

        .label-barcode-text {
            font-size:
                {{ $selectedSize['h'] <= 30 ? '4pt' : '6pt' }}
            ;
            text-align: center;
            margin-top: 1mm;
            color: #000;
            letter-spacing: 1px;
        }

        /* Empty barcode notice */
        .no-barcode-notice {
            background: #fff3cd;
            border: 2px dashed #ffc107;
            border-radius: 8px;
            padding: 30px 20px;
            text-align: center;
            margin: 20px auto;
            max-width: 500px;
        }

        .no-barcode-notice h4 {
            color: #856404;
            margin-bottom: 10px;
        }

        .no-barcode-notice p {
            color: #6c5700;
            font-size: 14px;
        }

        @media print {

            .preview-bar,
            .no-barcode-notice {
                display: none !important;
            }

            html,
            body {
                width:
                    {{ $selectedSize['w'] }}
                    mm;
                height: auto;
            }
        }
    </style>
</head>

<body>

    @if(!$isPreview)
        {{-- Preview toolbar shown only when opened in a full tab, not in the iframe --}}
        <div class="preview-bar">
            <div>
                <strong>🖨 Barcode Print Preview</strong><br>
                <span class="preview-info">
                    <b>Product:</b> {{ $stock->name }} &nbsp;|&nbsp;
                    <b>Size:</b> {{ $selectedSize['w'] }} × {{ $selectedSize['h'] }} mm &nbsp;|&nbsp;
                    <b>Copies:</b> {{ $copies }} &nbsp;|&nbsp;
                    <b>Type:</b> {{ strtoupper($type) }}
                </span>
            </div>
            <button class="btn-print" onclick="window.print()">🖨&nbsp; Print Now</button>
        </div>
    @endif

    @if(empty($barcodeValue))
        {{-- No barcode assigned — show a friendly notice instead of empty labels --}}
        <div class="no-barcode-notice">
            <h4>⚠ No Barcode Assigned</h4>
            <p>
                This product does not have a barcode value yet.<br>
                Please use the <strong>"Generate Code"</strong> or <strong>"Scan Code"</strong>
                buttons on the stock form before printing.
            </p>
        </div>
    @else
        @for ($i = 0; $i < $copies; $i++)
            <div class="label-page">
                <div class="label-product-name">{{ $stock->name }}</div>
                <div class="label-barcode-wrap">{!! $barcodeSvg !!}</div>
                <div class="label-barcode-text">{{ $barcodeValue }}</div>
            </div>
        @endfor
    @endif

    @if(!$isPreview && !empty($barcodeValue))
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () { window.print(); }, 600);
            });
        </script>
    @endif

</body>

</html>