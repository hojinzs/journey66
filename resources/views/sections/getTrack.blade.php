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

        let xhr = new XMLHttpRequest();
        xhr.open('POST','/api/gpxupload',true);
        xhr.onload = function(){
            if (xhr.status == 200 || xhr.status == 201) {
                // AJAX success
                let data = JSON.parse(xhr.responseText);
                Journey66.Write(data,function(){
                    document.getElementById("getTrack").style.display = "none";;
                });
            } else {
                // AJAX error
                let error = GPXbtn.getElementsByClassName('error');
                error[0].textContent = "error";
            };
            // AJAX complete
            event.target.value = "";
            description[0].style.display = "block";
            loading[0].style.display = "none"
            GPXonProgress = false;
        };
        // AJAX beforeSend
        description[0].style.display = "none";
        loading[0].style.display = "block"
        GPXonProgress = true;

        // AJAX send
        xhr.send(gpx);
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

<style>
#getTrack{
    background-image: url('/assets/adult-backlit-bicycle-1522545.jpg');
    background-position: center center;
    background-size: cover;
    background-repeat: no-repeat;    
    padding-top: 20px;
    padding-bottom: 20px;
}

#getTrack .container{
    padding-right: 0px;
}
 
#getTrack .getTrack-element{
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

#getTrack .getTrack-header{
    background-color: white;
    opacity: .7;
    color: black;
    display: inline-block;
    margin-bottom: 1em;
    padding: 0.8em;
}

#getTrac span.error{
    color: red;
}
</style>
<div id="getTrack" class="getTrack-Wrapper">
    <div class="container">
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
    </div>
</div>