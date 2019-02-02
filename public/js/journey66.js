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
    if(param.title == null) param.title = 'Marker #'+Idx;
    if(param.label == null) param.label = 'W'+ Idx;

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