var map;
var gMapKey;
var form = '#journey';

function initMap() {
  gMapKey = $('#map').data('gmapkey');
  var gpxurl = "/api/gpx/" + $('#map').data('gpx');
  var ujid = $('#journey').data('ujid');
  var encpath = $('#map').data('summary-polyline');


  var GetGpxFile = new Promise(function (resolve, reject) {
    $.ajax({
      type: "POST",
      url: gpxurl,
      dataType: "xml",
      success: function (data) {
        resolve(data);
      }
    });
  })

  var getJourneyData = new Promise(function (resolve, reject) {
    $.ajax({
      type: "GET",
      url: '/api/journey/' + ujid,
      dataType: "json",
      success: function (data) {
        resolve(data);
      }
    });
  })

  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 1,
    center: { lat: 1.0, lng: 1.0 }
  });

  Promise.all([GetGpxFile, getJourneyData]).then(function (values) {

    var parser = new GPXParser(values[0], map);
    parser.setTrackColour("#ff0000");     // Set the track line colour
    parser.setTrackWidth(3);          // Set the track line width
    parser.setMinTrackPointDelta(0.001);      // Set the minimum distance between track points
    parser.centerAndZoom(values[0]);
    parser.addTrackpointsToMap();         // Add the trackpoints

    var Reader = new JournalLogger(map);
    Reader.setSequence(values[1].sequence);

    values[1].waypoints.forEach(function (waypoint, k) {
      //find waypoint section
      var wps = document.getElementById(waypoint.UWID);

      //set Static Map
      smap = wps.getElementsByClassName('gmap-static-img');
      Journal.setStaticMap(smap, {
        width: "250",
        height: "250",
        zoom: map.getZoom() + 1,
        lat: waypoint.latitude,
        lng: waypoint.longitude,
        encpath: encpath,
      });

      //set Marker label
      switch (waypoint.type) {
        case 'starting':
          var label = 'S'
          break;

        case 'destination':
          var label = 'F'
          break;

        default:
          var label = 'W' + k
          break;
      }

      //set Marker
      var LatLng = new google.maps.LatLng(waypoint.latitude, waypoint.longitude);
      Journal.setMarker(map, $(wps), k, LatLng, {
        title: waypoint.name,
        label: label
      });

    });
  });
};

$(document).ready(function () {

  // unUse
  // $(window).scroll(function (event) {
  //   var scroll = $(this).scrollTop();
  //   var mapHeight = $('.journey-map').height();
  //   // $('#scrollevent').text(scroll+"/"+mapHeight);

  //   if (scroll > mapHeight) {

  //   } else {

  //   }

  // });

  $('.gmap-static-img').click(function (event) {
    $('html, body').stop().animate({
      scrollTop: 0
    }, 500);
  });

  $('.waypoint-img').each(function(i, image){
    console.log(image);
    let imgWidth = $(image).width();
    let URL = image.src;
    
    loadImage(
      URL,
      function(loadedImage){
        let $canvas = $(loadedImage);
        $canvas.addClass('waypoint-img rounded');
        $(image).replaceWith($canvas);
      },
      {
        maxWidth: imgWidth,
        orientation: true,
        canvas: false,
      }
    );
  })

  $('.galarry-images').slick({
    dots: true,
    infinite: false,
    // speed: 300,
    slidesToShow: 1,
    variableWidth: true
  });
});