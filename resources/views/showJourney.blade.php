@extends('layout.app',[
    'footer' => false,
])

@section('title', $journey['name'])

@push('meta')

    <meta name="title" content="{{ $journey->name }}">
    <meta name="description" content="{{ $journey->description }}">

    <meta property="og:site_name" content="Journey 66">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $journey->name }}" />
    <meta property="og:description" content="{{ $journey->description }}" />
    <meta property="og:url" content="http://journey66.cc/journey{{ $journey->ujid }}" />
    <meta property="og:image" content="{{ $journey->getCover()['thumbnail'] }}" />
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-load-image/2.20.1/load-image.all.min.js"></script>
    <script type="text/javascript" src="{{ url('/') }}/js/wplogger.js"></script>
    <script type="text/javascript" src="{{ url('/') }}/js/read.js"></script>
    <script type="text/javascript" src="{{ url('/') }}/slick/slick.min.js"></script>
@endpush

@push('css')
    <link rel="stylesheet" type="text/css" href="../css/read.css">
    <link rel="stylesheet" type="text/css" href="../slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="../slick/slick-theme.css"/>
@endpush

@section('contents')

    @component('components.TopMenu')
    @endcomponent

    @component('components.section66',[
        'name' => 'readerCover',
        'section' => 'sections.readerCover',
        'cover' => $journey->getCover(),
        'thumbnail' => $journey->getCover()['thumbnail'],
    ])
    @endcomponent

    <div class="journey-map level_2">
        @component('components.GoogleMap',[
            'mapdata' => [
                'gpx' => $gpx,
                'summary-polyline' => $summary_polyline,
            ],
            'scriptparam' => [
                'region' => 'KR',
                'libraries' => 'geometry',
                'callback' => 'initMap',
            ],
        ])
        @endcomponent
    </div>
    <div id="journey" data-ujid="{{ $journey['UJID'] }}">
        <div class="container journey-contents">
            <section id="journey-header">
                <div class="row journey-head">
                    <div class="col-md-12">
                        <h2>{{ $journey->name }}</h2>
                        <p class="author"> @isset($journey->distance) {{ \App\Calc::getDistance($journey->distance) }}@endisset </p>
                        <p class="author"> @isset($journey->getCover()['date']) {{ $journey->getCover()['date'] }}@endisset </p>
                        <div class="d-flex flex-row-reverse feature-group">
                            <div class="p-1 feature lilumi-target"><a class="lilumi-target-a"></a><i class="fas fa-ellipsis-h"></i></div>
                        </div>
                        <hr>
                        <p>{!! nl2br(e($journey->description)) !!}</p>
                    </div>
                </div>
            </section>
            <section id="waypoint-list">
                @foreach ($waypoints as $waypoint)
                <section id="{{ $waypoint['UWID'] }}" waypoint-id="{{ $waypoint['UWID'] }}">
                    <div class="row waypoint" data-sequence="{{ $waypoint['sequence'] }}" data-latitude="{{ $waypoint['latitude'] }}" data-longitude="{{ $waypoint['longitude'] }}">
                        <div class="col-md-12 waypoint_type">
                            <span class="badge badge-secondary"><i class="fas fa-{{ __($waypoint['icon']) }}"></i>  {{ __('journey.label.waypoint.'.$waypoint['type']) }}</span>
                        </div>
                        @isset($waypoint['name'])
                        <div class="col-md-12 waypoint_name">
                            <h3>{{ $waypoint['name'] }}</h3>
                        </div>
                        @endisset
                        <div class="col-md-12 waypoint-medias">
                            <!-- use Slick jQuery Slider (http://kenwheeler.github.io/slick/) -->
                            <div class="galarry-images">
                                <div class="img-container">
                                    <img src="" class="gmap-static-img rounded">
                                </div>
                                @foreach ($waypoint['images'] as $img)
                                <div class="img-container">
                                    <img src="{{ $img->path }}" class="waypoint-img rounded">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @isset($waypoint['distance'])
                        <div class="col-md-12 waypoint_description">
                            <p class="author">{{ __('journey.form.waypoint.stats.distance') }} :: {{ \App\Calc::getDistance($waypoint['distance']) }}</p>
                        </div>
                        @endisset
                        @isset($waypoint['description'])
                        <div class="col-md-12 waypoint_description">
                            <p>{!! nl2br(e($waypoint['description'])) !!}</p>
                        </div>
                        @endisset
                    </div>
                </section>
                @endforeach
            </section>
            <hr>
            <div class="journey-reader-end">
                <div><i class="fas fa-pen-fancy"></i></div>
                <div class="author">{{ $journey->author_name }}</div>
                <div>{{ $journey->created_at }}</p></div>
            </div>
        </div>
        @include('footer')
    </div>

@endsection