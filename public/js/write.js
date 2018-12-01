var map;
var JLogger;
var src = 'test.gpx';
var gMapKey = "AIzaSyC24oO9KSFgwoDRSdQQzOEhbHYOAX4ldsc";

function initMap(){
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 1,
    center: {lat: 1.0, lng: 1.0}
  });

  map.markers = [];

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

        var JLogger = new JournalLogger(map);
        JLogger.TrackMarker(track);
        JLogger.setForm();

        $('#GPX-upload').detach();

    });

};


function gpxupload(e) {
  var file = e.target.files[0];
  var output = document.getElementById('waypoint');

  output.src = URL.createObjectURL(file);

  loadGPXFileIntoGoogleMap(this.map,samplegpx);

};

function gpxupload_test(e) {
  var samplegpx = "sample_gpx/300k.gpx";

  loadGPXFileIntoGoogleMap(this.map,samplegpx);

};

$(document).ready(function(){
  $('#gpx-upload-file').on('change',gpxupload);

  $( "form" ).on( "submit", function( event ) {
    event.preventDefault();
    console.log( $( this ).serialize() );
  });


});

