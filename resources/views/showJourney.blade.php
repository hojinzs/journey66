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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-load-image/2.20.1/load-image.all.min.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/wplogger.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/read.js"></script>
    <script type="text/javascript" src="{{url('/')}}/slick/slick.min.js"></script>
@endpush

@push('css')
    <link rel="stylesheet" type="text/css" href="../css/read.css">
    <link rel="stylesheet" type="text/css" href="../slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="../slick/slick-theme.css"/>
@endpush

@section('contents')

    @component('components.TopMenu')
    @endcomponent

    @component('sections.readerCover',[
        'thumbnail' => '/assets/adult-adventure-asphalt-969679.jpg',
        'name' => $journey->name,
        'date' => $journey->startedAt,
        'distance' => \App\Calc::getDistance($journey['distance']),
    ])
    @endcomponent

    <div class="journey-map level_2">
        <div id="map" data-gmapkey="{{$gmapkey}}" data-gpx="{{$gpx}}" data-summary-polyline="{{$summary_polyline}}"></div>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{$gmapkey}}&callback=initMap"></script>
    </div>
    <article id="journey" data-ujid="{{$journey['UJID']}}">
    <div class="container journey-container level_3">
        <div class="row">
            <div class="journey-contents col-md-12">
                <section>
                    <div class="row journey-head">
                        <div class="col-md-12">
                            <h2>{{$journey->name}}</h2>
                            <p class="author"><i class="fas fa-user-alt"></i> {{$journey->author_name}}     <i class="fas fa-pen-fancy"></i> {{$journey->created_at}} </p>
                            <p class="author">
                                @isset($journey['distance']) {{\App\Calc::getDistance($journey['distance'])}}@endisset
                            </p>
                            <hr>
                            <p>{{$journey->description}}</p>
                        </div>
                    </div>
                </section>

                <section id="waypoint-list">
                    @foreach ($waypoints as $waypoint)
                    <section id="{{$waypoint['UWID']}}" waypoint-id="{{$waypoint['UWID']}}">
                        <div class="row waypoint" data-sequence="{{$waypoint['sequence']}}" data-latitude="{{$waypoint['latitude']}}" data-longitude="{{$waypoint['longitude']}}">
                            <div class="col-md-12 waypoint_type">
                                <span class="badge badge-secondary"><i class="fas fa-{{__($waypoint['icon'])}}"></i>  {{__('journey.label.waypoint.'.$waypoint['type'])}}</span>
                            </div>
                            @isset($waypoint['name'])
                            <div class="col-md-12 waypoint_name">
                                <h3>{{$waypoint['name']}}</h3>
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
                                        <img src="{{$img['path']}}" class="waypoint-img rounded">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @isset($waypoint['distance'])
                            <div class="col-md-12 waypoint_description">
                                <p class="author">{{__('journey.form.waypoint.stats.distance')}} :: {{\App\Calc::getDistance($waypoint['distance'])}}</p>
                            </div>
                            @endisset
                            @isset($waypoint['description'])
                            <div class="col-md-12 waypoint_description">
                                <p>{{$waypoint['description']}}</p>
                            </div>
                            @endisset
                        </div>
                    </section>
                    @endforeach
                </section>

                <hr>

                <div class="row journey-metas">
                    <div class="journey-meta col-md-6">
                        {{-- <p>{{__('journey.form.created_at')}}:: {{date('d/m/Y',strtotime($waypoint['created_at']))}}</p> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </article>

@endsection