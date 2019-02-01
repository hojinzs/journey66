var gMapKey;
var form = '#journey';

function initMap() {
    let ujid = $('#journey').data('ujid');

    Journey66.Mapkey = $('#map').data('gmapkey');
    Journey66.Map = new google.maps.Map(document.getElementById('map'), {
        zoom: 1,
        center: { lat: 1.0, lng: 1.0 }
    });

    $.ajax({
        type: "GET",
        url: '/api/journey/' + ujid,
        dataType: "json",
        success: function (data) {
            Journey66.Reader(data);
        }
    });
};

$(document).ready(function (){

    $('.gmap-static-img').click(function (event) {
        $('html, body').stop().animate({
            scrollTop: 0
        }, 500);
    });

    $('.waypoint-img').each(function(i, image){
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

Journey66.Reader = function(data){
    let Reader = new JournalLogger(Journey66.Map,Journey66.Mapkey);
    Reader.setSequence(data.sequence);
    Reader.centerAndZoom();
    Reader.TrackMarker();

    // Set Waypoints
    data.waypoints.forEach(function (waypoint, k) {
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
        var LatLng = new google.maps.LatLng(waypoint.latitude, waypoint.longitude);
        Journal.setMarker(Journey66.Map, $(wps), k, LatLng, {
            title: waypoint.name,
            label: label
        });

    });
};