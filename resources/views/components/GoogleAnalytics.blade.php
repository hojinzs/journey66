@if($TrackingID = env('GOOGLE_ANALYTICS_KEY',null))

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{$TrackingID}}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{$TrackingID}}');
</script>


@endif