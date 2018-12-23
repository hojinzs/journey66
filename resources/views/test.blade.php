@extends('layout.app')

@section('title', 'TEST PAGE')

@section('contents')

@php
echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];
@endphp
<br>
@php
echo App::getLocale()    
@endphp

@endsection