@php
    $gmapkey = env('GOOGLE_MAPS_KEY',null);
@endphp

@extends('layout.app')

@section('title', 'Write the new Journey')

@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-load-image/2.20.1/load-image.all.min.js"></script>
    <script type="text/javascript" src="/js/geopoint.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/loadgpx.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/wplogger.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/write.js"></script>
    <script type="text/javascript" src="{{url('/')}}/slick/slick.min.js"></script>
@endpush

@push('css')
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="../slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="../slick/slick-theme.css"/>
@endpush

@section('contents')

    @component('components.TopMenu')
    @endcomponent

    @component('sections.getTrack')
    @endcomponent

    <div id="map" data-gmapkey="{{$gmapkey}}"></div>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{$gmapkey}}&libraries=geometry&callback=initMap"></script>
    

    <div class="container">

        @component('components.FormJourney',[
            'journey_labels' => $journey_labels,
            'stats' => ['distance','elevation','duration','startedAt','finishedAt'],
        ])
        @endcomponent

        @component('components.FormWaypoint',[
            'waypoint_labels' => $waypoint_labels,
            'waypoint_stats' => ['distance','elevation','time'],
        ])
        @endcomponent

    </div>

    {{-- @component('modals.uploadPath')
    @endcomponent --}}

    @component('modals.journeyPosted',[
        'mode' => 'new',
    ])
    @endcomponent

    @component('modals.confirmGeophotoSet')
    @endcomponent

@endsection