var map;
var JLogger;
var $form
var src = 'test.gpx';
var gMapKey = "AIzaSyC24oO9KSFgwoDRSdQQzOEhbHYOAX4ldsc";

$(document).ready(function(){
  $form = $("#journey");
  JLogger = new JournalLogger(map,$form);
  
  $('#gpx-upload-file').on('change',gpxupload);
  $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  //Form Submit Action
  $form.on( "submit", function(event) {
    event.preventDefault();

    JLogger.Submit();
  }); 

});

function initMap(){
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 1,
    center: {lat: 1.0, lng: 1.0}
  });

};

function loadGPXFileIntoGoogleMap(map, filename) {

  function getData(callback){
        return new Promise(function(resolve,reject){
            $.ajax({url: filename,
                dataType: "xml",
                success: function(data) {
                  var parser = new GPXParser(data, map);
                  parser.setTrackColour("#ff0000");     // Set the track line colour
                  parser.setTrackWidth(3);          // Set the track line width
                  parser.setMinTrackPointDelta(0.001);      // Set the minimum distance between track points
                  parser.centerAndZoom(data);
                  parser.addTrackpointsToMap();         // Add the trackpoints
                  // parser.addRoutepointsToMap();         // Add the routepoints
                  // parser.addWaypointsToMap();           // Add the waypoints

                  resolve(parser);
                }
            });
        });
    }

    getData().then(function(parser){
        var track = parser.track.getPath();
        var xml = parser.xmlDoc;

        JLogger.TrackMarker(track);
        JLogger.setForm();
        JLogger.gpx = xml;

        $('#gpx-upload-file').prependTo('#journey').hide();

        $('#GPX-upload').detach();

    });

};


function gpxupload(e) {
  var file = e.target.files[0];
  result = URL.createObjectURL(file);

  loadGPXFileIntoGoogleMap(map,result);

};

function gpxupload_test(e) {
  var samplegpx = "sample_gpx/300k.gpx";

  loadGPXFileIntoGoogleMap(map,samplegpx);

};