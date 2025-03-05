<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/weather-icons/css/weather-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/themify-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/main.css') }}">
    <script src="{{ asset('assets/js/modernizr-custom.js') }}"></script>
</head>
<body>
<script>
    const BASE_URL = '{{ asset('') }}';
</script>


<div class="container-fluid">
    <div align="center">
        @if(!empty(getStoreSettings()->logo))
            <img src="{{ asset('img/'.getStoreSettings()->logo) }}"  class="img-responsive" style="width:10%; margin: auto; display: block;"/>
        @else
            <h2 class="logo">{{ getStoreSettings()->name }}</h2>
        @endif
    </div><br/>
    <div class="col-sm-12 col-lg-offset-3 col-lg-6">
        <div class="panel">
            <div class="panel-heading">
                <h4 class="panel-title text-center">SCAN INVOICE QR CODE TO CHECKOUT</h4>
                <p class="card-title-desc text-center">Please Scan the Qr Code on the Invoice to Checkout Invoice</p>
            </div>
            <div class="panel-body p4">

                <div>
                    <div class="row">
                        <div class="col-12" style="position: relative">
                            <div style="padding: 0px;background: #f2f2f2;" id="reader">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('bower_components/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('bower_components/autosize/dist/autosize.min.js') }}"></script>
<script src="{{ asset('dist/js/main.js') }}"></script>
<script src="{{ asset('js/sweetalert2.js') }}"></script>
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
    let html5QrcodeScanner = undefined;
    function onScanError(errorMessage) {
        console.log(errorMessage);
    }

    function onScanSuccess(decodedText, decodedResult){
        //decodedText
        beep();
        Swal.fire({
            title: 'Processing invoice',
            html: 'Please wait...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });
        $.get('{{ route('processScaninvoice') }}?invoice_code='+decodedText, function(response){
            beep();
            Swal.hideLoading();
            html5QrcodeScanner.clear();
            if(response.status === false) {
                Swal.fire({
                    title: "Invoice Scanned Result",
                    text: response.message,
                    icon: "error"
                }).then((result) => {
                    window.location.reload();
                });
            } else {
                beep();
                Swal.fire({
                    title: "Invoice Scanned Result",
                    text: "invoice has been scanned successfully!",
                    icon: "success"
                });
                setTimeout(function(){
                   window.location.reload();
                }, 2000)
            }
        })


    }

    function initScanner() {
        let config = {
            fps: 10,
            qrbox: 250,
            showTorchButtonIfSupported: true,
            rememberLastUsedCamera: true
        }

        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", config);
        html5QrcodeScanner.render(onScanSuccess);
        let ping = setInterval(function(){
            $('#reader button')
                .addClass('btn')
                .addClass('btn-lg')
                .addClass('btn-primary')
                .addClass('mb-2');

            $('#reader select').addClass('form-control').addClass('mt-2').addClass('mb-2')
        },10);
    }



    const myAudioContext = new AudioContext();

    function beep(duration, frequency, volume){
        return new Promise((resolve, reject) => {
            // Set default duration if not provided
            duration = duration || 200;
            frequency = frequency || 440;
            volume = volume || 100;

            try{
                let oscillatorNode = myAudioContext.createOscillator();
                let gainNode = myAudioContext.createGain();
                oscillatorNode.connect(gainNode);

                // Set the oscillator frequency in hertz
                oscillatorNode.frequency.value = frequency;

                // Set the type of oscillator
                oscillatorNode.type= "square";
                gainNode.connect(myAudioContext.destination);

                // Set the gain to the volume
                gainNode.gain.value = volume * 0.01;

                // Start audio with the desired duration
                oscillatorNode.start(myAudioContext.currentTime);
                oscillatorNode.stop(myAudioContext.currentTime + duration * 0.001);

                // Resolve the promise when the sound is finished
                oscillatorNode.onended = () => {
                    resolve();
                };
            }catch(error){
                reject(error);
            }
        });
    }


    $(document).ready(function(){
        initScanner()
    });

</script>
</body>

</html>
