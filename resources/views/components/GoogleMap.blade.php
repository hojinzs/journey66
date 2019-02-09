<!-- Google Map-->
<div id="map" data-gmapkey="{{env('GOOGLE_MAPS_HOST_KEY',null)}}"></div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_HOST_KEY',null)}}&libraries=geometry&callback=initMap"></script>