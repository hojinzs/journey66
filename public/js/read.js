var map;
var form = '#journey';
var JLogger;
var gMapKey;

function initMap(){
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 1,
    center: {lat: 1.0, lng: 1.0}
  });

  gMapKey = $('#map').data('gmapkey');
  var gpxurl = $('#map').data('gpx');

  function getData(callback){
    return new Promise(function(resolve,reject){
        $.ajax({
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
    var xml = parser.xmlDoc;

    waypoints = $('.waypoint');

    $.each(waypoints,function(k,waypoint){
      latitude = $(waypoint).data("latitude");
      longitude = $(waypoint).data("longitude");
      zoom = map.getZoom();

      target = $(waypoint).find('.gmap-static-img');
      JournalReader.setStaticMap(target,{
        width : "300",
        height : "300",
        zoom : zoom,
        lat : latitude,
        lng : longitude
      });

      galimgs = $('.waypoint-galarry').children();
      JournalReader.setGallary(galimgs);
    })
  });
};

$(document).ready(function(){

});

var JournalReader = {};

JournalReader.setStaticMap = function(target,param){
  var staticmap = "https://maps.googleapis.com/maps/api/staticmap?"
    +"size="+param.width+"x"+param.height
    +"&markers=color:red|"+param.lat+","+param.lng
    +"&zoom=" + param.zoom
    +"&scale=2"
    +"&key=" + gMapKey;

    $(target).attr('src',staticmap);
}

JournalReader.setGallary = function(target){
  $.each(target,function(k,v){
    console.log(v);
  })
};