<!-- Dummy Waypoint form-->
<fieldset class="waypoint"
@isset($waypoint)
id="{{$id}}" data-UWID="{{$waypoint['UWID']}}" data-seq="{{$waypoint['sequence']}}"
@endisset
@empty($waypoint)
    id="DUMMY" style="display:none"
@endif>
    <div class="row">
        <div class="col-md-3">
            <img id="static-map" style="width: 100%" src="">
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="form-group card-header">
                    <legend id="wp-name">{{__('journey.form.waypoint.waypoint')}} #@isset($id){{$id}}@endisset</legend>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="Lat Lng" class="col-md-2 col-form-label">{{__('journey.form.waypoint.location')}}</label>
                        <div class="col-md-10">
                            <input id="Lat" name="Lat" class="form-control disabled" type="number" required readonly
                            @isset($waypoint['latitude'])value="{{$waypoint['latitude']}}"@endisset>
                            <input id="Lng" name="Lng" class="form-control disabled" type="number" required readonly
                            @isset($waypoint['longitude'])value="{{$waypoint['longitude']}}"@endisset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="waypoint-type" class="col-md-2 col-form-label">{{__('journey.form.waypoint.type')}}</label>
                        <div class="col-md-10">
                            <select id="waypoint-type" name="waypoint-type" class="form-control">
                                @foreach ($waypoint_labels as $label)
                                    <option value="{{$label->name}}"
                                        @isset($waypoint['type'])
                                            @if ($label->name == $waypoint['type']) selected @endif
                                        @endisset
                                        >{{__('journey.label.waypoint.'.$label->name)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="waypoint-name" class="col-md-2 col-form-label">{{__('journey.form.waypoint.name')}}</label>
                        <div class="col-md-10">
                            <input id="waypoint-name" name="waypoint-name" class="form-control" type="text"
                            @isset($waypoint['name'])value="{{$waypoint['name']}}"@endisset>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="image" class="col-md-2 col-form-label">{{__('journey.form.waypoint.photo')}}</label>
                        <div id="waypoint-images" class="col-md-10">
                            <div class="image" style="overflow: hidden;">
                                @isset($waypoint['images'])
                                    @foreach ($waypoint['images'] as $img)
                                        <img data-type="cur" data-imgid={{$img['id']}} src="{{$img['path']}}" class="gallary rounded float-left" mode="edit">
                                    @endforeach
                                @endisset
                            </div>
                            <input type="file" id="input_img" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-md-2 col-form-label">{{__('journey.form.waypoint.description')}}</label>
                        <div class="col-md-10">
                            <textarea id="description" name="description" class="form-control" rows="5" placeholder="description about this waypoint"
                            >@isset($waypoint['description']){{$waypoint['description']}}@endisset</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button id="waypoint-up" type="button" class="btn btn-light" style="display:none">{{__('journey.form.waypoint.btn_up')}}</button>
                    <button id="waypoint-down" type="button" class="btn btn-light" style="display:none">{{__('journey.form.waypoint.btn_down')}}</button>
                    <button id="waypoint-delete" type="button" class="btn btn-danger" style="display:none">{{__('journey.form.waypoint.btn_delete')}}</button>
                    <button id="waypoint-undelete" type="button" class="btn btn-outline-primary" style="display:none">{{__('journey.form.waypoint.btn_undelete')}}</button>
                </div>
            </div>
        </div>
    </div>
</fieldset>
<!-- Dummy Waypoint form END-->