<!-- Journey input form-->
<form method="POST" id="journey"
    @isset($journey)
        data-mode="edit" data-UJID="{{$journey['UJID']}}" action="/api/editjourney/{{$journey['UJID']}}" data-gpx="{{basename($journey['file_path'])}}"
    @endisset
    @empty($journey)
        data-mode="create" action="/api/newjourney" style="display:none"
    @endif>

    <fieldset name="title" id="title">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                        <label for="journey-title" class="col-md-2 col-form-label">{{__('journey.form.title')}}</label>
                            <div class="col-md-10">
                                <input id="journey-title" name="journey-title" class="form-control" type="text" 
                                @isset($journey['name'])value="{{$journey['name']}}"@endisset
                                required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="journey-type" class="col-md-2 col-form-label">{{__('journey.form.style')}} </label>
                            <div class="col-md-10">
                                <select id="journey-type" name="journey-type" class="form-control">
                                    @foreach ($journey_labels as $label)
                                    <option value="{{$label->name}}"
                                        @isset($journey['type'])
                                            @if($label->name == $journey['type']) selected @endisset
                                        @endisset
                                    >{{__('journey.label.journey.'.$label->name)}}</option>
                                    @endforeach
                                </select>                                
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="journey-description" class="col-md-2 col-form-label">{{__('journey.form.description')}}</label>
                            <div class="col-md-10">
                                <textarea id="journey-description" name="journey-description" class="form-control" type="text" rows="5" placeholder="{{__('journey.form.description_ph')}}"
                                >@isset($journey['description']){{$journey['description']}}@endisset</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="alert alert-secondary" id="waypoint-guide">
        {{__('journey.form.inform.setmarker')}}
    </div>

    <fieldset name="waypoint-list" id="waypoint-list">
        @isset($waypoints)
            @foreach ($waypoints as $waypoint)
                @component('components.FormWaypoint',[
                    'waypoint' => $waypoint,
                    'waypoint_labels' => $waypoint_labels,
                    'id' => $loop->iteration,
                ])
                @endcomponent
            @endforeach
        @endisset
    </fieldset>

    <fieldset id="confirm" name="confirm">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{__('journey.form.confirm_title')}}</h4>
                        <div class="form-group row">
                            <label for="author" class="col-md-2 col-form-label">{{__('journey.form.author')}}</label>
                            <div class="col-md-10">
                                <input id="author" name="author" class="form-control" placeholder="{{__('journey.form.author_ph')}}"
                                @isset($journey['author_name'])value="{{$journey['author_name']}}"@endisset
                                required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label">{{__('journey.form.email')}}</label>
                            <div class="col-md-10">
                                <input id="email" name="email" class="form-control" type="email" 
                                @isset($journey['author_email'])value="{{$journey['author_email']}}"@endisset
                                required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="check_email" class="col-md-2 col-form-label"></label>
                            <div class="col-md-10">
                                <input name="check_email" class="form-check-label" type="checkbox" required>
                                <label>{{__('journey.form.email_check')}}</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input type="submit" value="Submit" type="button" class="btn btn-primary btn-lg btn-block">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

</form>
<!-- Journey form end-->