@extends('layout.app')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-load-image/2.20.1/load-image.all.min.js"></script>
    <script src="/js/geopoint.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(){

            var button = document.getElementById("geotag_img_load");
            var fileinput = document.getElementById("geotag_img");
            var output = document.getElementById("allMetaDataSpan");
            var Gmapkey = document.getElementsByName('google-map-key')[0].getAttribute('content');
            
            button.addEventListener("click",function(){
                fileinput.dispatchEvent(new MouseEvent('click'));
            })

            fileinput.addEventListener("change",function(event){
                let file = event.target.files[0];

                let gpx = new FormData();
                gpx.append('gpx', file);

                //ajax POST /ref/phpGpxParser Controller
                let xhr = new XMLHttpRequest();
                xhr.open('POST','/api/ref/php-gpx-parser',true);
                xhr.onreadystatechange = function(){
                    if (xhr.readyState == xhr.DONE) {
                        if (xhr.status == 200 || xhr.status == 201) {
                            console.log(xhr.responseText);
                            let data = JSON.parse(xhr.responseText)
                            getGpxParseAfter(data);
                        } else {
                            console.log(xhr.responseText);
                        }
                    };
                };
                xhr.send(gpx);
            });

            function getGpxParseAfter(data){

                //set Static Map
                $(document.getElementById("map")).empty();
                var width = Math.round($("#map").width()); //use jQuery
                var staticMap = new Image();
                staticMap.src = setStaticMapURL({
                    width: width,
                    height: width,
                    encpath: data.polyline,
                    key: Gmapkey,
                });
                document.getElementById("map").appendChild(staticMap);

                //set Metadata Array
                let MetaTable = $("#meta")
                displayMetaData(MetaTable,data.parse);

                //set Sequence Array
                let SequenceTable =$('#sequence');
                displaySequnceData(SequenceTable,data.sequence);

                function displayMetaData (node, tags) {
                    let table = node.find('table').empty()
                    let row = $('<tr></tr>')
                    let cell = $('<td></td>')
                    let prop
                    for (prop in tags) {
                        if (tags.hasOwnProperty(prop)) {
                            table.append(
                            row.clone()
                                .append(cell.clone().text(prop))
                                .append(cell.clone().text(tags[prop]))
                            )
                        }
                    }
                    node.show();
                };

                function displaySequnceData(node,sequnce){
                    let table = node.find('table').empty();
                    let row = $('<tr></tr>');
                    let cell = $('<td></td>');
                    sequnce.forEach(function(point){
                        table.append(
                            row.clone()
                                .append(cell.clone().text(point.sequence))
                                .append(cell.clone().text(point.latitude))
                                .append(cell.clone().text(point.longitude))
                        );  
                    });
                    node.show();
                }

            }

            function setStaticMapURL(param={
                width: 300,
                height: 300,
                zoom: 10,
                lat: null,
                lng: null,
                marker: false,
                encpath: null,
                key: null,
            }){
                var staticmap = "https://maps.googleapis.com/maps/api/staticmap?"
                +"&size="+param.width+"x"+param.height
                +"&scale=2";

                //set Zoom Level
                if(param.zoom)
                {
                    staticmap = staticmap
                    +"&zoom=" +param.zoom;
                } else {
                    staticmap = staticmap
                    +"&zoom="+10;
                };

                //set Marker
                if(param.lat && param.lng && param.marker)
                {
                    staticmap = staticmap
                    +"&markers=color:red|"+param.lat+","+param.lng;
                };
                
                //set Center
                if(param.lat && param.lng){
                    staticmap = staticmap
                    +"&center="+param.lat+","+param.lng;                    
                };

                //set Path
                console.log(param.encpath);
                if(param.encpath)
                {
                    let color = "0xff0000ff";
                    let width = 3;
                    staticmap = staticmap + "&path=weight:" + width + "%7Ccolor:"+ color + "%7Cenc:"+ param.encpath;
                };

                //finally, add Key And Complete request URL
                staticmap = staticmap
                +"&key=" + param.key;

                return staticmap;
            };

        });


    </script>
@endpush

@push('styles')
    <style>
        html body{
            min-width: 900px;
            padding: 1em;
        }

        .flex-container {
            display: -webkit-flex;
            display: flex;
        }

        .flex-box{
            -webkit-flex: 1;
            -webkit-flex-shrink:0;
            flex: 1;
            flex-shrink:0;
        }

        .flex-box canvas{
            max-width: 100%;
        }

        .flex-box img{
            max-width: 100%;
        }

        .flex-box table{
            width: 100%;
            word-wrap: break-word;
            table-layout: fixed;
        }

        .flex-box table tr:nth-child(odd) {
            background: #eee;
            color: #222;
        }
    </style>
@endpush

@push('meta')
    <meta name="google-map-key" property="text" content="{{env('GOOGLE_MAPS_KEY',null)}}">
@endpush

@section('title', 'TEST PAGE')

@section('contents')

<h1>PHP-GPX</h1>
References
<ul>
    <li><b>phpGPX::  </b><a href="https://sibyx.github.io/phpGPX/" target="_blank">https://sibyx.github.io/phpGPX/</a></li>
</ul>
<hr>
    <button class="btn btn-secondary btn-sm" id="geotag_img_load">Upload Gpx File</button>
    <input class="btn btn-secondary btn-sm" type="file" value="Upload Photo" id="geotag_img" hidden>
<hr>
    <div class="flex-container">
        <div class="flex-box" id="map">
            <b>Static Map</b>
        </div>
        <div class="flex-box" id="meta">
            <b>Meta data</b>
            <table>
            </table>
        </div>
        <div class="flex-box" id="sequence">
            <b>Sequence</b>
            <table>
            </table>
        </div>
    </div>

@endsection