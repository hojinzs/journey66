<{{$name}} class="section66 level-1">

<script> const <?php echo $name ?> = new Journey66.Section66("<?php echo $name ?>"); </script>

<style>
    {{$name}}{
        display: block;
        @isset($thumbnail) 
        background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url({{$thumbnail}});
        @endisset
        background-position: center center;
        background-size: cover;
        background-repeat: no-repeat;    
        padding-top: 20px;
        padding-bottom: 20px;
    }
    {{$name}} .container{
        padding-right: 0px;
    }

    {{$name}}.hidden_section{
        overflow: hidden;
        display: none;
        height: 0px;
    }
</style>

<div class="container">
    @include($section)
</div>
</{{$name}} >