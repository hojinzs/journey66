// Waypoint Marker

function JournalLogger(map,form){
    this.$form = form;
    this.$waypointlist = this.$form.find('#waypoint-list');
    this.$dummywp = $('#DUMMY');
    this.map = map;

    //track setting
    this.trackcolour = "#ff00ff"; // red
    this.trackwidth = 3;
    this.mintrackpointdelta = 0.0001
};

JournalLogger.prototype.setForm = function(){
    this.$form.show();
};

JournalLogger.prototype.TrackMarker = function(track){
    var colour = this.trackcolour;
    var width = this.trackwidth;
    
    var pointarray = track.j

    var polyline = new google.maps.Polyline({
        path: pointarray,
        strokeColor: colour,
        strokeOpacity: 0,
        strokeWeight: (width*5),
        map: this.map,
        visible: true,
        zIndex: 5
    });
    
    
    google.maps.event.addListener(polyline,"mouseover",function(event){

        polyline.setOptions({strokeOpacity: 0.3});

        // 원 그리기
        var testCircle = new google.maps.Circle({
          strokeColor: '#0099ff',
          strokeOpacity: 1,
          strokeWeight: 10,
          fillColor: '#0099ff',
          fillOpacity: 0.5,
          map: this.map,
          center: event.latLng,
          radius: (width*5),
          zIndex: 2
        });

        google.maps.event.addListener(polyline,"mousemove",function(event){
            testCircle.setOptions({center: event.latLng})
        });

        google.maps.event.addListener(polyline,"mouseout",function(event){
            polyline.setOptions({strokeOpacity: 0});
            testCircle.setMap(null);
        });

    });

    // event :: New Waypoint
    google.maps.event.addListener(polyline,'click',function(event){
        var plat = event.latLng.lat();
        var plng = event.latLng.lng();
        var point = new google.maps.LatLng(plat,plng);

        // var waypoint = new JournalLogger(map);
        JLogger.Waypoint(point);
        
    });

}

JournalLogger.prototype.Waypoint = function(latlng){
    var $form = this.$waypointlist;
    var $dummywp = this.$dummywp;

    //get last waypoint node
    var Idx = new JournalLogger.getWaypointIndex($form);

    //set NewWaypoint
    var $newWaypoint = $dummywp.clone(true);
    $newWaypoint.addClass('waypoint');
    $newWaypoint.attr("id",Idx.NextId);
    $newWaypoint.attr("name",Idx.NextId);
    $newWaypoint.data("index",Idx.NextIndex);
    $newWaypoint.find('#wp-name').text('Waypoint #'+Idx.NextIndex);
    $newWaypoint.find('#Lat').val(latlng.lat());
    $newWaypoint.find('#Lng').val(latlng.lng());
    $newWaypoint.show();

    //set NewWaypointEvent
    $newWaypoint.find('#waypoint-delete').on('click',function(e){
        marker.setMap(null);
        $newWaypoint.detach();
        JournalLogger.setWaypointReindex($form,map);
    });
    $newWaypoint.find('#waypoint-up').on('click',function(e){
        $newWaypoint.after($newWaypoint.prev());
        JournalLogger.setWaypointReindex($form,map);
        $('html, body').stop().animate({
            scrollTop: $newWaypoint.offset().top 
            }, 500);
    });
    $newWaypoint.find('#waypoint-down').on('click',function(e){
        $newWaypoint.before($newWaypoint.next());
        JournalLogger.setWaypointReindex($form,map);
        $('html, body').stop().animate({
            scrollTop: $newWaypoint.offset().top 
            }, 500);
    });
    $newWaypoint.find('#input_img').on('change',JournalLogger.handleImgsFilesSelect);

    //set Static Google Maps
    var currentzoom = map.getZoom();

    var staticmap = "https://maps.googleapis.com/maps/api/staticmap?";
    staticmap = staticmap + "size=150x150";
    staticmap = staticmap + "&markers=color:red|"+latlng.lat()+","+latlng.lng();
    staticmap = staticmap + "&zoom=" + currentzoom;
    staticmap = staticmap + "&key=" + gMapKey;

    $newWaypoint.find('#static-map').attr('src',staticmap);

    //get marker node
    var lastmarkeridx = map.markers.length;
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: 'Marker #'+lastmarkeridx,
        label: 'W'+ Idx.NextIndex
    });
    
    //set MarkerEvent
    google.maps.event.addListener(marker,'click',function(event){
        $('html, body').stop().animate({
            scrollTop: $newWaypoint.offset().top 
            }, 500,function(){
                $newWaypoint.focus();
            });
    });

    this.map.markers.push(marker);
    $newWaypoint.data("marker-index",'Marker #'+lastmarkeridx);

    //add Waypoint
    $form.append($newWaypoint);
    $form.data("last-waypoint-idx",Idx.NextIndex);
};

JournalLogger.getWaypointIndex = function(form){
    var $form = form;
    var last = Number($form.data('last-waypoint-idx'));

    if(last){
        var current = last;
        var next = 1+current;
    } else {
        var current = 0;
        var next = 1;
    };

    var nextid = 'Waypoint_'+next;

    this.CurrentIndex = current;
    this.NextIndex = next;
    this.NextId = nextid;
}

JournalLogger.setWaypointReindex = function(form,map){
    var $form = form;
    var map = map;
    
    //Initialization Waypoint id 
    $form.removeData("last-waypoint-idx");

    $form.children('.waypoint').each(function(i,e){

        var Idx = new JournalLogger.getWaypointIndex($form);
        var oldmarkerindex = $(e).data("marker-index");

        $(e).attr("id",Idx.NextId);
        $(e).data("index",Idx.NextIndex);
        $(e).find('#wp-name').text('Waypoint #'+Idx.NextIndex);

        $form.data("last-waypoint-idx",Idx.NextIndex);

        //ReLabeling Markers
        for (var i=0; i<map.markers.length; i++) {
            if (map.markers[i].title == oldmarkerindex) {
                map.markers[i].setLabel('W'+ Idx.NextIndex);
            }
        }

    });

    return;
}

JournalLogger.handleImgsFilesSelect = function(e){

    var sel_files = [];

    var $target = $(e.target).parents('#waypoint-images').find('.image');

    var files = e.target.files;
    var filesArr = Array.prototype.slice.call(files);

    filesArr.forEach(function(f){
        if(!f.type.match("image.*")){
            alert("Only upload Imagefiles");
            return;
        }

        sel_files.push(f);

        var reader = new FileReader();
        reader.onload = function(e){
            var $newImg = $('<img/>',{
                class: 'gallary rounded float-left',
                src: e.target.result
            })
            
            $newImg.appendTo($target);
        }
        reader.readAsDataURL(f);

    });

};