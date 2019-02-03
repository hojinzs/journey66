@extends('layout.app')

@section('title',"Reference:: javascript animation")

@section('contents')
    <p>Click on the box to animate it</p>
    <div class="box"></div>
    <style>
        html{
            background: #f2f2f2;
        }
    
        .box {
            width: 100px;
            height: 100px;
            background: #FFF;
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
        }
    </style>

    <script>
        function animateBox() {
            var target = document.querySelector('.box');
            var player = target.animate([
            {transform: 'translate(0)'},
            {transform: 'translate(100px, 100px)'}
            ], 500);
            player.addEventListener('finish', function() {
            target.style.transform = 'translate(100px, 100px)';
            });
        }
    
        var box = document.querySelector('.box');
        box.addEventListener('click', function() {
            animateBox()
        });
    </script>
@endsection