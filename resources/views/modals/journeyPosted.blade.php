<div id="journeyPosted" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{__('journey.form.posted.title')}}</h5>
        </div>
        <div class="modal-body">
            <modal-message-loading>
                <img src="https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif"><br>
                {{__('journey.form.posted.ing')}}
            </modal-message-loading>

            @switch($mode)
                @case('new')
                <modal-message-done>
                    <p><i class="far fa-envelope fa-5x"></i></p>
                    {{__('journey.form.posted.done1')}}<br>
                    <b><author-email></author-email></b><br>
                    {{__('journey.form.posted.done2')}}<br>
                    {{__('journey.form.posted.done3')}}<br>
                </modal-message-done>
                    @break
                @case('edit')
                <modal-message-done>
                    <p><i class="far fa-save fa-5x"></i></p>
                    {{__('journey.form.edited.done1')}}<br>
                    <a href="{{$journeyLink}}">{{__('journey.form.edited.gojourney')}}</a>
                </modal-message-done>
                    @break
                @default
            @endswitch
            <modal-message-error>
            </modal-message-error>
        </div>
    </div>
</div>