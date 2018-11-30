<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Journey66:: Write New Journey</title>

        <!-- Load JS Plugin-->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        
        <!-- Load Bootstrap-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

        <!-- Load Custom Scripts & Stylesheet-->
        <script type="text/javascript" src="js/loadgpx.js"></script>
        <script type="text/javascript" src="js/wplogger.js"></script>
        <script type="text/javascript" src="js/common.js"></script>

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
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
                <input id="gpx-upload-file" type="file" accept=".gpx">
                <a href="#" onclick="javascriot:gpxupload_test()">use sample gpx file</a>
            </form>

            <!-- Waypoint input form-->
            <form id="waypoint" style="display:none">
                <fieldset id="title">
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

                <div id="waypoint-list">

                    <fieldset id="DUMMY" style="display:none">
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
                                                    <option>1</option>
                                                    <option>2</option>
                                                    <option>3</option>
                                                    <option>4</option>
                                                    <option>5</option>
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

                </div>

                <fieldset id="confirm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Confirm & Publish your Journey</h4>
                                    <div class="form-group row">
                                        <label for="email" class="col-md-2 col-form-label">email</label>
                                        <div class="col-md-10">
                                            <input id="email" name="email" class="form-control" type="email">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button id="form-submit" type="button" class="btn btn-primary btn-lg btn-block">Submit</button>
                                            <input type="submit" value="Submit">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

            </form>
            <!-- Waypoint input form end-->

            <div class="test_serialize_result">

            </div>

        </div>
    </body>
</html>
