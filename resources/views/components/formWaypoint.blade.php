<!-- Dummy Waypoint form-->
<fieldset id="{{$id}}" style="{{$style}}">
    <div class="row">
        <div class="col-md-3">
            <img id="static-map" style="width: 100%" src="">
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="form-group card-header">
                    <legend id="wp-name">{{__('journey.form.waypoint.waypoint')}} #</legend>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="Lat Lng" class="col-md-2 col-form-label">{{__('journey.form.waypoint.location')}}</label>
                        <div class="col-md-10">
                            <input id="Lat" name="Lat" class="form-control disabled" type="number" value="12" required readonly>
                            <input id="Lng" name="Lng" class="form-control disabled" type="number" value="32" required readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="waypoint-type" class="col-md-2 col-form-label">{{__('journey.form.waypoint.type')}}</label>
                        <div class="col-md-10">
                            <select id="waypoint-type" name="waypoint-type" class="form-control">
                                @foreach ($waypoint_labels as $label)
                                    <option value="{{$label->name}}">{{__('journey.label.waypoint.'.$label->name)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="image" class="col-md-2 col-form-label">{{__('journey.form.waypoint.photo')}}</label>
                        <div id="waypoint-images" class="col-md-10">
                            <div class="image" style="overflow: hidden;">

                            </div>
                            <input type="file" id="input_img" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="waypoint-name" class="col-md-2 col-form-label">{{__('journey.form.waypoint.name')}}</label>
                        <div class="col-md-10">
                            <input id="waypoint-name" name="waypoint-name" class="form-control" type="text">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-md-2 col-form-label">{{__('journey.form.waypoint.description')}}</label>
                        <div class="col-md-10">
                            <textarea id="description" name="description" class="form-control" rows="5" placeholder="description about this waypoint"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button id="waypoint-up" type="button" class="btn btn-light">{{__('journey.form.waypoint.btn_up')}}</button>
                    <button id="waypoint-down" type="button" class="btn btn-light">{{__('journey.form.waypoint.btn_down')}}</button>
                    <button id="waypoint-delete" type="button" class="btn btn-danger">{{__('journey.form.waypoint.btn_delete')}}</button>
                </div>
            </div>
        </div>
    </div>
</fieldset>
<!-- Dummy Waypoint form END-->