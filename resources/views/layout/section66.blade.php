<{{$name}} class="section66">

{{--PART SCRIPT--}}
@section('scripts')
<script>
    const <?php echo $name ?> = new Journey66.Section66("<?php echo $name ?>")
</script>
@show

{{--PART STYLE--}}
@section('styles')
<style>
    {{$name}}{
        display: block;
        background-image: url({{$background_url}});
        background-position: center center;
        background-size: cover;
        background-repeat: no-repeat;    
        padding-top: 20px;
        padding-bottom: 20px;
    }
    {{$name}} .container{
        padding-right: 0px;
    }
</style>
@show

{{--PART HTML--}}
<div class="container">
    @yield('html')
</div>

</{{$name}} >