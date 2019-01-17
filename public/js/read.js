var map;
var form = '#journey';

function initMap(){
  var gMapKey = $('#map').data('gmapkey');
  var gpxurl = "/api/gpx/"+$('#map').data('gpx');
  var ujid = $('#journey').data('ujid');


  var GetGpxFile = new Promise(function(resolve, reject)
  {
    $.ajax({
      type: "POST",
      url: gpxurl,
      dataType: "xml",
      success: function(data) {
        resolve(data);
      }
    });
  })
  
  var getJourneyData = new Promise(function(resolve, reject){
    $.ajax({
      type: "GET",
      url: '/api/journey/'+ujid,
      dataType: "json",
      success: function(data){
        resolve(data);
      }
    });  
  })

  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 1,
    center: {lat: 1.0, lng: 1.0}
  });

  Promise.all([GetGpxFile,getJourneyData]).then(function(values){
    console.log(values);

    var parser = new GPXParser(values[0], map);
    parser.setTrackColour("#ff0000");     // Set the track line colour
    parser.setTrackWidth(3);          // Set the track line width
    parser.setMinTrackPointDelta(0.001);      // Set the minimum distance between track points
    parser.centerAndZoom(values[0]);
    parser.addTrackpointsToMap();         // Add the trackpoints
    // parser.addRoutepointsToMap();         // Add the routepoints
    // parser.addWaypointsToMap();           // Add the waypoints

    var Reader = new JournalLogger(map);

    // //set Static Map
    // smap = target.find('.gmap-static-img');
    // Journal.setStaticMap(smap,{
    //   width : "300",
    //   height : "300",
    //   zoom : map.getZoom() + 1,
    //   lat : latitude,
    //   lng : longitude,
    //   encpath : $('#map').data('summary-polyline'),
    // });

    // //set Marker
    // Journal.setMarker(map,target,k+1,LatLng);

    // // //set Img Gallary
    // // galimgs = $('.waypoint-galarry').children();
    // // Journal.setGallary(galimgs);    
  });
};

$(document).ready(function(){

});