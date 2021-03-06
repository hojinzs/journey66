<!-- Journey input form-->
<form method="POST" id="journey"
    @isset($journey)
        data-mode="edit" data-UJID="{{$journey['UJID']}}" action="/api/editjourney/{{$journey['UJID']}}" data-gpx="{{basename($journey['file_path'])}}" data-key="{{$journey['key']}}"
    @endisset
    @empty($journey)
        data-mode="create" action="/api/newjourney" style="display:none"
    @endif>
    @csrf

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
                        <div class="form-group row">
                            <label for="journey-meta" class="col-md-2 col-form-label">{{__('journey.form.stat')}}</label>
                            <ul id="journey-stat" class="stats">
                                @foreach ($stats as $stat)
                                <li><span class="stat-name journey-stat-name">{{__('journey.form.stats.'.$stat)}}</span>
                                    <span class="stat-value" name="{{$stat}}"></span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="alert alert-secondary" id="waypoint-guide">
        <i class="fas fa-map-marked-alt"></i> {{__('journey.form.inform.setmarker')}}<br>
        <i class="fas fa-image"></i> {{__('journey.form.inform.geotag_photo')}} 
            <button type="button" class="btn btn-secondary btn-sm" id="geotag_img_load">Upload Photo</button>
            <input class="btn btn-secondary btn-sm" type="file" value="Upload Photo" id="geotag_img" accept="image/*" hidden>
            <p>{{__('journey.form.inform.sort')}}</p>
    </div>

    <fieldset name="waypoint-list" id="waypoint-list">
        @isset($waypoints)
            @foreach ($waypoints as $waypoint)
                @component('components.FormWaypoint',[
                    'waypoint' => $waypoint,
                    'waypoint_labels' => $waypoint_labels,
                    'id' => $loop->index,
                    'waypoint_stats' => ['distance','elevation','time'],
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
                                @isset($journey['author_email'])value="{{$journey['author_email']}}" readonly @endisset
                                required>
                            </div>
                        </div>
                        @empty($journey)
                        <div class="form-group row">
                            <label for="check_email" class="col-md-2 col-form-label"></label>
                            <div class="col-md-10">
                                <input name="check_email" class="form-check-label" type="checkbox" required>
                                <label>{{__('journey.form.email_check')}}</label>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <div class="row">
                                <label for="publish_stage" class="col-md-2 col-form-label">{{ __('journey.form.publish') }}</label>
                                <div class="col-md-10">
                                    @empty($journey)
                                        <input class="form-check-input" type="radio" name="publish_stage" id="Radio1" value="Pending" checked disabled>
                                        <label class="form-check-label" for="Radio1">
                                            <b>{{__('journey.form.published_stages.pending')}}</b> -{{__('journey.form.published_stages.pending_description')}}
                                        </label>
                                        <p>{{__('journey.form.published_stages.new_description')}}</p>
                                    @endempty                                    
                                    @isset($journey)
                                    @if ($journey['publish_stage'] == 'Pending')
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="publish_stage" id="Radio1" value="Pending" checked>
                                            <label class="form-check-label" for="Radio1">
                                                <b>{{__('journey.form.published_stages.pending')}}</b> -{{__('journey.form.published_stages.pending_description')}}
                                            </label>
                                        </div>
                                    @endif
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="publish_stage" id="Radio2" value="Published"
                                            @if ($journey['publish_stage'] == 'Published') checked @endif>
                                            <label class="form-check-label" for="Radio2">
                                                <b>{{__('journey.form.published_stages.published')}}</b> -{{__('journey.form.published_stages.published_description')}}
                                            </label>
                                        </div>
                                    @if ($journey['publish_stage'] != 'Pending')
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="publish_stage" id="Radio3" value="Private"
                                            @if ($journey['publish_stage'] == 'Private') checked @endif>
                                            <label class="form-check-label" for="Radio3">
                                                <b>{{__('journey.form.published_stages.private')}}</b> -{{__('journey.form.published_stages.private_description')}}
                                            </label>
                                        </div>
                                    @endif
                                    @endisset
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input type="submit" value="{{ __('journey.form.submit') }}" type="button" class="btn btn-primary btn-lg btn-block">
                            </div>
                        </div>
                        @isset($journey)
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" id="delete" class="btn btn-outline-danger">{{__('journey.form.delete')}}</button>
                            </div>
                        </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

</form>
<!-- Journey form end-->