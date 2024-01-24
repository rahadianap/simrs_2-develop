<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <link rel="stylesheet" href="{{url('css/report/style.css')}}">
    <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
    <title>component barcode generator</title>
</head>
<body>
    <div class="uk-flex-center uk-align-center" style="margin-top:35px;" v-for="index in 2" :key="index" v-if="barCode">
        <div class="uk-margin-small-top">
            <svg class="remove-margin-bottom uk-align-center"
                id="barcodeGenerator" 
                jsbarcode-format="CODE128"
                jsbarcode-width=5
                jsbarcode-height=150
                jsbarcode-displayValue=false
            >
            </svg>
        </div>
        <div>
            <h4 class="value-text uk-margin-remove" contenteditable @input="updateValue">RM-000042123532</h4>
        </div>
    </div>
</body>
</html>
<script>
        // Fungsi untuk menginisialisasi barcode generator
        function initializeBarcodeGenerator() {
            var valueText = document.querySelector('.value-text');
            var value = valueText.innerText;

            JsBarcode("#barcodeGenerator", value, {
                format: "code128",
                width: 5,
                height: 150,
                displayValue: false
            });
            
            // Untuk mengubah nilai barcode saat input diubah
            valueText.addEventListener('input', function() {
                var updatedValue = valueText.innerText;
                JsBarcode("#barcodeGenerator", updatedValue, {
                    format: "code128",
                    width:5,
                    height: 150,
                    displayValue: false
                });
            });
        }

        // Jalankan fungsi initializeBarcodeGenerator saat halaman selesai dimuat
        document.addEventListener("DOMContentLoaded", function(event) {
            initializeBarcodeGenerator();
        });
    </script>
    <style>
        .value-text {
            font-family: monospace;
            font-size: 25px;
            letter-spacing: 0.2cm;
            text-align:center;
            margin: 1px 0 30px 0px;
            position: relative;
        }
        .remove-margin-bottom {
            margin-bottom: 0 !important;
        }
    </style>