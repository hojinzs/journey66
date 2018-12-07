// Waypoint Marker

function JournalLogger(map,form){
    this.$form = form;
    this.$waypointlist = this.$form.find('#waypoint-list');
    this.$dummywp = $('#DUMMY');
    this.map = map;
    this.waypoints = [];
    this.path = [];

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
    var Logger = this;

    this.path = track;

    
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

        Logger.Waypoint(point);
        
    });

}

JournalLogger.prototype.Waypoint = function(latlng){
    var Logger = this;
    var $form = this.$waypointlist;
    var $dummywp = this.$dummywp;

    //get last waypoint node
    var Idx = this.waypoints.length + 1

    //set NewWaypoint
    var $newWaypoint = $dummywp.clone(true);
    $newWaypoint.addClass('waypoint');
    $newWaypoint.attr("id",Idx);
    $newWaypoint.attr("name",Idx);
    $newWaypoint.data("index",Idx);
    $newWaypoint.find('#wp-name').text('Waypoint #'+Idx);
    $newWaypoint.find('#Lat').val(latlng.lat());
    $newWaypoint.find('#Lng').val(latlng.lng());
    $newWaypoint.show();

    //set NewWaypointEvent
    $newWaypoint.find('#waypoint-delete').on('click',function(e){
        $newWaypoint.detach();
        $newWaypoint.marker.setMap(null);

        var index = Logger.waypoints.indexOf($newWaypoint);
        Logger.waypoints.splice(index,1);
        Logger.setWaypointReindex();
    });
    $newWaypoint.find('#waypoint-up').on('click',function(e){
        var oi = Logger.waypoints.indexOf($newWaypoint);
        if (oi == 0){return;}

        var ti = oi - 1;
        swap(Logger.waypoints,oi,ti);
        Logger.setWaypointReindex();
        
        $newWaypoint.after($newWaypoint.prev());        
        $('html, body').stop().animate({
            scrollTop: $newWaypoint.offset().top 
            }, 500);
    });
    $newWaypoint.find('#waypoint-down').on('click',function(e){
        var oi = Logger.waypoints.indexOf($newWaypoint);
        if (oi == Logger.waypoints.length-1){return;}

        var ti = oi + 1;
        swap(Logger.waypoints,oi,ti);
        Logger.setWaypointReindex();

        $newWaypoint.before($newWaypoint.next());
        $('html, body').stop().animate({
            scrollTop: $newWaypoint.offset().top 
            }, 500);
    });
    $newWaypoint.find('#input_img').on('change',function(e){
        Logger.handleImgsFilesSelect(e,$newWaypoint);
    });

    //set Static Google Maps
    var currentzoom = map.getZoom();

    var staticmap = "https://maps.googleapis.com/maps/api/staticmap?";
    staticmap = staticmap + "size=150x150";
    staticmap = staticmap + "&markers=color:red|"+latlng.lat()+","+latlng.lng();
    staticmap = staticmap + "&zoom=" + currentzoom;
    staticmap = staticmap + "&key=" + gMapKey;

    // track line in static map..
    // var colour = this.trackcolour;
    // var width = this.trackwidth;
    // var path = Logger.path;
    // var encpath = google.maps.geometry.encoding.encodePath(path);
    // staticmap = staticmap + "&path=weight:" + width + "%7Ccolor:"+ colour + "%7Cenc:"+ encpath;

    $newWaypoint.find('#static-map').attr('src',staticmap);

    //get marker node
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: 'Marker #'+Idx,
        label: 'W'+ Idx
    });
    
    //set MarkerEvent
    google.maps.event.addListener(marker,'click',function(event){
        $('html, body').stop().animate({
            scrollTop: $newWaypoint.offset().top 
            }, 500,function(){
                $newWaypoint.focus();
            });
    });

    //pair marker in waypoint
    $newWaypoint.marker = marker;
    $newWaypoint.imgs = [];
    
    // Legarcy
    // this.map.markers.push(marker);
    // $newWaypoint.data("marker-index",'Marker #'+Idx);

    Logger.waypoints.push($newWaypoint);

    //add Waypoint
    $form.append($newWaypoint);
};

JournalLogger.prototype.setWaypointReindex = function(){
    var $list = this.waypoints;

    $list.forEach(function(v,i){
        idx = i+1
        console.log(v);
        console.log(i);

        v.attr("id",idx);
        v.data("index",idx);
        v.find('#wp-name').text('Waypoint #'+idx);

        v.marker.setTitle('Marker #'+idx);
        v.marker.setLabel('W'+ idx);
    });

    return;
}

JournalLogger.prototype.handleImgsFilesSelect = function(e,$wp){
    var $target = $wp.find('.image');

    console.log($target);

    var files = e.target.files;
    var filesArr = Array.prototype.slice.call(files);

    filesArr.forEach(function(f){
        if(!f.type.match("image.*")){
            alert("Only upload Imagefiles");
            return;
        }

        $wp.imgs.push(f);

        var reader = new FileReader();
        reader.onload = function(e){
            var $newImg = $('<img/>',{
                class: 'gallary rounded float-left',
                src: e.target.result
            })

            $newImg.mouseenter(function(){
                $(this).addClass('shadow');
            });
            $newImg.mouseleave(function(){
                $(this).removeClass('shadow');
            })
            $newImg.click(function(){
                var index = $wp.imgs.indexOf(f);
                $wp.imgs.splice(index,1);
                f.remove();
            })
            
            $newImg.appendTo($target);
        }
        reader.readAsDataURL(f);

    });

};

JournalLogger.prototype.Submit =function(){
        // form data
        var FormArray = {};
        var ImgArray = [];
        var Logger = this;

        // set journey data
        FormArray.title = this.$form.find("[name=journey-title]").val();
        FormArray.description = this.$form.find("[name=journey-description]").val();
        FormArray.type = this.$form.find("[name=journey-type]").val();
        FormArray.author = this.$form.find("[name=author]").val();
        FormArray.email = this.$form.find("[name=email]").val();

        FormArray.waypoints = [];

        // set waypoint data
        this.waypoints.forEach(function(w){
            wp={};
            
            wp.id = w.find("[name=waypoint-name]").val();
            wp.name = w.find("[name=waypoint-name]").val();
            wp.description = w.find("[name=description]").val();
            wp.type = w.find("[name=waypoint-type]").val();
            wp.Lat = w.find("[name=Lat]").val();
            wp.Lng = w.find("[name=Lng]").val();

            FormArray.waypoints.push(wp);

        })

        // serialize gpx file
        var oSerializer = new XMLSerializer();
        var sXML = oSerializer.serializeToString(JLogger.gpx); 
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

            arr = JSON.parse(data);
            Logger.waypoints.forEach(function(wp,i){

                console.log(wp.imgs);

                uwid = arr['UWID'][i];
                wp.attr("UWID",uwid);
                wp.imgs.forEach(function(f){
                    img = [];
                    img.file =  f;
                    img.target = uwid;
                    ImgArray.push(img);
                })
            });

            console.log(ImgArray);

          },
          error: function(xhr,status,error){
            alert(error);
          }
        });
} 

function swap(array, i1, i2) {
    var temp = array[i2];
    array[i2] = array[i1];
    array[i1] = temp;
}