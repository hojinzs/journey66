var map;
var form = '#journey';
var JLogger;
var gMapKey;

$(document).ready(function(){
    $('#gpx-upload-file').on('change',gpxupload);
    $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $('#uploadPath').modal({
      backdrop: 'static',
      keyboard: false,
    })
    $('#uploadPath').modal('show');
});

function initMap(){
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 1,
        center: {lat: 1.0, lng: 1.0}
    });
    
    gMapKey = $('#map').data('gmapkey');
};

function loadGPXFileIntoGoogleMap(map, filename) {

  function getData(callback){
      return new Promise(function(resolve,reject){
          $.ajax({
                url: filename,
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

                  resolve(parser,filename);
              }
          });
      });
  }

  getData().then(function(parser,filename){
    var track = parser.track.getPath();
    var xml = parser.xmlDoc;

    console.log(track);

    JLogger = new JournalLogger(map);
    JLogger.TrackMarker(track);
    JLogger.setForm(form);
    JLogger.CreateJourney();
    JLogger.gpx = xml;

    $('#uploadPath').modal('hide');
    
    $('#gpx-upload-file').prependTo(form).hide();
    $('#GPX-upload').detach();
  });

};


function gpxupload(e) {
  var file = e.target.files[0];
  result = URL.createObjectURL(file);

  // Upload GPX file and get temporary url
  var gpx = new FormData();
  gpx.append('gpx', file);

  $.ajax({
    url: "/api/gpxupload",
    type: "POST",
    data: gpx,
    contentType: false,
    processData: false,
    beforeSend: function(){
        var $LoadingImg = $('<img/>',{
            src: 'https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif',
        })
        $('#uploadPath .modal-body').empty();
        $('#uploadPath .modal-body').css('text-align','center');
        $('#uploadPath .modal-body').append($LoadingImg);
        $('#uploadPath .modal-body').append('<p>Uploading file...</p>');
    },
    success: function(data){
      $(form).attr('data-polyline',data.polyline_path);
      $(form).attr('data-gpx',data.gpx_path);
      $(form).attr('data-summary-polyline',data.encoded_polyline_summary);

      loadGPXFileIntoGoogleMap(map,result);
    },
    complete: function(){
      // $('#uploadPath .modal-body').empty();
    }
  });

};

function gpxupload_test(e) {
  var samplegpx = "sample_gpx/300k.gpx";

  loadGPXFileIntoGoogleMap(map,samplegpx);

};

function setTrack(pointarray){
  var track = new google.maps.Polyline({
    path: pointarray,
    strokeColor: "#ff00ff",
    strokeWeight: 5,
    map: map,
    visible: true,
    zIndex: 1
  });

  return track;
};