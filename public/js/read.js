var map;
var form = '#journey';
var gMapKey;

function initMap(){
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 1,
    center: {lat: 1.0, lng: 1.0}
  });

  gMapKey = $('#map').data('gmapkey');
  var gpxurl = "/api/gpx/"+$('#map').data('gpx');

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
    waypoints = $('.waypoint');

    $.each(waypoints,function(k,waypoint){
      target = $(waypoint);

      latitude = target.data("latitude");
      longitude = target.data("longitude");
      LatLng = new google.maps.LatLng(latitude,longitude);

      //set Static Map
      smap = target.find('.gmap-static-img');
      Journal.setStaticMap(smap,{
        width : "300",
        height : "300",
        zoom : map.getZoom(),
        lat : latitude,
        lng : longitude
      });
      
      //set Marker
      Journal.setMarker(map,target,k+1,LatLng);

      //set Img Gallary
      galimgs = $('.waypoint-galarry').children();
      Journal.setGallary(galimgs);
    })
  });
};

$(document).ready(function(){

});