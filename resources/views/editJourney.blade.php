@php
    $gmapkey = env('GOOGLE_MAPS_KEY',null);
@endphp

@extends('layout.app')

@section('title', 'edit Journey')

@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/loadgpx.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/wplogger.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/edit.js"></script>
@endpush

@push('css')
    <link rel="stylesheet" type="text/css" href="{{url('/')}}/css/style.css">    
@endpush

@section('contents')

    @component('components.TopMenu')
    @endcomponent

    <div id="map" data-gmapkey="{{$gmapkey}}"></div>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{$gmapkey}}&callback=initMap"></script>

    <div class="container">

        @component('components.FormJourney',[
            'journey_labels' => $journey_labels,
            'waypoint_labels' => $waypoint_labels,
            'journey' => $journey,
            'waypoints' => $waypoints,
        ])
        @endcomponent

        @component('components.FormWaypoint',[
            'waypoint_labels' => $waypoint_labels,
        ])
        @endcomponent

    </div>

@endsection