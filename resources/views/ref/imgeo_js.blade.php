@extends('layout.app')

@section('title',"Reference:: imgeo")

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

                /////////////////////////////
                // use Javascript Load Image
                // for Parse Image Metadata
                // Reference:: https://github.com/blueimp/JavaScript-Load-Image
                // Demo:: https://blueimp.github.io/JavaScript-Load-Image/
                // //
                // use Geopoint js
                // for Converte DMS(127° 46' 16.52"]) to latitude/longitude(127.77125...,37.1537555...) string
                // Reference:: https://github.com/perfectline/geopoint
                /////////////////////////////
                var file = event.target.files[0];

                var loadingImage = loadImage(
                    file,
                    function(img){
                        //
                        loadImage.parseMetaData(
                            file,
                            function (data) {
                                if (!data.imageHead) {
                                    return;
                                }
                                var AllData = data.exif.getAll();
                                console.log(AllData);

                                var DMSlat = data.exif.get('GPSLatitude');
                                var DMSlon = data.exif.get('GPSLongitude');
                                console.log('DMS data',{
                                    'GPSLatitude' : DMSlat,
                                    'GPSLongitude' : DMSlon,
                                });

                                var point = new GeoPoint(
                                    DMSlon[0]+"° "+DMSlon[1]+"'"+DMSlon[2]+'"',
                                    DMSlat[0]+"° "+DMSlat[1]+"'"+DMSlat[2]+'"',
                                );
                                var lat = point.getLatDec().toFixed(8);
                                var lon = point.getLonDec().toFixed(8);
                                console.log("Latitute",lat);
                                console.log("Longitude",lon);

                                return getImageAfter(img,lat,lon,AllData);
                            },
                            {
                                maxMetaDataSize: 262144,
                                disableImageHead: false
                            }
                        );
                    },
                    {
                        maxWidth: 600,
                        orientation: true,                        
                    }
                );
                if (!loadingImage) {
                    alert('error!');
                };
            });

            function getImageAfter(img,lat,lon,data){

                //set image
                $(document.getElementById("image")).empty();
                document.getElementById("image").appendChild(img);

                //set Static Map
                $(document.getElementById("map")).empty();
                var width = Math.round($("#map").width()); //use jQuery
                var staticMap = new Image();
                staticMap.src = setStaticMapURL({
                    width: width,
                    height: width,
                    lat: lat,
                    lng: lon,
                    marker: true,
                    key: Gmapkey,
                });
                document.getElementById("map").appendChild(staticMap);

                //set Metadata Array
                var table = $("#meta")
                displayTagData(table,data)

                function displayTagData (node, tags) {
                    var table = node.find('table').empty()
                    var row = $('<tr></tr>')
                    var cell = $('<td></td>')
                    var prop
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

<h1>GEOTAGGED PHOTO TEST</h1>
References
<ul>
    <li><b>Javascript Load Image::  </b><a href="https://github.com/blueimp/JavaScript-Load-Image" target="_blank">https://github.com/blueimp/JavaScript-Load-Image</a></li>
    <li><b>Geopoint::  </b><a href="https://github.com/perfectline/geopoint" target="_blank">https://github.com/perfectline/geopoint</a></li>
</ul>
<hr>
    <button class="btn btn-secondary btn-sm" id="geotag_img_load">Upload Photo</button>
    <input class="btn btn-secondary btn-sm" type="file" value="Upload Photo" id="geotag_img" hidden>
<hr>
    <div class="flex-container">
        <div class="flex-box" id="image">
            <b>Image</b>
        </div>
        <div class="flex-box" id="map">
            <b>Static Map</b>
        </div>
        <div class="flex-box" id="meta">
            <b>Meta</b>
            <table>
            </table>
        </div>
    </div>

@endsection