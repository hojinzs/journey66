var map;
var form = '#journey';
var Journey;
var gMapKey;

$(document).ready(function(){
  $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
});

function initMap(){
    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 1,
      center: {lat: 1.0, lng: 1.0}
    });
  
    gMapKey = $('#map').data('gmapkey');
    var gpxurl = "/api/gpx/"+$('#journey').data('gpx');
  
    function getData(callback){
      return new Promise(function(resolve,reject){
          $.ajax({
            type: "POST",
            url: gpxurl,
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

        Journey = new JournalLogger(map);
        Journey.setForm(form);
        Journey.TrackMarker(track);

        waypoints = $('.waypoint').not('#DUMMY');
        $.each(waypoints,function(k,waypoint){
          Journey.setWaypoint(waypoint);
        });
        Journey.UpdateJourney();
    });
  };