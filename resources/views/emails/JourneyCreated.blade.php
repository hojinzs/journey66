<h1>Journey66 :: {{ __('journey.email.saved') }}</h1>
<b>!! {{ __('journey.email.notice') }} !!</b> {{ __('journey.email.notice_message') }}<br>
{{ __('journey.email.notice_message2') }}<br>
<br>
<h2>{{$journey->name}}</h2>
{{-- <img src="{{$journey->thumbnail}}"> --}}
- {{ __('journey.form.author') }}: {{$journey->author_name}} <br>
- {{ __('journey.form.savedAt') }} {{$currnetTime}} <br>
<br>
{{ __('journey.email.status_message1') }}<br>
{{ __('journey.email.status_message2') }}<br>
<br>
<a href="{{$link}}">{{ __('journey.email.link') }} ></a><br>
{{-- - PIN : {{$journey->pin}} --}}