var map;
var JLogger;
var src = 'test.gpx';
var gMapKey = "AIzaSyC24oO9KSFgwoDRSdQQzOEhbHYOAX4ldsc";
var gpx;
var img_file = [];

$(document).ready(function(){
  $('#gpx-upload-file').on('change',gpxupload);
  $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  //Form Submit Action
  $( "#waypoint" ).on( "submit", function(event) {
    event.preventDefault();

    // form data
    var FormArray = $(this).serializeField();

    // serialize gpx file
    var oSerializer = new XMLSerializer();
    var sXML = oSerializer.serializeToString(gpx); 
    FormArray.gpx = window.btoa(encodeURIComponent(sXML));

    // ready to json
    var jsonData = JSON.stringify(FormArray);

    //send
    $.ajax({
      url: "/api/newjourney",
      type: "POST",
      contentType: "application/json",
      data: jsonData,
      dataType: "text",
      success: function(data){
        var print = decodeURIComponent(window.atob(data));
        $('.test_serialize_result').text(print);
      },
      error: function(xhr,status,error){
        alert(error);
      }
    });

  }); 

});

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

                  gpx = data;
                  console.log(gpx);

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

        $('#gpx-upload-file').prependTo('#waypoint').hide();

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

$.fn.serializeField = function() {
  var result = {};
  
  this.each(function() {
      
      $(this).find("fieldset").each( function() {
        var $this = $(this);
        var name = $this.attr("name");

        console.log(name);
      
        if (name) {
          result[name] = {};
          $.each($this.serializeArray(), function() {
            result[name][this.name] = this.value;
          }); 
        } else {
          $.each($this.serializeArray(), function() {
            result[this.name] = this.value;
          });
        };
       });
      
  });
  
  return result;
};


