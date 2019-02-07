{{-- ReaderCover --}}
{{-- show journey thumbnail image and hack loading time..
---- Variable Given by showJourney.blade.php
---- @cover['thumbnail'] => $journey->meta->thumbnail
---- @cover['title'] => $journey->name,
---- @cover['date'] => $journey->startedAt,
---- @cover['distance'] => $journey->distance,
==}}

{{-- MODULE SCRIPT  --}}
{{-- :::HELPER FUNCTION:::
---- => const {name} = new Journey66.Section66
---- => {name}.Element : Element of the section
---- => {name}._AJAXcall ( data{method,url,data}, callback{BeforeSendFn(), CompleteFn(), SuccessFn(response), ErrorFn(response)} )
---- => {name}._Show(boolen) // toggle visibility of the Section
--}}

<script>
document.addEventListener("DOMContentLoaded", function(){

    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    function removeURLParameter(url, parameter) {
        //prefer to use l.search if you have a location/link object
        var urlparts = url.split('?');   
        if (urlparts.length >= 2) {

            var prefix = encodeURIComponent(parameter) + '=';
            var pars = urlparts[1].split(/[&;]/g);

            //reverse iteration as may be destructive
            for (var i = pars.length; i-- > 0;) {    
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                    pars.splice(i, 1);
                }
            }

            return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
        }
        return url;
    }

    let urlParam = window.location.search;
    let key = getParameterByName('key',urlParam);
    let removed = removeURLParameter(urlParam,'key');
    let UJID = window.location.pathname.split("/")[2];

    let URL = '/api/journey/'+UJID+"?key="+key;
    let msgbox = readerCover.Element.querySelector('span[name=message]');
    msgbox.textContent = "Loading Journey data...";
    readerCover._AJAXcall(
        {
            method: "GET",
            url: URL,
            data: null,
        },
        {
            SuccessFn(response){
                Journey66.Reader(response);
                msgbox.textContent = "Loading Complete";
            },
            ErrorFn(response){
                msgbox.textContent = "Error!! Something Happend!";
            },
        }
    );
});
</script>

<style>
    readerCover{
        position: sticky;
        position: -webkit-sticky;
        top: 0;
        height: 60vh;
        min-height: 500px;
        max-height: 700px;
        font: 20px;
    }

    readerCover .transparent-black{
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
    }

    readerCover .box{
        background-color: white;
        height: auto;
        opacity: .7;
        color: black;
        display: inline-block;
        margin-bottom: 1em;
        padding: 0.8em;
        margin-left: 0.5em;
        box-shadow: 10px 10px 0 0 black;
    }
    readerCover span.error{
        color: red;
    }
</style>

<div>
    <span name="title" class="box">
        <h2>{{$cover['title']}}<h2>
    </span>
</div>

<div>
    <span name="date" class="box">
        {{$cover['date']}}
    </span>
</div>

<div>
    <span name="distance" class="box">
        {{$cover['distance']}}
    </span>
</div>

<div>
    <span name="message" class="box">
        
    </span>
</div>