$(document).ready(function(){
  $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
});

function initMap(){
    Journey66.Map = new google.maps.Map(document.getElementById('map'), {
        zoom: 1,
        center: {lat: 1.0, lng: 1.0}
    });
    Journey66.Mapkey = $('#map').data('gmapkey');
    Journey66.Key = $('meta[name="journey-key"]').attr('content');
    let ujid = $('#journey').data('ujid');

    $.ajax({
        type: "GET",
        url: '/api/journey/'+ujid+'?key='+Journey66.Key,
        dataType: "json",
        success: function(data){
            Journey66.Edit(data);
        }
    });
};

Journey66.Edit = function(data){
    console.log(Journey66.Mapkey);
    // Set JournalLogger
    let Journey = new JournalLogger(Journey66.Map,Journey66.Mapkey);
    Journey.setForm({
        form: '#journey',
        waypoint_list: 'waypoint-list',
        dummy_waypoint: '#DUMMY',
        journey_posted_modal: '#journeyPosted',
        journey_key: Journey66.Key,
        stats: data.stats,
      });
    Journey.$form.attr('data-polyline',data.polyline);
    Journey.$form.attr('data-summary-polyline',data.summary_polyline);
    Journey.setSequence(data.sequence);
    Journey.centerAndZoom();
    Journey.TrackMarker();
    Journey.UpdateJourney();
    Journey.DeleteJourney($('#delete'));
    Journey.GeoPhotoUploader({
        button_id: 'geotag_img_load',
        input_id: 'geotag_img',
        modal_id: 'confrimGeophotoSet',
    });

    // set Waypoints
    waypoints = Journey.$waypointlist.find('.waypoint') // jQuery
    Journey.setCurrentWaypoint(waypoints);
};