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
    <script type="text/javascript" src="{{url('/')}}/js/loadgpx.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/wplogger.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/read.js"></script>
@endpush

@push('css')
    <link rel="stylesheet" type="text/css" href="../css/read.css">
@endpush

@section('contents')

    @component('components.TopMenu')
    @endcomponent

    <article id="journey" data-ujid="{{$journey['UJID']}}">
    <div class="container-fluid journey-container">
        <div class="row">
        <div class="col-md-5 journey-map">
            <div id="map" data-gmapkey="{{$gmapkey}}" data-gpx="{{$gpx}}" data-summary-polyline="{{$summary_polyline}}"></div>
            <script async defer src="https://maps.googleapis.com/maps/api/js?key={{$gmapkey}}&callback=initMap"></script>
            </div>
        <div class="journey-contents col-md-7">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{$journey['name']}}</h2>
                    <p class="author">{{__('journey.form.author')}} :: {{$journey['author_name']}}<p>
                    <hr>
                    <p>{{$journey['description']}}</p>
                </div>
            </div>

            @foreach ($waypoints as $waypoint)
            <section>
                <div class="row waypoint" data-latitude="{{$waypoint['latitude']}}" data-longitude="{{$waypoint['longitude']}}">
                    <div class="col-md-12 waypoint_type">
                        <span class="badge badge-secondary">{{__('journey.label.waypoint.'.$waypoint['type'])}}</span>
                    </div>
                    @isset($waypoint['name'])
                    <div class="col-md-12 waypoint_name">
                        <h3>{{$waypoint['name']}}</h3>
                    </div>
                    @endisset
                    <div class="waypoint-medias">
                        <div class="col-md-12 waypoint-galarry">
                            <div class="galarry-images">
                                <div class="img-container">
                                    <img src="" class="gmap-static-img">
                                </div>
                                @foreach ($waypoint['images'] as $img)
                                <div class="img-container">
                                    <img src="{{$img['path']}}" class="waypoint-img">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @isset($waypoint['images'])
                        <div class="galarry-control">
                            <button type="button" class="btn btn-light">< Prev</button> 
                            <button type="button" class="btn btn-light">Next ></button> 
                        </div>
                        @endisset
                    </div>
                    @isset($waypoint['description'])
                    <div class="col-md-12 waypoint_description">
                        <p>{{$waypoint['description']}}</p>
                    </div>
                    @endisset
                </div>
            </section>
            @endforeach

            <hr>

            <div class="row journey-metas">
                <div class="journey-meta col-md-6">
                    <p>{{__('journey.form.created_at')}}:: {{date('d/m/Y',strtotime($waypoint['created_at']))}}</p>
                </div>
            </div>
        </div>
        </div>
    </div>
    </article>

@endsection