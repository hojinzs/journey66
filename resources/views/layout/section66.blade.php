<{{$name}} class="section66 level-1">

{{--PART STYLE--}}
@section('styles')
<style>
    {{$name}}{
        display: block;
        background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url({{$background_url}});
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
@show

{{--PART HTML--}}
<div class="container">
    @yield('html')
</div>
</{{$name}} >

{{--PART SCRIPT--}}
@section('scripts')
<script>
    const <?php echo $name ?> = new Journey66.Section66("<?php echo $name ?>");
</script>
@show