
const JournalLogger = function(map,key=null){
    this.map = map;
    this.gMapKey = key;
    this.waypoints = [];
    this.path = [];
    this.sequence = [];
    this.journey_key = null;
};

JournalLogger.prototype.setForm = function(Elements = {
    form: null,
    waypoint_list: null,
    dummy_waypoint: null,
    journey_posted_modal: null,
    journey_key: null,
    stats: null,
}){
    this.$form=$(Elements.form);
    // this.$waypointlist = $(Elements.waypoint_list);
    this.$waypointlist = $(document.getElementById(Elements.waypoint_list));
    this.$dummywp = $(Elements.dummy_waypoint);
    this.$postingModal = $(Elements.journey_posted_modal);
    this.journey_key = Elements.journey_key;
    this.stats = Elements.stats;

    let started_at = moment.tz(this.stats.startedAt,"UTC").tz(this.stats.timezone);
    let finisted_at = moment.tz(this.stats.finishedAt,"UTC").tz(this.stats.timezone);

    // set stats
    $('#journey-stat').find("span[name='distance']").text(Journey66.calc.Distance(this.stats.distance));
    $('#journey-stat').find("span[name='elevation']").text(Journey66.calc.Elevation(this.stats.elevation));
    $('#journey').find("span[name='duration']").text(this.stats.duration);
    // $('#journey-stat').find("span[name='startedAt']").text(this.stats.startedAt);
    $('#journey-stat').find("span[name='startedAt']").text(started_at);
    $('#journey-stat').find("span[name='finishedAt']").text(finisted_at);

    this.$form.show();
};

JournalLogger.prototype.setSequence = function(sequence = {}){

    let track = [];
    sequence.forEach(function(point,i){
        let latlng = new google.maps.LatLng(point.latitude,point.longitude);
        track.push(latlng);
    })

    this.sequence = sequence;
    this.trackpoint = track;
}

JournalLogger.prototype.centerAndZoom = function(sequence = null) {
    if(sequence == null) sequence = this.sequence;

    // initalize data
    var minlat = sequence[0].latitude;
    var maxlat = sequence[0].latitude;
    var minlon = sequence[0].longitude;
    var maxlon = sequence[0].longitude;

    // find min & max 
    sequence.forEach(function(point,i){
        if(point.longitude < minlon) minlon = point.longitude;
        if(point.longitude > maxlon) maxlon = point.longitude;
        if(point.latitude < minlat) minlat = point.latitude;
        if(point.latitude > maxlat) maxlat = point.latitude;
    });

    if((minlat == maxlat) && (minlat == 0)) {
        this.map.setCenter(new google.maps.LatLng(49.327667, -122.942333), 14);
        return;
    }

    // Center around the middle of the points
    var centerlon = (maxlon + minlon) / 2;
    var centerlat = (maxlat + minlat) / 2;

    var bounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(minlat, minlon),
            new google.maps.LatLng(maxlat, maxlon));
    this.map.setCenter(new google.maps.LatLng(centerlat, centerlon));
    this.map.fitBounds(bounds,15);
    this.zoom = this.map.getZoom();
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

JournalLogger.prototype.TrackMarker = function(prop = {
    map : null,
    track : null,
    marker : null,
    color : null,
    width : null,
}){
    // set Default Properties
    if(prop.color == null) prop.color = "#ff0000";
    if(prop.width == null) prop.width = 3;
    if(prop.track == null) prop.track = this.trackpoint;
    if(prop.marker == null) prop.marker = true;
    if(prop.map == null) prop.map = this.map;

    let Logger = this;

    let baseline = new google.maps.Polyline({
        path: prop.track,
        strokeColor: prop.color,
        strokeWeight: prop.width,
        map: prop.map,
        visible: true,
        zIndex: 1
    });

    if(prop.marker == false) return;

    let polyline = new google.maps.Polyline({
        path: prop.track,
        strokeColor: prop.color,
        strokeOpacity: 0,
        strokeWeight: (prop.width*5),
        map: prop.map,
        visible: true,
        zIndex: 5
    });

    this.Polyline = polyline;

    google.maps.event.addListener(polyline,"mouseover",function(event){

        polyline.setOptions({strokeOpacity: 0.3});

        // draw circle
        var testCircle = new google.maps.Circle({
          strokeColor: '#0099ff',
          strokeOpacity: 1,
          strokeWeight: 10,
          fillColor: '#0099ff',
          fillOpacity: 0.5,
          map: prop.map,
          center: event.latLng,
          radius: (prop.width*5),
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
        var point = new google.maps.LatLng(
            event.latLng.lat(),
            event.latLng.lng()
        );
        var node = Logger.findSequenceNode(point);
        Logger.NewWaypoint(node,{
            new: true,
            offset: true
        });
    });

};

JournalLogger.prototype.purgeTrackMarker = function(){
    google.maps.event.clearListeners(this.Polyline,'mouseover');
    google.maps.event.clearListeners(this.Polyline,'click');
}


///////////////////////////
// ** GeoPhotoUploader **
// > Set PhotoUploader for place waypoint by photo exif geotaggind data
// * dependency::  Javascript Load Image & Geopoint js (Reference:: /ref/imgeo_js)
///////////////////////////
JournalLogger.prototype.GeoPhotoUploader = function(Elements={
    button_id: null,
    input_id: null,
    modal_id: null,
}){
    var Logger = this;
    var button = document.getElementById(Elements.button_id);
    var fileinput = document.getElementById(Elements.input_id);
    var result = {
        file : null,
        img : null,
        lat : null,
        lon : null,
    };

    // Set confirm Modal
    this.$confirmGeophotoModal = $(document.getElementById(Elements.modal_id));
    this.$confirmGeophotoModal.on('hidden.bs.modal', function (e) {
        // clear value & modal
        fileinput.value = "";
        Logger.$confirmGeophotoModal.find("#Geophoto_img").empty();
        Logger.$confirmGeophotoModal.find("#Geophoto_img").empty();
    });
    this.$confirmGeophotoModal.find("#GeophotoSet").click(function(){
        Logger.setWaypointByGeoPhoto(
            result.file,
            result.lat,
            result.lon
        ,function(data) {
            Logger.$confirmGeophotoModal.modal("hide");
            return;
        });
    });

    button.addEventListener("click",function(){
        fileinput.dispatchEvent(new MouseEvent('click'));
    });
    
    fileinput.addEventListener("change",function(event){
        result.file = event.target.files[0];

        var loadingImage = loadImage(
            result.file,
            function(img){
                //get Exif Data
                loadImage.parseMetaData(
                    result.file,
                    function (data) {
                        if (!data.imageHead) {
                            alert("cannot find EXIF meta");
                            return;
                        };
                        let DMSlat = data.exif.get('GPSLatitude');
                        let DMSlon = data.exif.get('GPSLongitude');

                        if(!DMSlon || !DMSlat){
                            alert("cannot find GPS meta");
                            return;
                        };

                        var point = new GeoPoint(
                            DMSlon[0]+"° "+DMSlon[1]+"'"+DMSlon[2]+'"',
                            DMSlat[0]+"° "+DMSlat[1]+"'"+DMSlat[2]+'"',
                        );
                        result.lat = point.getLatDec().toFixed(8);
                        result.lon = point.getLonDec().toFixed(8);
                        result.img = img;

                        return ShowConfirmModal();
                    },
                    {
                        maxMetaDataSize: 262144,
                        disableImageHead: false
                    }
                );
            },
            {
                maxWidth: 600,
                orientation: true,
            }
        );
        if (!loadingImage) {
            alert('error!');
        };
    });

    // confirm modal show
    function ShowConfirmModal(){
        Logger.$confirmGeophotoModal.find("#Geophoto_img").append(result.img);
        var StaticMapImg = new Image();
        StaticMapImg.src = Journal.setStaticMapURL({
            width: 600,
            height: 400,
            lat: result.lat,
            lng: result.lon,
            marker: true,
            key: Logger.gMapKey,
        });
        Logger.$confirmGeophotoModal.find("#Geophoto_img").append(StaticMapImg);
        Logger.$confirmGeophotoModal.modal('show');
    };
}

///////////////////////////
// ** setWaypointByGeoPhoto **
// > Set Waypoint & Image from parsed exif data
// * dependency::  Javascript Load Image & Geopoint js (Reference:: /ref/imgeo_js)
///////////////////////////
JournalLogger.prototype.setWaypointByGeoPhoto = function(imgfile,lat,lon,callbackFn){
    var Logger = this;

    // Set Waypoint
    var point = new google.maps.LatLng(lat,lon);
    var node = Logger.findSequenceNode(point);
    var $Waypoint = Logger.NewWaypoint(node,{
        new: true,
        offset: true
    });

    // Upload & Set Image
    var $img = Logger.setTempImage(imgfile,$Waypoint);

    return callbackFn('success');
};

// set Starting point & Destination Waypoint
JournalLogger.prototype.setStartEndWaypoint = function(){

    var starting = this.sequence[0];
    this.NewWaypoint(starting,{
        type: 'starting'
    });

    var destination = this.sequence[this.sequence.length - 1];
    this.NewWaypoint(destination,{
        type: 'destination'
    });
};

JournalLogger.prototype.setCurrentWaypoint = function($waypoints = [])
{   
    Logger = this;
    $.each($waypoints,function(k,waypoint){
        seq = Logger.sequence[waypoint.dataset.seq];

        Logger.NewWaypoint(seq,{
            waypoint_form: waypoint,
            offset: false,
        });
    })
}

JournalLogger.prototype.NewWaypoint = function(SequencePoint = {},prop = {
    waypoint_form: null,
    type: null,
    offset: false,
})
{
    let Logger = this;
    let $waypointlist = this.$waypointlist;
    let $dummywp = this.$dummywp;
    let timezone = this.stats.timezone;

    let time = moment.tz(SequencePoint.time,"UTC").tz(timezone).format();

    var plat = SequencePoint.latitude;
    var plng = SequencePoint.longitude;
    var latlng = new google.maps.LatLng(plat,plng);

    //get last waypoint node
    var Idx = this.waypoints.length - 1

    if(prop.waypoint_form){
        // current Waypoint
        $newWaypoint = $(prop.waypoint_form);
        $newWaypoint.data("index",Idx);
        $newWaypoint.data("sequence",SequencePoint.sequence);
        $newWaypoint.data("mode",'edit');
        prop.type = $newWaypoint.find('#waypoint-type').val();
        $newWaypoint.sequence = SequencePoint.sequence;
        $newWaypoint.uwid = $(prop.waypoint_form).data('uwid');
        $newWaypoint.find("#waypoint-stat").find('span[name="distance"]').text(Journey66.calc.Distance(SequencePoint.distance));
        $newWaypoint.find("#waypoint-stat").find('span[name="elevation"]').text(Journey66.calc.Elevation(SequencePoint.elevation));
        $newWaypoint.find("#waypoint-stat").find('span[name="time"]').text(time);

    } else {
        //set NewWaypointForm
        var $newWaypoint = $dummywp.clone(true);
        $newWaypoint.data("index",Idx);
        $newWaypoint.data("sequence",SequencePoint.sequence);
        $newWaypoint.data("mode",'new');
        $newWaypoint.addClass('waypoint');
        $newWaypoint.attr("id",Idx);
        $newWaypoint.attr("name",Idx);
        $newWaypoint.find('#Lat').val(latlng.lat());
        $newWaypoint.find('#Lng').val(latlng.lng());
        $newWaypoint.find("#waypoint-stat").find('span[name="distance"]').text(Journey66.calc.Distance(SequencePoint.distance));
        $newWaypoint.find("#waypoint-stat").find('span[name="elevation"]').text(Journey66.calc.Elevation(SequencePoint.elevation));
        $newWaypoint.find("#waypoint-stat").find('span[name="time"]').text(time);
        $newWaypoint.show();

        $newWaypoint.sequence = SequencePoint.sequence;
    }

    //when setting type
    switch (prop.type) {
        case 'starting':
            $newWaypoint.find('#waypoint-type').val('starting');
            $newWaypoint.find('#waypoint-type').children('[value=starting]').prop('disabled',false);
            $newWaypoint.find('#wp-name').text('Starting');
            $newWaypoint.find('#waypoint-type').prop('disabled', true);
            $newWaypoint.marker = new Journal.setMarker(this.map,$newWaypoint,Idx,latlng,{
                title: "startingPoint",
                label: "S"
            });
            break;

        case 'destination':
            $newWaypoint.find('#waypoint-type').val('destination');
            $newWaypoint.find('#waypoint-type').children('[value=destination]').prop('disabled',false);
            $newWaypoint.find('#wp-name').text('Finish');
            $newWaypoint.find('#waypoint-type').prop('disabled', true);
            $newWaypoint.marker = new Journal.setMarker(this.map,$newWaypoint,Idx,latlng,{
                title: "destinationPoint",
                label: "F"
            });
            break;
    
        default:
            // set Marker & title
            $newWaypoint.marker = new Journal.setMarker(this.map,$newWaypoint,Idx,latlng);
            $newWaypoint.find('#wp-name').text('Waypoint #'+Idx);

            // starting&destination remove
            $newWaypoint.find('#waypoint-type').children('[value=starting]').remove();
            $newWaypoint.find('#waypoint-type').children('[value=destination]').remove();

            // deletable
            $newWaypoint.find('#waypoint-delete').show();
            $newWaypoint.find('#waypoint-delete').on('click',function(e){
                var DelYes = confirm('Delete Waypoint');
                if(DelYes){
                    Logger.deleteWaypoint($newWaypoint);
                };
            });

            break;
    }
    
    //set image Gallary
    $newWaypoint.imgs = [];
    if(prop.waypoint_form){

        // loadCurrntImgs
        $newWaypoint.find('.image').children('img')
            .each(function(key,val){
                $img = $(val);
                Logger.setImage({
                    $img : $img,
                    $target : $newWaypoint,
                    id : $img.data('imgid'),
                    src : $img.attr('src'),
                    path : $img.attr('src'),
                    type : 'cur'
                });
            });
    }
    $newWaypoint.find('#input_img').on('change',function(e){
        var imgfile = e.target.files[0];
        Logger.setTempImage(imgfile,$newWaypoint);
    });

    // set Static Map
    $StaticMap = $newWaypoint.find('#static-map');
    Journal.setStaticMap($StaticMap,{
        width : "250",
        height : "250",
        zoom : this.zoom,
        lat : latlng.lat(),
        lng : latlng.lng(),
        encpath : this.$form.data('summary-polyline'),
    });

    // set Position
    $waypointlist.append($newWaypoint); //jQuery
    // $waypointlist.appendChild($newWaypoint);
    var saved = false;
    Logger.waypoints.forEach(function(w,i){
        if(w.sequence > $newWaypoint.sequence && !saved){
            $(w[0]).before($newWaypoint);
            saved = true;
        }
    });
    Logger.waypoints.push($newWaypoint);
    Logger.ReindexWaypoints();

    // offset
    if(prop.offset){
        var timer = setTimeout(function(){
            $('html, body').stop().animate({
                scrollTop: $newWaypoint.offset().top 
                }, 500,function(){
                    $newWaypoint.focus();
                    clearTimeout(timer);
            });
        },700);
    }

    return $newWaypoint;
};

JournalLogger.prototype.setTempImage = function(imgfile,$Waypoint){
    var Logger = this;

    var $newImg = $('<img/>',{
        class: 'gallary rounded float-left',
    })
    var $target = $Waypoint.find('.image');

    if(!imgfile.type.match("image.*")){
        alert("Only upload Imagefiles");
        return;
    }

    // set formdata
    var filedata = new FormData();
    filedata.append('image', imgfile);

    $.ajax({
        url: "/api/image/upload",
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
            Logger.setImage({
                $img : $newImg,
                $target : $Waypoint,
                src : data.url,
                path : data.filename,
                type : 'tmp'
            });

            return $newImg;
        },
        error: function(xhr,status,error){
            $newImg.remove();
            alert(error);

            return "fail";
        },
        complete: function(){
            $newImg.removeClass('img-loading');
        }
    });

};

JournalLogger.prototype.setImage = function(prop = {
    $img : null,
    $target : null,
    id : null,
    src : null,
    path : null,
    type : 'tmp'
    },callbackFn)
    {
    var $target = prop.$target;
    var journey_key = this.journey_key;

    var img = {};
    img.$img = prop.$img;
    img.id = prop.id;
    img.src = prop.src;
    img.path = prop.path;
    img.type = prop.type;

    loadImage(
        img.src,
        function(image){
            let $canvas = $(image);
            $wrapped = setCanvasEvent($canvas);
            img.$img.replaceWith($wrapped);
            img.$img = $canvas;
            $target.imgs.push(img);
            if(callbackFn == Function && callbackFn){
                return callbackFn(img);
            };
        },
        {
            orientation: true,
            canvas: true,
        }
    );

    function setCanvasEvent($canvas){
        let $Wrapper = $('<div/>',{
            class: 'waypoint-image-wrapper',
        });
        $Wrapper.append($canvas);

        $delMessage = $('<span />').text("DEL")
        $delMessage.addClass('img-delete')
        $delMessage.css({
            'position' : 'absolute',
            'top' : '5px',
            'right' : '5px',
            'padding-left' : '5px',
            'padding-right' : '5px',
            'color' : 'white',
            'font-weight' : 'bold',
            'background-color' : 'red',
        });

        $canvas.addClass('gallary rounded');
        // set Actions
        $Wrapper.mouseenter(function(){
            $(this).addClass('shadow');
            $(this).append($delMessage.clone());
            // $(this).after($delMessage.clone());
        });
        $Wrapper.mouseleave(function(){
            $(this).removeClass('shadow');
            $(this).children('.img-delete').remove();
        });
        $Wrapper.click(function(){
            // Delete image
            var delconfirm = confirm('Delete Image')
            if(delconfirm){

                var senddata = {
                    target: $target,
                    imgid: img.id,
                    UWID: $target.uwid,
                    key: journey_key,
                };

                removeData(senddata)
                .then(eraseFile(senddata))
                .then(removeDom(senddata))
                .catch(function(error){
                    Journey66.ErrorHandler('error',error);
                });

                function removeData(data){
                    return new Promise(function(resolve, reject){
                        if(img.type == 'cur' && img.id){
                            let xhr = new XMLHttpRequest();
                            xhr.open('DELETE','/api/waypoint/'+data.UWID+'/image/'+data.imgid+'/delete',true);
                            xhr.onreadystatechange = function(){
                                if (xhr.readyState == xhr.DONE) {
                                    if (xhr.status == 200 || xhr.status == 201) {
                                        resolve('success_DELETE');
                                    } else {
                                        reject(xhr.responseText);
                                    }
                                };
                            };
                            xhr.setRequestHeader("Content-Type", "application/json");
                            xhr.setRequestHeader("uwid", data.UWID);
                            xhr.setRequestHeader("key", data.key);
                            xhr.send();
                        } else {
                            resolve('nothing_to_DELETE');
                        };
                    });
                };
            
                function removeDom(){
                    let index = $target.imgs.indexOf(img);
                    $target.imgs.splice(index,1);
                    // img.$img.remove();
                    $Wrapper.remove();
                };
            
                function eraseFile(){
                    return new Promise(function(resolve,reject){
            
                        resolve('success');
                    })
                };
            };
        });

        return $Wrapper;
    };
};

JournalLogger.prototype.SubmitNew = function(callbackFn = null){
        // form data
        var FormArray = {};
        var Logger = this;

        // set journey data
        FormArray.title = this.$form.find("[name=journey-title]").val();
        FormArray.description = this.$form.find("[name=journey-description]").val();
        FormArray.type = this.$form.find("[name=journey-type]").val();
        FormArray.author = this.$form.find("[name=author]").val();
        FormArray.email = this.$form.find("[name=email]").val();
        FormArray._token = this.$form.find("[name=_token]").val();

        FormArray.waypoints = [];

        // set waypoint data
        this.waypoints.forEach(function(w){
            var wp={};
            wp.imgs = [];
            
            wp.mode = w.data('mode');
            wp.sequence = w.data('sequence');
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
        FormArray.polyline = this.$form.data('encoded-polyline');
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
            url: "/api/journeynew",
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
                
                if(callbackFn instanceof Function) return callbackFn(parse);
            },
            error: function(xhr,status,error){
                // alert(error);
                Logger.$postingModal.find('modal-message-error').show();
                Logger.$postingModal.find('modal-message-error').text(error);
            },
            complete: function(){
                Logger.$postingModal.find('modal-message-loading').hide();
                Logger.$postingModal.find('#journeyPosted_close').attr('disabled',false);
            },
        });
};

JournalLogger.prototype.SubmitUpdate = function(CallbackFn = null){
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
        wp.sequence = w.data('sequence');
        // wp.id = w.find("[name=waypoint-name]").val();
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

    //send
    $.ajax({
        url: "/api/journey/"+UJID+"/edit",
        type: "POST",
        contentType: "application/json",
        data: jsonData,
        headers: {
            'UJID':UJID,
            'key':Logger.journey_key,
        },
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
            
            Logger.$postingModal.find('author-email').text(parse.mail);
            Logger.$postingModal.find('modal-message-done').show();
            Logger.$form.remove();

            if(CallbackFn instanceof Function) return CallbackFn(parse);
          },
          error: function(xhr,status,error){
            // alert(error);
            Logger.$postingModal.find('modal-message-error').show();
            Logger.$postingModal.find('modal-message-error').text(error);
          },
          complete: function(){
            Logger.$postingModal.find('modal-message-loading').hide();
            Logger.$postingModal.find('#journeyPosted_close').attr('disabled',false);
          },
    });
}


JournalLogger.prototype.SubmitDelete = function(){

    var delConfirm = confirm('Delete Journey');
    if(!delConfirm){return};

    var UJID = this.$form.data('ujid');
    var key = this.journey_key;

    //send ajax DELETE
    var xhr = new XMLHttpRequest();
    xhr.open('DELETE','/api/journey/'+UJID+'/delete',true);
    xhr.onreadystatechange = function(){
        if (xhr.readyState == xhr.DONE) {
            if (xhr.status == 200 || xhr.status == 201) {
                alert('success');
                location.replace("/");
            } else {
                alert(xhr.responseText);
            }
        };
    };
    xhr.setRequestHeader("UJID", UJID);
    xhr.setRequestHeader("key", key);
    xhr.send();
}

JournalLogger.prototype.deleteWaypoint = function($Waypoint){
    var WaypointData = {
        mode: $Waypoint.data("mode"),
        uwid: $Waypoint.data("uwid"),
    };
    var journey_key = this.journey_key;
    var senddata = {
        UWID: $Waypoint.uwid,
        key: journey_key,
    };

    removeData(senddata)
        .then(removeDom)
        .catch(function(error){

    });

    function removeData(data){
        return new Promise(function(resolve, reject){
            if(WaypointData.mode == 'edit'){
                var xhr = new XMLHttpRequest();
                xhr.open('DELETE','/api/waypoint/'+WaypointData.uwid+'/delete',true);
                xhr.onreadystatechange = function(){
                    if (xhr.readyState == xhr.DONE) {
                        if (xhr.status == 200 || xhr.status == 201) {
                            resolve('success_DELETE');
                        } else {
                            reject(xhr.responseText);
                        }
                    };
                };
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.setRequestHeader("uwid", data.UWID);
                xhr.setRequestHeader("key", data.key);
                xhr.send();
            } else {
                resolve('nothing_to_DELETE');
            };
        });
    };

    function removeDom(){
        return new Promise(function(resolve, reject){
            $Waypoint.detach();
            $Waypoint.marker.setMap(null);
            var index = Logger.waypoints.indexOf($Waypoint);
            Logger.waypoints.splice(index,1);
            Logger.ReindexWaypoints();

            resolve(true);
        });
    };
}

JournalLogger.prototype.findSequenceNode = function(LatLng={}){
    var sequence = this.sequence;
    var vs;
    var gatcha = {};
    sequence.forEach(point => {
        var vslt = Math.pow(point.latitude,2) - Math.pow(LatLng.lat(),2);
        var vsln = Math.pow(point.longitude,2) - Math.pow(LatLng.lng(),2);
        abs = Math.abs(vslt) + Math.abs(vsln);
        if(vs == null || vs > abs){
            vs = abs;
            gatcha = point;
        };
    });
    return gatcha;
}

JournalLogger.prototype.ReindexWaypoints = function(){
    var list = this.waypoints;

    list.sort(function(a,b){
        return a.sequence < b.sequence ? -1 : a.sequence > b.sequence ? 1 : 0;
    });

    list.forEach(function(v,i){
        if(i == 0 || list.length-1 == i){
        } else {
            switch (v.data('mode')) {
                case 'del':
                    v.attr("id",'');
                    v.data("index",'');
                    v.find('#wp-name').text('Deleted');
        
                    v.marker.setTitle('delete');
                    v.marker.setLabel('Del');
                    break;
        
                case 'edit':
        
                    v.attr("id",i);
                    v.data("index",i);
                    v.find('#wp-name').text('Waypoint #'+i);
        
                    v.marker.setTitle('Marker #'+i);
                    v.marker.setLabel('W'+ i);
                    break;
        
                case 'new':
        
                    v.attr("id",i);
                    v.data("index",i);
                    v.find('#wp-name').text('Waypoint #'+i);
        
                    v.marker.setTitle('Marker #'+i);
                    v.marker.setLabel('W'+ i);
                    break;
            
                default:
                    break;
            };
        }
    });

}

const Journal = {};

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
    staticmap = staticmap +"&key=" + Journey66.Mapkey;

    if(target){
        $(target).attr('src',staticmap);
    }

    return staticmap;
}

Journal.setMarker = function(map,target,Idx,latlng,prop = {
    title: 'Marker #'+Idx,
    label: 'W'+ Idx,
}){
    var $target = target
    //get marker node
    marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: prop.title,
        label: prop.label
    });
    
    //set Marker Offset Event
    google.maps.event.addListener(marker,'click',function(event){
        $('html, body').stop().animate({
            scrollTop: $target.offset().top 
            }, 500,function(){
                $target.focus();
            });
    });

    return marker;
};

Journal.setStaticMapURL = function(param={
    width: 300,
    height: 300,
    zoom: 10,
    lat: null,
    lng: null,
    marker: false,
    encpath: null,
    key: null,
}){
    var staticmap = "https://maps.googleapis.com/maps/api/staticmap?"
    +"&size="+param.width+"x"+param.height
    +"&scale=2";

    //set Zoom Level
    if(param.zoom)
    {
        staticmap = staticmap
        +"&zoom=" +param.zoom;
    } else {
        staticmap = staticmap
        +"&zoom="+10;
    };

    //set Marker
    if(param.lat && param.lng && param.marker)
    {
        staticmap = staticmap
        +"&markers=color:red|"+param.lat+","+param.lng;
    } else {
        staticmap = staticmap
        +"&center="+param.lat+","+param.lng;
    };

    //set Path
    if(param.encpath)
    {
        var color = "0xff0000ff";
        var width = 3;
        staticmap = staticmap + "&path=weight:" + width + "%7Ccolor:"+ color + "%7Cenc:"+ param.encpath;
    };

    //finally, add Key And Complete request URL
    staticmap = staticmap
    +"&key=" + param.key;

    return staticmap;
};