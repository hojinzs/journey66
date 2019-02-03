@extends('layout.section66',[
    'name' => 'getTrack', //Name is using for Tag Name
    'background_url' => '/assets/adult-backlit-bicycle-1522545.jpg',
])

{{-- MODULE SCRIPT  --}}
{{-- :::HELPER FUNCTION:::
---- => const {name} = new Journey66.Section66
---- => {name}.Element : Element of the section
---- => {name}._AJAXcall ( data{method,url,data}, callback{BeforeSendFn(), CompleteFn(), SuccessFn(response), ErrorFn(response)} )
---- => {name}._Show(boolen) // toggle visibility of the Section
--}}
@section('scripts')
@parent
<script>
document.addEventListener("DOMContentLoaded", function(){
    let GPXbtn = document.getElementById("gpx-upload");
    let GPXuploader = document.getElementById("gpx-upload-file");
    
    // attach GPXuploader Event
    let GPXonProgress = false;
    GPXbtn.addEventListener("click",function(){
        if(GPXonProgress) return;
        GPXuploader.dispatchEvent(new MouseEvent('click'));
    });

    // upload GPX and get parsing data
    GPXuploader.addEventListener('change',function(event){
        let description = GPXbtn.getElementsByTagName('description');
        let loading = GPXbtn.getElementsByTagName('loading');

        let file = event.target.files[0];
        let gpx = new FormData();
        gpx.append('gpx', file);

        getTrack._AJAXcall(
        {
            method: 'POST',
            url : '/api/gpxupload',
            data : gpx,
        },
        {
            BeforeSendFn: function(){
                description[0].style.display = "none";
                loading[0].style.display = "block"
                GPXonProgress = true;
            },
            CompleteFn: function(){
                event.target.value = "";
                description[0].style.display = "block";
                loading[0].style.display = "none"
                GPXonProgress = false;
            },
            SuccessFn: function(data){
                Journey66.Write(data,function(){
                    getTrack._Show(false);
                });
            },
            ErrorFn: function(data){
                let error = GPXbtn.getElementsByClassName('error');
                error[0].textContent = "error";
            },
        });
    });

    // set button slider, use jQuery
    $('#getTrack-features').slick({
        infinite: false,
        slidesToShow: 1,
        variableWidth: true,
        arrows: false
    });
});
</script>
@endsection

@section('styles')
@parent
<style>
    getTrack .getTrack-element{
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
    getTrack .getTrack-header{
        background-color: white;
        opacity: .7;
        color: black;
        display: inline-block;
        margin-bottom: 1em;
        padding: 0.8em;
    }
    getTrack span.error{
        color: red;
    }
</style>
@endsection

@section('html')
    @parent

    <div class="getTrack-header">
        <h3>{{__('journey.form.getPath.title')}}</h3>
    </div>
    <!-- use Slick jQuery Slider (http://kenwheeler.github.io/slick/) -->
    <div id="getTrack-features">

        <!-- GPX UPLOAD -->
        <div id="gpx-upload" class="getTrack-element">
                <h4>{{__('journey.form.getPath.gpx.title')}} <i class="fas fa-file-upload"></i></h4>
            <description>
                <p>{{__('journey.form.getPath.gpx.description')}}</p>
                <p><span class="error"></span></p>
            </description>
            <loading style="display: none">
                <p>Loading... <img style="display:inline; height:15px;" src="/assets/Spinner-2s-50px.gif"> </p>
            </loading>
            <input id="gpx-upload-file" name="gpx" type="file" accept=".gpx" hidden>
        </div>
        <!-- GPX UPLOAD END -->

        <!-- LOAD FROM STRAVA -->
        <div id="load-strava" class="getTrack-element">
            <h4>{{__('journey.form.getPath.strava.title')}} <i class="fab fa-strava"></i></h4>
            <p>{{__('journey.form.getPath.strava.description')}}</p>
            <p><span class="error"></span></p>
            {{-- <button id="gpx-upload-button" type="button" class="btn btn-primary" >UPLOAD</button> --}}
            {{-- <input id="gpx-upload-file" name="gpx" type="file" accept=".gpx" hidden> --}}
        </div>
        <!-- LOAD FROM STRAVA END -->

    </div>

@endsection