@extends('layout.app')

@section('title', 'Write the new Journey')

@push('meta')
    <meta name="title" content="Journey 66 Write new Journey">
    <meta name="description" content="write and share your wonderful journey">

    <meta property="og:site_name" content="Journey 66">
    <meta property="og:title" content="Journey 66 Write new Journey" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="write and share your wonderful journey" />
    <meta property="og:url" content="http://journey66.cc/write" />
    <meta property="og:image" content="adult-backlit-bicycle-1522545.jpg" />
@endpush

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

    @component('components.section66',[
        'name' => 'getTrack',
        'section' => 'sections.getTrack',
        'thumbnail' => '/assets/adult-backlit-bicycle-1522545.jpg',
    ])
    @endcomponent

    @component('components.section66',[
        'name' => 'setCover',
        'section' => 'sections.setCover',
    ])
    @endcomponent

    @component('components.GoogleMap',[
        'scriptparam' => [
            'region' => 'KR',
            'libraries' => 'geometry',
            'callback' => 'initMap',
        ],
    ])
    @endcomponent
    

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