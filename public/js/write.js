$(document).ready(function(){
    $.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
});

function initMap(){
    Journey66.Map = new google.maps.Map(document.getElementById('map'), {
        zoom: 1,
        center: {lat: 1.0, lng: 1.0},
        mapTypeControl: false,
        // zoomControl: false,
        gestureHandling: "cooperative",
        streetViewControl: false,
        fullscreenControl: false,
        rotateControl: false,
    });
    Journey66.Mapkey = $('#map').data('gmapkey');
};

Journey66.Write = function(gpx_data,callbackFn){

    let JLogger = new JournalLogger(Journey66.Map,Journey66.Mapkey);
    JLogger.setForm({
    form: '#journey',
        waypoint_list: 'waypoint-list',
        dummy_waypoint: '#DUMMY',
        journey_posted_modal: '#journeyPosted',
        stats: gpx_data.stats,
    });
    JLogger.$form.attr('data-gpx',gpx_data.gpx_path);
    JLogger.$form.attr('data-encoded-polyline',gpx_data.encoded_polyline);
    JLogger.$form.attr('data-summary-polyline',gpx_data.encoded_polyline_summary);
    JLogger.setSequence(gpx_data.sequence);
    JLogger.centerAndZoom();
    JLogger.TrackMarker();
    // JLogger.CreateJourney();
    JLogger.setStartEndWaypoint();
    JLogger.GeoPhotoUploader({
        button_id: 'geotag_img_load',
        input_id: 'geotag_img',
        modal_id: 'confrimGeophotoSet',
    });

    JLogger.$form.on("submit", function(event) {
        event.preventDefault();

        var loading = $('.img-loading').length;
        if(loading > 0) {
            alert(loading+'images upload is unfinished');
            return;
        };

        JLogger.SubmitNew(function(response){
            
            //remove edit mode
            JLogger.$form.hide();
            JLogger.purgeTrackMarker();


            //make image array
            let image = [];
            for(var i = 0,len = response.IMG.length; i < len; i++){
                img = response.IMG[i];
                image.push(img);
            };

            //make data array
            let cover = {
                journey: response.UJID,
                title: response.cover.title,
                distance: response.cover.distance,
                thumbnail: response.cover.thumbnail,
                date: response.cover.date,
            }
            let key = response.KEY;

            //cover, go
            setCover.Start(image,cover,key);
        });
    });

    return callbackFn()

};