@extends('layout.app')

@section('title', 'Home')

@push('scripts')

@endpush

@push('css')

@endpush

@push('styles')
    <style>
        html, body{
            height: 100vh;
            margin: 0;
        }
    	.main-container{
            width: 100%;
            display: table;
            height: 100%;
        }
        .main-box{
            text-align: center;
            display: table-cell;
            vertical-align: middle;
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
    </style>
@endpush

@section('contents')

    @component('components.TopMenu')
    @endcomponent

    <div class="main-container">
        <div class="main-box">
            <div class="d-flex flex-row justify-content-center">
                <div class="p-2">
                    <h1>- Journey 66 -</h1>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-center">
                <div class="a-target p-2">
                    <a class="a-wrapper" href="/write"></a>
                    <i class="fas fa-edit fa-5x"></i>
                    <h3>write journey</h3>
                    <p>write new journey</p>
                </div>
                <div class="a-target p-2">
                    <a class="a-wrapper" href="/"></a>
                    <i class="fas fa-random fa-5x"></i>
                    <h3>read journey</h3>
                    <p>read journey</p>	
                </div>
            </div>
        </div>
    </div>

@endsection