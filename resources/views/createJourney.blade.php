@php
    $gmapkey = env('GOOGLE_MAPS_KEY',null);
    App::setLocale("ko");
@endphp

@extends('layout.app')

@section('title', 'Write the new Journey')

@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js"></script>
    <script type="text/javascript" src="../js/loadgpx.js"></script>
    <script type="text/javascript" src="../js/wplogger.js"></script>
    <script type="text/javascript" src="../js/write.js"></script>
@endpush

@push('css')
    <link rel="stylesheet" type="text/css" href="css/style.css">    
@endpush

@section('contents')

    @component('components.topMenu')
    @endcomponent

    <div id="map"></div>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{$gmapkey}}&callback=initMap"></script>

    <div class="container">

        <!-- Gpx Upload-->
        <form id="GPX-upload">
            <h3>{{__('journey.form.gpxupload.title')}}</h3>
            <p>{{__('journey.form.gpxupload.description')}}</p>
            <input id="gpx-upload-file" name="gpx" type="file" accept=".gpx">
            <a href="#" onclick="javascriot:gpxupload_test()">use sample gpx file</a>
        </form>

        <!-- Journey input form-->
        <form method="POST" id="journey" style="display:none" action="/api/newjourney">
            <fieldset name="title" id="title">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group row">
                                <label for="journey-title" class="col-md-2 col-form-label">{{__('journey.form.title')}}</label>
                                    <div class="col-md-10">
                                        <input id="journey-title" name="journey-title" class="form-control" type="text" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="journey-type" class="col-md-2 col-form-label">{{__('journey.form.style')}} </label>
                                    <div class="col-md-10">
                                        <select id="journey-type" name="journey-type" class="form-control">
                                            @foreach ($journey_labels as $label)
                                            <option value="{{$label->name}}">{{__('journey.label.journey.'.$label->name)}}</option>
                                            @endforeach
                                        </select>                                
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="journey-description" class="col-md-2 col-form-label">{{__('journey.form.description')}}</label>
                                    <div class="col-md-10">
                                        <textarea id="journey-description" name="journey-description" class="form-control" type="text" rows="5" placeholder="{{__('journey.form.description_ph')}}"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="alert alert-secondary" id="waypoint-guide">
                {{__('journey.form.inform.setmarker')}}
            </div>

            <fieldset name="waypoint-list" id="waypoint-list">

            </fieldset>

            <fieldset id="confirm" name="confirm">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">{{__('journey.form.confirm_title')}}</h4>
                                <div class="form-group row">
                                    <label for="author" class="col-md-2 col-form-label">{{__('journey.form.author')}}</label>
                                    <div class="col-md-10">
                                        <input id="author" name="author" class="form-control" placeholder="{{__('journey.form.author_ph')}}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-md-2 col-form-label">{{__('journey.form.email')}}</label>
                                    <div class="col-md-10">
                                        <input id="email" name="email" class="form-control" type="email" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="check_email" class="col-md-2 col-form-label"></label>
                                    <div class="col-md-10">
                                        <input name="check_email" class="form-check-label" type="checkbox" required>
                                        <label>{{__('journey.form.email_check')}}</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <input type="submit" value="Submit" type="button" class="btn btn-primary btn-lg btn-block">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

        </form>
        <!-- Journey form end-->

        @component('components.formWaypoint',[
            'waypoint_labels' => $waypoint_labels,
            'id' => "DUMMY",
            'style' => "display:none",
        ])
        @endcomponent

    </div>

@endsection