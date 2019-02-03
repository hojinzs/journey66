{{-- MODULE PROPERTIES--}}
@extends('layout.section66',[
    'name' => 'section66', //Name is using for Tag Name. 
    'background_url' => '/assets/adult-backlit-bicycle-1522545.jpg', //Backgrount URL
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
document.addEventListener("DOMContentLoaded", function(){

});
</script>
@endsection
{{-- MODULE SCRIPT END --}}

{{-- MODULE STYLE --}}
@section('styles')
<style>
    
</style>
@show
{{-- MODULE STYLE END--}}

{{-- MODULE MARKUP --}}
@section('html')

@endsection
{{-- MODULE MARKUP END--}}