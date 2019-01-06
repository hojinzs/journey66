// Waypoint Marker

var JournalLogger = function(map){
    this.map = map;
    this.waypoints = [];
    this.path = [];
    this.zoom = this.map.getZoom();
    this.sequence = [];

    //track setting
    this.trackcolour = "#ff00ff"; // red
    this.trackwidth = 3;
    this.mintrackpointdelta = 0.0001

};

JournalLogger.prototype.setForm = function(form_id){
    this.$form=$(form_id);

    this.$form.show();
    this.$waypointlist = this.$form.find('#waypoint-list');
    this.$dummywp = $('#DUMMY');
    this.$postingModal = $('#journeyPosted');
};

JournalLogger.prototype.setSequence = function($sequence = {}){
    this.sequence = $sequence;
}

JournalLogger.prototype.CreateJourney = function(){
    var Logger = this;

    this.$form.on("submit", function(event) {
        event.preventDefault();

        var loading = $('.img-loading').length;
        if(loading > 0) {
            alert(loading+'images upload is unfinished');
            return;
        };

        Logger.SubmitNew();
    });

}

JournalLogger.prototype.UpdateJourney = function(){
    var Logger = this;

    this.$form.on("submit", function(event) {
        event.preventDefault();

        var loading = $('.img-loading').length;
        if(loading > 0) {
            alert(loading+'images upload is unfinished');
            return;
        };

        Logger.SubmitUpdate();
    });

}

JournalLogger.prototype.DeleteJourney = function($target){
    var Logger = this;

    $target.on("click", function(event) {
        event.preventDefault();

        var loading = $('.img-loading').length;
        if(loading > 0) {
            alert(loading+'images upload is unfinished');
            return;
        };

        Logger.SubmitDelete();
    });

}

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
        console.log(event);
        var plat = event.latLng.lat();
        var plng = event.latLng.lng();
        var point = new google.maps.LatLng(plat,plng);

        Logger.NewWaypoint(point);
        
    });

}

JournalLogger.prototype.NewWaypoint = function(latlng){
    var Logger = this;
    var $form = this.$waypointlist;
    var $dummywp = this.$dummywp;

    //get Near Node
    node = Journal.findSequenceNode(latlng,this.sequence);
    console.log(node);


    //get last waypoint node
    var Idx = this.waypoints.length + 1

    //set NewWaypoint
    var $newWaypoint = $dummywp.clone(true);
    $newWaypoint.addClass('waypoint');
    $newWaypoint.attr("id",Idx);
    $newWaypoint.attr("name",Idx);
    $newWaypoint.data("index",Idx);
    $newWaypoint.data("mode",'new');
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

    $StaticMap = $newWaypoint.find('#static-map');
    Journal.setStaticMap($StaticMap,{
        width : "250",
        height : "250",
        zoom : this.zoom,
        lat : latlng.lat(),
        lng : latlng.lng(),
        encpath : this.$form.data('summary-polyline'),
    });

    marker = new Journal.setMarker(map,$newWaypoint,Idx,latlng);

    $newWaypoint.marker = marker;
    $newWaypoint.imgs = [];

    Logger.waypoints.push($newWaypoint);

    //add Waypoint
    $form.append($newWaypoint);

    var timer = setTimeout(function(){
        $('html, body').stop().animate({
            scrollTop: $newWaypoint.offset().top 
            }, 500,function(){
                $newWaypoint.focus();
                clearTimeout(timer);
        });
    },700);
};

JournalLogger.prototype.setWaypointReindex = function(){
    var $list = this.waypoints;

    var idx = 0;
    $list.forEach(function(v,i){

        switch (v.data('mode')) {
            case 'del':
                v.attr("id",'');
                v.data("index",'');
                v.find('#wp-name').text('Deleted');

                v.marker.setTitle('delete');
                v.marker.setLabel('Del');
                break;

            case 'edit':
                idx = idx +1;

                v.attr("id",idx);
                v.data("index",idx);
                v.find('#wp-name').text('Waypoint #'+idx);

                v.marker.setTitle('Marker #'+idx);
                v.marker.setLabel('W'+ idx);
                break;

            case 'new':
                idx = idx +1;

                v.attr("id",idx);
                v.data("index",idx);
                v.find('#wp-name').text('Waypoint #'+idx);

                v.marker.setTitle('Marker #'+idx);
                v.marker.setLabel('W'+ idx);
                break;
        
            default:
                break;
        }
    });

    return;
}

JournalLogger.prototype.handleImgsFilesSelect = function(e,$wp){
    var $newImg = $('<img/>',{
        class: 'gallary rounded float-left',
    })

    var $target = $wp.find('.image');
    var files = e.target.files;
    var f = files[0];

    if(!f.type.match("image.*")){
        alert("Only upload Imagefiles");
        return;
    }

    // Set formdata
    var filedata = new FormData(); // FormData 인스턴스 생성
    filedata.append('image', f);

    $.ajax({
        url: "/api/imageuploader",
        type: "POST",
        data: filedata,
        contentType: false,
        processData: false,
        beforeSend: function(){
            $newImg.appendTo($target);
            $newImg.addClass('img-loading');
            $newImg.attr('src',"https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif");
        },
        success: function(data){
            $newImg.attr('src',data.url);
    
            $newImg.mouseenter(function(){
                $(this).addClass('shadow');
            });
            $newImg.mouseleave(function(){
                $(this).removeClass('shadow');
            })
            $newImg.click(function(){
                var index = $wp.imgs.indexOf(f);
                $wp.imgs.splice(index,1);
                $(this).remove();
            })

            var img = [];
            
            img.$img = $newImg;
            img.src = data.url;
            img.path = data.filename;
            img.type = 'tmp';
            
            $wp.imgs.push(img);
        },
        error: function(xhr,status,error){
            $newImg.remove();
            alert(error);
        },
        complete: function(){
            $newImg.removeClass('img-loading');
        }
    });

};

JournalLogger.prototype.SubmitNew = function(){
        // form data
        var FormArray = {};
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
            var wp={};
            wp.imgs = [];
            
            wp.mode = w.data('mode');
            wp.id = w.find("[name=waypoint-name]").val();
            wp.name = w.find("[name=waypoint-name]").val();
            wp.description = w.find("[name=description]").val();
            wp.type = w.find("[name=waypoint-type]").val();
            wp.Lat = w.find("[name=Lat]").val();
            wp.Lng = w.find("[name=Lng]").val();

            // set image data
            w.imgs.forEach(function(f){
                img = {};
                img.url = f.$img.currentSrc;
                img.path = f.path;
                img.type = f.type;

                wp.imgs.push(img);
            })

            FormArray.waypoints.push(wp);

        })

        // set track files
        FormArray.polyline = this.$form.data('polyline');
        FormArray.gpx = this.$form.data('gpx');

        // set Static img
        summary_map = $("#journeyPosted .summary-map");
        FormArray.staticmap = Journal.setStaticMap(summary_map,{
            width : "500",
            height : "500",
            encpath : this.$form.data('summary-polyline')
        })
    
        // ready to json
        var jsonData = JSON.stringify(FormArray);
    
        //send
        $.ajax({
          url: "/api/newjourney",
          type: "POST",
          contentType: "application/json",
          data: jsonData,
          dataType: "text",
          beforeSend: function(){
            Logger.$form.find('[type=submit]').prop("disabled",true);
            Logger.$postingModal.find('modal-message-done').hide();
            Logger.$postingModal.find('modal-message-error').hide();
            Logger.$postingModal.modal({
                backdrop: 'static',
                keyboard: false,
            });
          },
          success: function(data){
            // alert(data);
            parse = JSON.parse(data);
            Logger.$form.remove();
            Logger.$postingModal.find('author-email').text(parse.mail);
            Logger.$postingModal.find('modal-message-done').show();
          },
          error: function(xhr,status,error){
            // alert(error);
            Logger.$postingModal.find('modal-message-error').show();
            Logger.$postingModal.find('modal-message-error').text(error);
          },
          complete: function(){
            Logger.$postingModal.find('modal-message-loading').hide();
          },
        });
} 

JournalLogger.prototype.setWaypoint = function(waypoint){
    var Logger = this;
    var $target = $(waypoint);
    var Idx = $target.attr("id");

    $target.data('mode','edit');

    latitude = $target.find("[name=Lat]").val();
    longitude = $target.find("[name=Lng]").val();
    LatLng = new google.maps.LatLng(latitude,longitude);

    //set Static Map
    smap = $target.find('#static-map');
    Journal.setStaticMap(smap,{
    width : "300",
    height : "300",
    zoom : map.getZoom(),
    lat : latitude,
    lng : longitude,
    encpath : this.$form.data('summary-polyline'),
    });

    //set Marker
    marker = new Journal.setMarker(map,$target,Idx,LatLng);
    $target.marker = marker;

    //set NewWaypointEvent
    $target.find('#waypoint-delete').on('click',function(e){
        $target.data('mode','del');
        $target.marker.setOptions({'opacity': 0.5});
        $target.addClass('delete');
        $target.find('#waypoint-undelete').show();
        $target.find('#waypoint-delete').hide();

        Logger.setWaypointReindex();
    });
    $target.find('#waypoint-undelete').on('click',function(e){
        $target.data('mode','edit');
        $target.marker.setOptions({'opacity': 1});
        $target.removeClass('delete');
        $target.find('#waypoint-undelete').hide();
        $target.find('#waypoint-delete').show();

        Logger.setWaypointReindex();
    });
    $target.find('#waypoint-up').on('click',function(e){
        var oi = Logger.waypoints.indexOf($target);
        if (oi == 0){return;}

        var ti = oi - 1;
        swap(Logger.waypoints,oi,ti);
        Logger.setWaypointReindex();
        
        $target.after($target.prev());        
        $('html, body').stop().animate({
            scrollTop: $target.offset().top 
            }, 500);
    });
    $target.find('#waypoint-down').on('click',function(e){
        var oi = Logger.waypoints.indexOf($target);
        if (oi == Logger.waypoints.length-1){return;}

        var ti = oi + 1;
        swap(Logger.waypoints,oi,ti);
        Logger.setWaypointReindex();

        $target.before($target.next());
        $('html, body').stop().animate({
            scrollTop: $target.offset().top 
            }, 500);
    });
    $target.find('#input_img').on('change',function(e){
        Logger.handleImgsFilesSelect(e,$target);
    });

    //set Waypoint images
    $imgArr = $target.find('.image').children('img');
    $target.imgs = Journal.setCurrentImageArr($imgArr);

    //push Waypoint Array
    Logger.waypoints.push($target);
}

JournalLogger.prototype.SubmitUpdate = function(){
    // form data
    var FormArray = {};
    var Logger = this;
    var UJID = this.$form.data('ujid');

    // set journey data
    FormArray.UJID = UJID;
    FormArray.key = this.$form.data('key');
    FormArray.title = this.$form.find("[name=journey-title]").val();
    FormArray.description = this.$form.find("[name=journey-description]").val();
    FormArray.type = this.$form.find("[name=journey-type]").val();
    FormArray.author = this.$form.find("[name=author]").val();
    FormArray.email = this.$form.find("[name=email]").val();
    FormArray.publish_stage = this.$form.find("input[name=publish_stage]:checked").val();

    // set waypoint data
    FormArray.waypoints = [];
    this.waypoints.forEach(function(w){
        var wp={};
        
        wp.uwid = w.data('uwid');
        wp.mode = w.data('mode');
        wp.id = w.find("[name=waypoint-name]").val();
        wp.name = w.find("[name=waypoint-name]").val();
        wp.description = w.find("[name=description]").val();
        wp.type = w.find("[name=waypoint-type]").val();
        wp.Lat = w.find("[name=Lat]").val();
        wp.Lng = w.find("[name=Lng]").val();

        // set image data
        wp.imgs = [];
        w.imgs.forEach(function(f){
            img = {};
            img.id = f.id;
            img.url = f.$img.currentSrc;
            img.path = f.path;
            img.type = f.type;

            wp.imgs.push(img);
        })

        FormArray.waypoints.push(wp);

    })

    // ready to json
    var jsonData = JSON.stringify(FormArray);
    console.log(FormArray);

    //send
    $.ajax({
        url: "/api/editjourney/"+UJID,
        type: "POST",
        contentType: "application/json",
        data: jsonData,
        dataType: "text",
        beforeSend: function(){
            Logger.$form.find('[type=submit]').prop("disabled",true);
            Logger.$postingModal.find('modal-message-done').hide();
            Logger.$postingModal.find('modal-message-error').hide();
            Logger.$postingModal.modal({
                backdrop: 'static',
                keyboard: false,
            });
          },
        success: function(data){
            parse = JSON.parse(data);
            Logger.$form.remove();
            Logger.$postingModal.find('author-email').text(parse.mail);
            Logger.$postingModal.find('modal-message-done').show();
          },
          error: function(xhr,status,error){
            // alert(error);
            Logger.$postingModal.find('modal-message-error').show();
            Logger.$postingModal.find('modal-message-error').text(error);
          },
          complete: function(){
            Logger.$postingModal.find('modal-message-loading').hide();
          },
    });
}


JournalLogger.prototype.SubmitDelete = function(){
    var UJID = this.$form.data('ujid');

    $.ajax({
        url:"/api/deletejourney/"+UJID,
        method: "DELETE",
        contentType: "application/json",
        dataType: "text",
        success: function(data){
            alert(data);
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

var Journal = {};

Journal.setStaticMap = function(target = null,param = {}){
    
    var staticmap = "https://maps.googleapis.com/maps/api/staticmap?"
        +"&size="+param.width+"x"+param.height
        +"&scale=2";

    if(param.zoom){   
        staticmap = staticmap 
        +"&zoom=" + (param.zoom + 1);
    }

    if(param.lat && param.lng){
    staticmap = staticmap
        +"&center="+param.lat+","+param.lng
        +"&markers=color:red|"+param.lat+","+param.lng;
    };

    if(param.encpath){
        var color = "0xff0000ff";
        var width = 3;
        staticmap = staticmap + "&path=weight:" + width + "%7Ccolor:"+ color + "%7Cenc:"+ param.encpath;
    }
;
    staticmap = staticmap +"&key=" + gMapKey;

    if(target){
        $(target).attr('src',staticmap);
    }

    return staticmap;
}

Journal.setGallary = function(target){
  $.each(target,function(k,v){
    // console.log(v);
  })
};

Journal.setMarker = function(map,target,Idx,latlng){
    var $target = target

    //get marker node
    marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: 'Marker #'+Idx,
        label: 'W'+ Idx
    });
    
    //set MarkerEvent
    google.maps.event.addListener(marker,'click',function(event){
        $('html, body').stop().animate({
            scrollTop: $target.position().top 
            }, 500,function(){
                $target.focus();
            });
    });

    return marker;
};

Journal.setCurrentImageArr = function(Arr = {}){
    var imgs = [];

    $.each(Arr,function(key,val){
        $img = $(val);
        var img = [];

        $img.mouseenter(function(){
            $(this).addClass('shadow');
        });
        $img.mouseleave(function(){
            $(this).removeClass('shadow');
        })
        $img.click(function(){
            if(img.type == 'cur'){
                img.type = 'del';
                $(this).addClass('delete');
            } else {
                img.type = 'cur';
                $(this).removeClass('delete');
            };
        })
        
        img.$img = $img;
        img.id = $img.data('imgid');
        img.src = $img.attr('src');
        img.path = $img.attr('src');
        img.type = 'cur';

        imgs.push(img);

    });

    return imgs;

}

Journal.findSequenceNode = function(LatLng={},Arr=[]){

    var vs = null;
    var gatcha;
    Arr.forEach(point => {
        var vslt = point.latitude - LatLng.lat();
        var vsln = point.longitude - LatLng.lng();
        abs = Math.abs(vslt + vsln);
        if(vs == null || vs > abs){
            vs = abs;
            gatcha = point;
        };
    });

    return gatcha;

}