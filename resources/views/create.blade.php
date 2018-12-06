@extends('layout.app')

@section('title', 'Write the new Journey')

@push('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js"></script>
<script type="text/javascript" src="js/loadgpx.js"></script>
<script type="text/javascript" src="js/wplogger.js"></script>
<script type="text/javascript" src="js/write.js"></script>
@endpush

@push('css')
<link rel="stylesheet" type="text/css" href="css/style.css">    
@endpush

@section('contents')

<div>
    <p><b>Journey66::</b>Google Maps GPX SAMPLE</p>
</div>
<hr>
<div id="map"></div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC24oO9KSFgwoDRSdQQzOEhbHYOAX4ldsc&callback=initMap"></script>

<div class="container">

    <!-- Gpx Upload-->
    <form id="GPX-upload">
        <h3>Select GPX file</h3>
        <input id="gpx-upload-file" name="gpx" type="file" accept=".gpx">
        <a href="#" onclick="javascriot:gpxupload_test()">use sample gpx file</a>
    </form>

    <!-- Waypoint input form-->
    <form method="POST" id="journey" style="display:none" action="/api/newjourney">
        <fieldset name="title" id="title">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="journey-title" class="col-md-2 col-form-label">Journey title</label>
                                <div class="col-md-10">
                                    <input id="journey-title" name="journey-title" class="form-control" type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="journey-type" class="col-md-2 col-form-label">Journey Style</label>
                                <div class="col-md-10">
                                    <select id="journey-type" name="journey-type" class="form-control">
                                        @foreach ($journey_labels as $label)
                                            <option>{{$label->name}}</option>
                                        @endforeach
                                    </select>                                
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="journey-description" class="col-md-2 col-form-label">description</label>
                                <div class="col-md-10">
                                    <textarea id="journey-description" name="journey-description" class="form-control" type="text" rows="5" placeholder="text something about your journey"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <div class="alert alert-secondary" id="waypoint-guide">
            Click on the memorable point. And tell me about your journey.
        </div>

        <fieldset name="waypoint-list" id="waypoint-list">

        </fieldset>

        <fieldset id="confirm" name="confirm">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Confirm & Publish your Journey</h4>
                            <div class="form-group row">
                                <label for="author" class="col-md-2 col-form-label">author</label>
                                <div class="col-md-10">
                                    <input id="author" name="author" class="form-control" placeholder="let us to know who writen this wonderful journey">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-md-2 col-form-label">email</label>
                                <div class="col-md-10">
                                    <input id="email" name="email" class="form-control" type="email">
                                </div>
                            </div>
                            <div class="row">
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
    <!-- Waypoint input form end-->

    <fieldset name="DUMMY" id="DUMMY" style="display:none">
        <div class="row">
            <div class="col-md-3">
                <img id="static-map" style="width: 100%" src="">
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="form-group card-header">
                        <legend id="wp-name">Waypoint #</legend>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="Lat Lng" class="col-md-2 col-form-label">Location</label>
                            <div class="col-md-10">
                                <input id="Lat" name="Lat" class="disabled" type="number" value="12" readonly>
                                <input id="Lng" name="Lng" class="disabled" type="number" value="32" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="waypoint-name" class="col-md-2 col-form-label">name</label>
                            <div class="col-md-10">
                                <input id="waypoint-name" name="waypoint-name" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="waypoint-type" class="col-md-2 col-form-label">type</label>
                            <div class="col-md-10">
                                <select id="waypoint-type" name="waypoint-type" class="form-control">
                                    @foreach ($waypoint_labels as $label)
                                        <option>{{$label->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="image" class="col-md-2 col-form-label">photo</label>
                            <div id="waypoint-images" class="col-md-10">
                                <div class="image" style="overflow: hidden;">
                                    <!-- <img id="upload-images" src="https://via.placeholder.com/100" class="gallary rounded float-left" alt="..."> -->
                                </div>
                                <input type="file" id="input_img" multiple/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-md-2 col-form-label">description</label>
                            <div class="col-md-10">
                                <textarea id="description" name="description" class="form-control" rows="5" placeholder="description about this waypoint"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="waypoint-up" type="button" class="btn btn-light">up</button>
                        <button id="waypoint-down" type="button" class="btn btn-light">down</button>
                        <button id="waypoint-delete" type="button" class="btn btn-danger">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="test_serialize_result">

    </div>

</div>

@endsection