{{-- Valiable
---- $mapdata
---- $scriptparam 
--}}

<!-- Google Map-->
{{-- <div id="map" data-gmapkey="{{env('GOOGLE_MAPS_HOST_KEY',null)}}" data-gpx="{{$gpx}}" data-summary-polyline="{{$summary_polyline}}"></div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_HOST_KEY',null)}}
&region=KR
&libraries=geometry
&callback=initMap
"></script> --}}



<div id="map" data-gmapkey="{{env('GOOGLE_MAPS_HOST_KEY',null)}}"
@isset($mapdata)
    @foreach ($mapdata as $key=>$item) data-{{ $key }}="{{ $item }}" @endforeach
@endisset
>

</div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_HOST_KEY',null)}}
@isset($scriptparam)
@foreach ($scriptparam as $key=>$item)&{{ $key }}={{ $item }}@endforeach
@endisset
"></script>