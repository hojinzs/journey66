@php
    $gmapkey = env('GOOGLE_MAPS_KEY',null);
@endphp

@extends('layout.app')

@section('title', 'Write the new Journey')

@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/loadgpx.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/wplogger.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/write.js"></script>
@endpush

@push('css')
    <link rel="stylesheet" type="text/css" href="css/style.css">    
@endpush

@section('contents')

    @component('components.TopMenu')
    @endcomponent

    <div id="map" data-gmapkey="{{$gmapkey}}"></div>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{$gmapkey}}&libraries=geometry&callback=initMap"></script>

    <div class="container">

        @component('components.FormJourney',[
            'journey_labels' => $journey_labels,
        ])
        @endcomponent

        @component('components.FormWaypoint',[
            'waypoint_labels' => $waypoint_labels,
        ])
        @endcomponent

    </div>

    @component('modals.uploadPath')
    @endcomponent

    @component('modals.journeyPosted',[
        'mode' => 'new',
    ])
    @endcomponent

@endsection