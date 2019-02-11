function initMap() {
    Journey66.Mapkey = $('#map').data('gmapkey');
    Journey66.Map = new google.maps.Map(document.getElementById('map'), {
        zoom: 1,
        center: { lat: 1.0, lng: 1.0 },
        mapTypeControl: false,
        zoomControl: false,
        // gestureHandling: "cooperative",
        gestureHandling: false,
        streetViewControl: false,
        fullscreenControl: false,
        rotateControl: false,
    });
};

$(document).ready(function (){

    // $('.waypoint-img').each(function(i, image){
    //     let imgWidth = $(image).width();
    //     let URL = image.src;
        
    //     loadImage(
    //     URL,
    //     function(loadedImage){
    //         let $canvas = $(loadedImage);
    //         $canvas.addClass('waypoint-img rounded');
    //         $(image).replaceWith($canvas);
    //     },
    //     {
    //         maxWidth: imgWidth,
    //         orientation: true,
    //         canvas: false,
    //     }
    //     );
    // })

    $('.galarry-images').slick({
        dots: true,
        arrows: false,
        infinite: false,
        slidesToShow: 1,
        variableWidth: true
    });

    $('.gmap-static-img').parent().click(function (event){
        $('html, body').stop().animate({
            scrollTop: $('#map').offset().top
        }, 500);
    });
});

Journey66.Reader = function(data){
    let Reader = new JournalLogger(Journey66.Map,Journey66.Mapkey);
    Reader.setSequence(data.sequence);
    Reader.centerAndZoom();
    Reader.TrackMarker({
        marker : false
    });

    // Set Waypoints
    data.waypoints.forEach(function(waypoint, k) {
        //find waypoint section
        let wps = document.getElementById(waypoint.UWID);

        //set Static Map
        let encpath = $('#map').data('summary-polyline');
        let smap = wps.getElementsByClassName('gmap-static-img');
        Journal.setStaticMap(smap, {
            width: "250",
            height: "250",
            zoom: Reader.zoom + 1,
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
        let LatLng = new google.maps.LatLng(waypoint.latitude, waypoint.longitude);
        Journey66.setMarker({
            latlng : LatLng,
            target : wps,
            Idx : k,
            title : waypoint.name,
            label : label,
        });
    });

};