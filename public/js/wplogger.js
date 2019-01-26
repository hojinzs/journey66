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
    this.mintrackpointdelta = 0.0001;
};

JournalLogger.prototype.setForm = function(Elements = {
    form: null,
    waypoint_list: null,
    dummy_waypoint: null,
    journey_posted_modal: null,
}){
    this.$form=$(Elements.form);
    // this.$waypointlist = $(Elements.waypoint_list);
    this.$waypointlist = $(document.getElementById(Elements.waypoint_list));
    this.$dummywp = $(Elements.dummy_waypoint);
    this.$postingModal = $(Elements.journey_posted_modal);

    this.$form.show();
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

        // var plat = event.latLng.lat();
        // var plng = event.latLng.lng();
        var point = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());

        var node = Logger.findSequenceNode(point);

        Logger.NewWaypoint(node,{
            new: true,
            offset: true
        });
        
    });

}

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
    var Logger = this;
    var $waypointlist = this.$waypointlist;
    var $dummywp = this.$dummywp;

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
            $newWaypoint.marker = new Journal.setMarker(map,$newWaypoint,Idx,latlng,{
                title: "startingPoint",
                label: "S"
            });
            break;

        case 'destination':
            $newWaypoint.find('#waypoint-type').val('destination');
            $newWaypoint.find('#waypoint-type').children('[value=destination]').prop('disabled',false);
            $newWaypoint.find('#wp-name').text('Finish');
            $newWaypoint.find('#waypoint-type').prop('disabled', true);
            $newWaypoint.marker = new Journal.setMarker(map,$newWaypoint,Idx,latlng,{
                title: "destinationPoint",
                label: "F"
            });
            break;
    
        default:
            // set Marker & title
            $newWaypoint.marker = new Journal.setMarker(map,$newWaypoint,Idx,latlng);
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
        Logger.handleImgsFilesSelect(e,$newWaypoint);
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

    var setNewImage = function(){
        alert(test);
        return;
    };
};

JournalLogger.prototype.handleImgsFilesSelect = function(e,$wp){
    var $newImg = $('<img/>',{
        class: 'gallary rounded float-left',
    })

    var Logger = this;

    var $target = $wp.find('.image');
    var files = e.target.files;
    var f = files[0];

    if(!f.type.match("image.*")){
        alert("Only upload Imagefiles");
        return;
    }

    // Set formdata
    var filedata = new FormData(); // FormData
    filedata.append('image', f);

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
                $target : $wp,
                src : data.url,
                path : data.filename,
                type : 'tmp'
            });
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

JournalLogger.prototype.setImage = function(prop = {
    $img : null,
    $target : null,
    id : null,
    src : null,
    path : null,
    type : 'tmp'
}){
    var $target = prop.$target;

    var img = {};
    img.$img = prop.$img;
    img.id = prop.id;
    img.src = prop.src;
    img.path = prop.path;
    img.type = prop.type;

    img.$img.attr('src',img.src);

    // set Actions
    img.$img.mouseenter(function(){
        $(this).addClass('shadow');
    });
    img.$img.mouseleave(function(){
        $(this).removeClass('shadow');
    });
    img.$img.click(function(){
        console.log('Before',$target.imgs);
        // Delete image
        var delconfirm = confirm('Delete Image')
        if(delconfirm){

            var senddata = {
                target: $target,
                UWID: $target.uwid,
                imgid: img.id,
            };
            
            removeData(senddata)
                .then(eraseFile(senddata))
                .then(removeDom(senddata))
                .catch(function(error){
                    console.log('something error',error);
            });
        
            function removeData(data){
                return new Promise(function(resolve, reject){
                    if(img.type == 'cur' && img.id){
                        console.log("디비:: 삭제요");
                        var xhr = new XMLHttpRequest();
                        xhr.open('DELETE','/api/waypoint/'+data.UWID+'/image/'+data.imgid+'/delete',true);
                        xhr.onreadystatechange = function(){
                            if (xhr.readyState == xhr.DONE) {
                                if (xhr.status == 200 || xhr.status == 201) {
                                    console.log(xhr.responseText);
                                    resolve('success_DELETE');
                                } else {
                                    console.error(xhr.responseText);
                                    reject(xhr.responseText);
                                }
                            };
                        };
                        xhr.send(data);
                    } else {
                        console.log("디비:: 안삭제요");
                        resolve('nothing_to_DELETE');
                    };
                });
            };
    
            function eraseFile(){
                return new Promise(function(resolve,reject){

                    resolve('success');
                })
            };
        
            function removeDom(){
                var index = $target.imgs.indexOf(img);
                $target.imgs.splice(index,1);
                img.$img.remove();

                console.log('After',$target.imgs);
            };
        };
    });

    $target.imgs.push(img);
    return img;
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
};

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
    var Form = new FormData();
    Form.append('key','asb');

    $.ajax({
        url:"/api/journey/"+UJID+"/delete",
        method: "DELETE",
        data: JSON.stringify(Form),
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

JournalLogger.prototype.deleteWaypoint = function($Waypoint){
    var WaypointData = {
        mode: $Waypoint.data("mode"),
        uwid: $Waypoint.data("uwid"),
    };

    removeData()
        .then(removeDom)
        .catch(function(error){

    });

    function removeData(){
        return new Promise(function(resolve, reject){
            if(WaypointData.mode == 'edit'){
                console.log("데이터:: 삭제요");

                var xhr = new XMLHttpRequest();
                xhr.open('DELETE','/api/waypoint/'+WaypointData.uwid+'/delete',true);
                xhr.onreadystatechange = function(){
                    if (xhr.readyState == xhr.DONE) {
                        if (xhr.status == 200 || xhr.status == 201) {
                            console.log(xhr.responseText);
                            resolve('success_DELETE');
                        } else {
                            console.error(xhr.responseText);
                            reject(xhr.responseText);
                        }
                    };
                };
                console.log('set');
                xhr.send(WaypointData);
            } else {
                console.log("데이터:: 안삭제요");
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

            console.log("돔:: 제거요")
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
            scrollTop: $target.position().top 
            }, 500,function(){
                $target.focus();
            });
    });

    return marker;
};

Journal.deleteImage = function($img){
    var WaypointData = {
        mode: $Waypoint.data("mode"),
        uwid: $Waypoint.data("uwid"),
    };

    
    removeData()
        .then(removeDom)
        .catch(function(error){

    });

    function removeData(){
        return new Promise(function(resolve, reject){
            if(Data.mode == 'edit'){
                console.log("데이터:: 삭제요");

                var xhr = new XMLHttpRequest();
                xhr.open('DELETE','/api/waypoint/'+WaypointData.uwid+'/delete',true);
                xhr.onreadystatechange = function(){
                    if (xhr.readyState == xhr.DONE) {
                        if (xhr.status == 200 || xhr.status == 201) {
                            console.log(xhr.responseText);
                            resolve('success_DELETE');
                        } else {
                            console.error(xhr.responseText);
                            reject(xhr.responseText);
                        }
                    };
                };
                console.log('set');
                xhr.send(WaypointData);
            } else {
                console.log("데이터:: 안삭제요");
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

            console.log("돔:: 제거요")
            resolve(true);
        });
    };
}