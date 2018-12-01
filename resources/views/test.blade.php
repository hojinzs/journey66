@extends('layout.app')

@section('title', 'TEST PAGE')

@section('contents')

Hello World!!

<hr>

Journey Styles<br>
@foreach ($journey_labels as $item)

<b>{{$item->id}}</b>
<ul>
    <li><b>name::</b> {{$item->name}}</li>
    <li><b>description::</b> {{$item->description}}</li>
</ul>
<hr>
@endforeach

Waypoints<br>
@foreach ($waypoint_labels as $item)

<b>{{$item->id}}</b>
<ul>
    <li><b>name::</b> {{$item->name}}</li>
    <li><b>description::</b> {{$item->description}}</li>
</ul>
<hr>
@endforeach

@endsection