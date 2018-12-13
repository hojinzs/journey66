@extends('layout.app')

@section('title', 'Write the new Journey')

@push('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js"></script>
<script type="text/javascript" src="../js/loadgpx.js"></script>
<script type="text/javascript" src="../js/wplogger.js"></script>
@endpush

@push('css')
<link rel="stylesheet" type="text/css" href="../css/style.css">    
@endpush

@section('contents')

<div>
    <p><b>Journey66::</b>Show Journey</p>
</div>
<hr>
<div id="map"></div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC24oO9KSFgwoDRSdQQzOEhbHYOAX4ldsc&callback=initMap"></script>

<div class="container">

    @php
        var_dump($journey['id']);

        foreach ($waypoints as $key => $value) {
            # code...
            var_dump($value);
            echo $value['id'];
            foreach ($value['images'] as $key => $value) {
                # code...
                echo '<img src="'.$value['path'].'"/>';
            }
        }
    @endphp

</div>

@endsection