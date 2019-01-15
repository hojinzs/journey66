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
    journey_key = $('meta[name="journey-key"]').attr('content');
  
    gMapKey = $('#map').data('gmapkey');
    var gpxurl = "/api/gpx/"+$('#journey').data('gpx');
    var ujid = $('#journey').data('ujid');

    var getGpxFile = new Promise(function(resolve, reject){
      $.ajax({
        type: "POST",
        url: gpxurl,
        dataType: "xml",
        success: function(data){
          resolve(data);
        }
      });
    });

    var getJourneyData = new Promise(function(resolve, reject){
      $.ajax({
        type: "GET",
        url: '/api/journey/'+ujid+'?key='+journey_key,
        dataType: "json",
        success: function(data){
          resolve(data);
        }
      });
    });

    Promise.all([getGpxFile,getJourneyData]).then(function(values){
      var parser = new GPXParser(values[0],map);
      parser.setTrackColour("#ff0000");
      parser.setTrackWidth(3);
      parser.setMinTrackPointDelta(0.001);
      parser.centerAndZoom(values[0]);
      parser.addTrackpointsToMap();

      var track = parser.track.getPath();
      var Journey = new JournalLogger(map);
      Journey.setForm({
        form: '#journey',
        waypoint_list: '#waypoint-list',
        dummy_waypoint: '#DUMMY',
        journey_posted_modal: '#journeyPosted',
      });
      Journey.$form.attr('data-polyline',values[1].polyline_path);
      Journey.$form.attr('data-summary-polyline',values[1].encoded_polyline_summary);
      Journey.TrackMarker(track);
      Journey.setSequence(values[1].sequence);
      Journey.UpdateJourney();
      Journey.DeleteJourney($('#delete'));

      // set Waypoints
      waypoints = $('.waypoint').not('#DUMMY');
      $.each(waypoints,function(k,waypoint){
        Journey.setWaypoint(waypoint);
      });
    })
  };