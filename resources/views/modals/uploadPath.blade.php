<div id="uploadPath" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{__('journey.form.getPath.title')}}</h5>
        </div>
        <div class="modal-body">
            <div class="contailer">
                <div class="row">
                    <div class="col-md-12">
                        <p>{{__('journey.form.getPath.description')}}</p>
                    </div>
                    <div class="col-md-6">
                        <form id="GPX-upload">
                            <h5>1. {{__('journey.form.getPath.gpx.title')}}</h5>
                            <p>{{__('journey.form.getPath.gpx.description')}}</p>
                            <input id="gpx-upload-file" name="gpx" type="file" accept=".gpx"><br>
                            {{-- <a href="#" onclick="javascriot:gpxupload_test()">use sample gpx file</a> --}}
                            <a href="/sample_gpx/300k.gpx" download="Sample.gpx">Download sample gpx file</a>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form id="Strava">
                            <h5>2. {{__('journey.form.getPath.strava.title')}}</h5>
                            <p>{{__('journey.form.getPath.strava.description')}}</p>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <p>{{__('journey.form.getPath.envnotice')}}</p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>