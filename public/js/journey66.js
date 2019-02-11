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
    
    //set Marker Scrolling Event //using jQuery. replace later..
    let $target = $(param.target);
    google.maps.event.addListener(marker,'click',function(event){
        $('html, body').stop().animate({
            scrollTop: $target.offset().top 
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
        
        xhr = new XMLHttpRequest
        xhr.open("GET",url,true);
        xhr.onload = function(){
            if (xhr.status == 200 || xhr.status == 201){
                let data = JSON.parse(xhr.responseText);
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
                header : null,
            },
            Callback = {
                BeforeSendFn : Function,
                CompleteFn : Function,
                SuccessFn : Function,
                ErrorFn : Function,
            },
        )
        {
            let xhr = new XMLHttpRequest();
            xhr.open(data.method,data.url,true);
            if(data.header){ //set Header
                let headers = Object.keys(data.header);
                for (let i= 0; i < headers.length; i++){
                    let key = headers[i];
                    xhr.setRequestHeader(key,data.header[key]);
                }
            }
            xhr.onload = function(){
                // AJAX complete
                if(Callback.CompleteFn instanceof Function) Callback.CompleteFn();
                
                if (xhr.status == 200 || xhr.status == 201) {
                    // AJAX success
                    let response = JSON.parse(xhr.responseText);
                    if(Callback.SuccessFn instanceof Function) Callback.SuccessFn(response);
                } else {
                    // AJAX error
                    let response = xhr.responseText;
                    if(Callback.ErrorFn instanceof Function) Callback.ErrorFn(response);
                    Journey66.ErrorHandler('AJAX call failure');
    
                };
            };
            // AJAX beforeSend
            if(Callback.BeforeSendFn instanceof Function) Callback.BeforeSendFn();
    
            // AJAX send
            if(data.data == null || data.method == "GET"){
                xhr.send();
            } else {
                xhr.send(data.data);
            };
        };
    this._Show = function(val = true)
        {
            if(val){
                this.Element.classList.remove("hidden_section");
                this.Element.disabled = false;
                // this.Element.style.display = "block";
            } else {
                this.Element.classList.add("hidden_section");
                this.Element.disabled = true;
                // this.Element.style.display = "none";
            }
        };
};

Journey66.ErrorHandler = function(message,response = null){
    throw new Error(message);
};