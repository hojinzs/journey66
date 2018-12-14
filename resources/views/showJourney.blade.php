@php
    $gmapkey = env('GOOGLE_MAPS_KEY',null)
@endphp

@extends('layout.app')

@section('title', $journey['name'])

@push('meta')
    <meta name="description" content="{{$journey['description']}}">
    <meta name="googlebot" content="">
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js"></script>
    <script type="text/javascript" src="../js/loadgpx.js"></script>
    <script type="text/javascript" src="../js/wplogger.js"></script>
    <script type="text/javascript" src="../js/read.js"></script>
@endpush

@push('css')
    <link rel="stylesheet" type="text/css" href="../css/read.css">    
@endpush

@section('contents')

    @component('components.topMenu')
    @endcomponent

    <div class="container">
        <h1>{{$journey['name']}}</h1>
    </div>
    <div id="map"></div>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{$gmapkey}}&callback=initMap"></script>
    <div class="container">
        <h1 hidden>{{$journey['name']}}</h1>
        <p>{{$journey['description']}}</p>

        @foreach ($waypoints as $waypoint)
        <div class="waypoint" latitude="{{$waypoint['latitude']}}" longitude="{{$waypoint['longitude']}}">
            <div class="row">
                <div class="col-md-12 waypoint_name">
                    <span class="badge badge-secondary">{{$waypoint['type']}}</span>
                </div>
                <div class="col-md-12 waypoint-galarry">
                    <div class="img-container gmap-static-img">
                        <img src="" class="img-fluid rounded">
                    </div>
                    @foreach ($waypoint['images'] as $img)
                    <div class="img-container waypoint-img">
                        <img src="{{$img['path']}}" class="img-fluid rounded">
                    </div>
                    @endforeach
                </div>
                @isset($waypoint['name'])
                <div class="col-md-12 waypoint_name">
                    <h2>{{$waypoint['name']}}</h2>
                </div>
                @endisset
                @isset($waypoint['description'])
                <div class="col-md-12 waypoint_description">
                    <p>{{$waypoint['description']}}</p>
                </div>
                @endisset
            </div>
        </div>
        @endforeach


    </div>

@endsection