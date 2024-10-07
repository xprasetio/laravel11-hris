@extends('layouts.presensi')
@section('header')
 <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">E-Absensi</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
<div class="camera-container">
    <div class="col">
        <div class="camera-view"></div>
        <input type="text" id="lokasi" value="">
        <div class="row" style="margin-top: 10px">
            <div class="col">
                @if ($presensi > 0)
                <button class="btn btn-danger btn-block" id="absen-masuk">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Pulang
                </button>  
                @else
                <button class="btn btn-primary btn-block" id="absen-masuk">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Masuk
                </button> 
                @endif
            </div>
        </div>
        <div class="row mt-2">
             <div class="col">
                <div id="map">ss</div>
             </div>
        </div>
</div>
<style>
    .camera-container{           
        margin-top: 80px;
    }
    .camera-view{
        width: 100%;
        height: auto;
        margin: auto;
    }
    #lokasi{
        position: absolute;
        bottom: 20px;
        left: 20px;
    }
    #map { height: 300px; }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
      <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin="">
</script>
<audio src="{{ asset('assets/audio/masuk.mp3') }}" id="notifikasi_in" type="audio/mpeg"></audio>
<audio src="{{ asset('assets/audio/keluar.mp3') }}" id="notifikasi_out" type="audio/mpeg"></audio>
@endsection


@push('myscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    var notifikasi_in = document.getElementById('notifikasi_in');
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90,
        constraints: {
            facingMode: "environment"
        },
        force_flash: false,
        flipHorizontal: false
    });
    Webcam.on( 'load', function() {
        const video = document.querySelector('video');
        video.style.borderRadius = '20px';
    });
    Webcam.attach( '.camera-view' );

    var lokasi = document.getElementById('lokasi');
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback, {enableHighAccuracy: true, timeout: 5000, maximumAge: 0});
    }

    function successCallback(position) {
        var latitude = position.coords.latitude.toFixed(6);
        var longitude = position.coords.longitude.toFixed(6);
        console.log('Latitude: ' + latitude + ', Longitude: ' + longitude); // Debugging untuk mengecek apakah berhasil
        // Set nilai lokasi pada input
        lokasi.value = latitude + ', ' + longitude;
        var map = L.map('map').setView([latitude, longitude], 15);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        var marker = L.marker([latitude, longitude]).addTo(map);
        }

    function errorCallback(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                console.log("User denied the request for Geolocation. Please allow location access in your browser setting.");
                break;
            case error.POSITION_UNAVAILABLE:
                console.log("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                console.log("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                console.log("An unknown error occurred.");
                break;
        }
    }

   $('#absen-masuk').on('click', function(){
        Webcam.snap(function(data_uri) {
            image = data_uri;            
        })
        var lokasi = document.getElementById('lokasi').value;

        $.ajax({
            type: "POST",
            url: "/presensi/store",
            data: {
                "_token": "{{ csrf_token() }}",
                lokasi: lokasi,
                image: image
            },
            cache: false,
            success: function (response) {
                var status = response.split("|");
                if (status[0] == "success") {
                    if (status[2] == "in") {
                        notifikasi_in.play();
                    } else {
                        notifikasi_out.play();
                    }
                    Swal.fire({
                        title: 'Berhasil',
                        text: status[1],
                        icon: 'success',
                        confirmButtonText: 'OK'
                        })
                        setTimeout(function(){ window.location.href = '/dashboard'; }, 2000);
                } else {
                    Swal.fire({
                        title: 'Error !',
                        text: status[1],
                        icon: 'error',
                        confirmButtonText: 'OK'
                        })
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

    });
    

</script>
@endpush

