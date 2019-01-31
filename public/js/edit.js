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

    //set Key
    var journey_key = $('meta[name="journey-key"]').attr('content');
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

      // Set GPX parser
      var parser = new GPXParser(values[0],map);
      parser.setTrackColour("#ff0000");
      parser.setTrackWidth(3);
      parser.setMinTrackPointDelta(0.001);
      parser.centerAndZoom(values[0]);
      parser.addTrackpointsToMap();


      console.log(values[1]);
      // Set JournalLogger
      var track = parser.track.getPath();
      var Journey = new JournalLogger(map);
      Journey.setForm({
        form: '#journey',
        waypoint_list: 'waypoint-list',
        dummy_waypoint: '#DUMMY',
        journey_posted_modal: '#journeyPosted',
        journey_key: journey_key,
        stats: values[1].stats,
      });
      Journey.$form.attr('data-polyline',values[1].polyline);
      Journey.$form.attr('data-summary-polyline',values[1].summary_polyline);
      Journey.TrackMarker(track);
      Journey.setSequence(values[1].sequence);
      Journey.UpdateJourney();
      Journey.DeleteJourney($('#delete'));
      Journey.GeoPhotoUploader({
        button_id: 'geotag_img_load',
        input_id: 'geotag_img',
        modal_id: 'confrimGeophotoSet',
      });

      // set Waypoints
      // waypoints = Journey.$waypointlist.getElementsByClassName('waypoint'); //javascript
      waypoints = Journey.$waypointlist.find('.waypoint') // jQuery
      Journey.setCurrentWaypoint(waypoints);
    })
  };