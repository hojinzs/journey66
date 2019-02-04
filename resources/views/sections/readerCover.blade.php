{{-- ReaderCover --}}
{{-- show journey thumbnail image and hack loading time..
---- Variable Given by showJourney.blade.php
---- @thumbnail => $journey->meta->thumbnail
---- @name => $journey->name,
---- @date => $journey->startedAt,
---- @distance' => $journey->distance,
==}}
{{-- MODULE PROPERTIES--}}
@extends('layout.section66',[
    'name' => 'readerCover', // Name is using for Tag Name. !!IMPORTANT
    'background_url' => $thumbnail, //Backgrount URL. required
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
// document.addEventListener("DOMContentLoaded", function(){
    let URL = '/api/journey/'+window.location.pathname.split("/")[2];
    let msgbox = readerCover.Element.querySelector('span[name=message]');
    msgbox.textContent = "Loading Journey data...";
    readerCover._AJAXcall(
        {
            method: "GET",
            url: URL,
            data: null,
        },
        {
            SuccessFn(response){
                Journey66.Reader(response);
                msgbox.textContent = "Loading Complete";
            },
            ErrorFn(response){
                msgbox.textContent = "Error!! Something Happend!";
            },
        }
    );
// });

</script>
@endsection
{{-- MODULE SCRIPT END --}}

{{-- MODULE STYLE --}}
@section('styles')
@parent
<style>
    readerCover{
        position: sticky;
        position: -webkit-sticky;
        top: 0;
        height: 60vh;
        min-height: 500px;
        max-height: 700px;
        font: 20px;
    }

    readerCover .transparent-black{
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
    }

    readerCover .box{
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
    readerCover span.error{
        color: red;
    }
</style>
@show
{{-- MODULE STYLE END--}}

{{-- MODULE MARKUP --}}
@section('html')

<div>
    <span name="title" class="box">
        <h2>{{$name}}<h2>
    </span>
</div>

<div>
    <span name="date" class="box">
        {{$date}}
    </span>
</div>

<div>
    <span name="distance" class="box">
        {{$distance}}
    </span>
</div>

<div>
    <span name="message" class="box">
        
    </span>
</div>

@endsection
{{-- MODULE MARKUP END--}}