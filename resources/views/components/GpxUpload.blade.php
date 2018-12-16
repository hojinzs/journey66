<!-- Gpx Upload-->
<form id="GPX-upload">
    <h3>{{__('journey.form.gpxupload.title')}}</h3>
    <p>{{__('journey.form.gpxupload.description')}}</p>
    <input id="gpx-upload-file" name="gpx" type="file" accept=".gpx">
    <a href="#" onclick="javascriot:gpxupload_test()">use sample gpx file</a>
</form>