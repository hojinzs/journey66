@extends('layout.app')

@section('title', 'Home')

@push('scripts')
<script>
    let backgrounds = ['adult-backlit-bicycle-1522545.jpg','adult-adventure-asphalt-969679.jpg','asphalt-aspiration-clouds-215.jpg'];
    document.addEventListener("DOMContentLoaded", function(){
        let container = document.getElementById("main");
        let random = randomItem(backgrounds);
        container.style.backgroundImage = "url(/assets/"+random+")";
        
        function randomItem(a){
            return a[Math.floor(Math.random() * a.length)];
        };
    });    
</script>
@endpush

@push('css')

@endpush

@push('styles')
    <style>
        html, body{
            height: 100vh;
            margin: 0;
            font-size: 18px;
        }
    	.main-container{
            width: 100%;
            height: 100%;
            background-position: center center;
            background-size: cover;
            background-repeat: no-repeat;
        }
        .main-box{
            margin-left: 5%;
            margin-top: 10%;
        }
        .a-target{
            position: relative;
        }
        .a-wrapper{
            display: block;
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
        }

        .opacity-box{
            background-color: white;
            opacity: .7;
            color: black;
            display: inline-block;
            margin-bottom: 1em;
            padding: 0.5em;
        }
        .opacity-box.link-box{
            margin-left: 0.5em;
        }

        .link_header{
            font-size: 1.5em;
            font-weight: 700;
        }

        
    </style>
@endpush

@section('contents')

    <div class="main-container" id="main">
        @component('components.TopMenu')
        @endcomponent
        <div class="main-box">
            <div class="">
                <div class="opacity-box">
                    <h1>Journey 66</h1>
                    <p>write and share your wonderful journey</p>
                </div>
            </div>
            <div class="">
                <div class="a-target opacity-box link-box">
                    <a class="a-wrapper" href="/write"></a>
                    <p><span class="link_header"><i class="fas fa-edit"></i> write journey</span></p>
                    <p>write new journey  <span class="arrow-right"><i class="fas fa-chevron-circle-right"></i></span>
                    </p>
                </div>
            </div>
            <div class="">
                <div class="a-target opacity-box link-box">
                    <a class="a-wrapper" href="/journey_shuffle"></a>
                    <p><span class="link_header"><i class="fas fa-random"></i> shuffle</span></p>
                    <p> read journey randomly   <i class="fas fa-chevron-circle-right"></i></p>
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection