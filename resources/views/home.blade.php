@extends('layout.app',[
    'footer' => false,
])

@section('title', 'Home')

@push('meta')
    <meta name="title" content="Journey 66 Home">
    <meta name="description" content="write and share your wonderful journey">

    <meta property="og:site_name" content="Journey 66">
    <meta property="og:title" content="Journey 66 Home" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="write and share your wonderful journey" />
    <meta property="og:url" content="http://journey66.cc/" />
    <meta property="og:image" content="/assets/adult-backlit-bicycle-1522545.jpg" />
@endpush

@push('scripts')
<script>
    let backgrounds = ['adventure-albay-clouds-672358.jpg','adult-backlit-bicycle-1522545.jpg','adult-adventure-asphalt-969679.jpg','asphalt-aspiration-clouds-215.jpg'];
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
        .main-header{
            margin-top: 10%;
        }

        .link_header{
            font-size: 1.5em;
            font-weight: 700;
        }

        .main-header .lilumi-box{
            padding: 1.2em;
        }
        
    </style>
@endpush

@section('contents')

    <div class="main-container" id="main">
        @component('components.TopMenu')
        @endcomponent
        <div class="container">
            <div class="main-header">
                <div class="lilumi-box">
                    <h1>Journey 66</h1>
                    <p>{{ __('journey.home.slogan') }}</p>
                </div>
            </div>
            <div class="sub-menus">
                <div>
                    <div class="sub-menu lilumi-target lilumi-box lilumi-btn">
                        <a class="lilumi-target-a" href="/write"></a>
                        <h4 class="link_header"><i class="fas fa-edit"></i> write journey</h4>
                        <p>{{ __('journey.home.write_desc') }}   <span class="arrow-right"><i class="fas fa-chevron-circle-right"></i></span>
                        </p>
                    </div>
                </div>
                <div>
                    <div class="sub-menu lilumi-target lilumi-box lilumi-btn disactive">
                        {{-- <a class="lilumi-target-a" href="/journey_shuffle"></a> --}}
                        <h4 class="link_header"><i class="fas fa-random"></i> shuffle</h4>
                        <p>{{ __('journey.home.shuffle_desc') }}   <i id="arrow" class="fas fa-chevron-circle-right"></i></p>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection