const Journey66 = {
    Map : null,
    Mapkey : null,
};


Journey66.setMarker = function(param = {
    latlng : null,
    map : null,
    target : null,
    Idx : null,
    title : null,
    label : null
})
{
    if(param.latlng == null) throw (new Error("Parameter latlng must require"));
    if(!param.latlng instanceof google.maps.LatLng) 
        throw (new Error("Parameter latlng must be instanceof 'google.maps.LatLng' "));
    if(param.map == null) param.map = this.Map;
    if(param.title == null) param.title = 'Marker #'+param.Idx;
    if(param.label == null) param.label = 'W'+ param.Idx;

    //get marker node
    let marker = new google.maps.Marker({
        position: param.latlng,
        map: param.map,
        title: param.title,
        label: param.label
    });
    
    //set Marker Offset Event
    let $target = $(param.target);
    google.maps.event.addListener(marker,'click',function(event){
        $('html, body').stop().animate({
            scrollTop: $target.position().top 
            }, 500,function(){
                $target.focus();
            });
    });

    return marker;
};


Journey66.calc = {
    Distance : function(string)
    {
        let km = Number(string * 0.001).toFixed(2)+"km";
        return km;
    },
    Elevation : function(string)
    {
        let m = Number(string).toFixed(1)+"m";
        return m;
    },
    LocalTime : function(lat,lng,callbackFn)
    {
        if(!lat && !lng) throw new Error("Parameter lat,lng must require");

        let url = "https://maps.googleapis.com/maps/api/timezone/json?"
            +"location="+lat
            +","+lng
            +"&timestamp="+Date.now()/1000
            +"&key="+Journey66.Mapkey;
            console.log(url);
        
        xhr = new XMLHttpRequest
        xhr.open("GET",url,true);
        xhr.onload = function(){
            if (xhr.status == 200 || xhr.status == 201){
                let data = JSON.parse(xhr.responseText);
                console.log(data);
                return callbackFn(data.timeZoneId);
            } else {
                return new Error("timezone get Error");
            }
        }
        xhr.send();
    },
};

Journey66.Section66 = function(string){
    this.Element = document.getElementsByTagName(string)[0];
    this._AJAXcall = function
        (
            data = {
                method : "GET",
                url : null,
                data : null,
            },
            Callback = {
                BeforeSendFn : Function, //callback funtion for 
                CompleteFn : Function,
                SuccessFn : Function,
                ErrorFn : Function,
            },
        )
        {
            let xhr = new XMLHttpRequest();
            xhr.open(data.method,data.url,true);
            xhr.onload = function(){
                if (xhr.status == 200 || xhr.status == 201) {
                    // AJAX success
                    let data = JSON.parse(xhr.responseText);
                    Callback.SuccessFn(data);
                } else {
                    // AJAX error
                    throw new Error("AJAX call failure");
                    Callback.ErrorFn(data);
    
                };
                // AJAX complete
                Callback.CompleteFn();
            };
            // AJAX beforeSend
            Callback.BeforeSendFn();
    
            // AJAX send
            xhr.send(data.data);
        };
    this._Show = function(val = true)
        {
            if(val){
                this.Element.style.display = "block";
            } else {
                this.Element.style.display = "none";
            }
        };
};