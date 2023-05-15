<?php
if (!isset($seo)) {
    $seo = (object)array('seo_title' => $siteSetting->site_name, 'seo_description' => $siteSetting->site_name, 'seo_keywords' => $siteSetting->site_name, 'seo_other' => '');
}
?>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="{{ (session('localeDir', 'ltr'))}}" dir="{{ (session('localeDir', 'ltr'))}}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<meta name="robots" content="noindex,nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{__($seo->seo_title) }}</title>
    <meta name="Description" content="{!! $seo->seo_description !!}">
    <meta name="Keywords" content="{!! $seo->seo_keywords !!}">
    {!! $seo->seo_other !!}
    <!-- Fav Icon -->
    <link rel="shortcut icon" href="{{asset('/')}}favicon.ico">
    <!-- Slider -->
    <link href="{{asset('/')}}js/revolution-slider/css/settings.css" rel="stylesheet">
    <!-- Owl carousel -->
    <link href="{{asset('/')}}css/owl.carousel.css" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    {{-- <link href="{{asset('/')}}css/bootstrap.min.css" rel="stylesheet"> --}}
    {{-- bootstrap icon --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    {{-- normalize css --}}
    <link href="{{asset('/')}}css/normalize.css" rel="stylesheet">
    <!-- slick carousel css -->
    <link rel="stylesheet" type="text/css" href="{{asset('/')}}css/slick.css"/>
    <link rel="stylesheet" type="text/css" href="{{asset('/')}}css/slick-theme.css"/>
    <!-- Font Awesome -->
    <link href="{{asset('/')}}css/font-awesome.css" rel="stylesheet">
    <!-- Simple Line Icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css" rel="stylesheet">
    <!-- Custom Style -->
    <link href="{{asset('/')}}css/main.css" rel="stylesheet">
    @if((session('localeDir', 'ltr') == 'rtl'))
    <!-- Rtl Style -->
    <link href="{{asset('/')}}css/rtl-style.css" rel="stylesheet">
    @endif
    <link href="{{ asset('/') }}admin_assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/') }}admin_assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/') }}admin_assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
          <script src="{{asset('/')}}js/html5shiv.min.js"></script>
          <script src="{{asset('/')}}js/respond.min.js"></script>
        <![endif]-->
    @stack('styles')
    {!! $siteSetting->ganalytics !!}
    {!! $siteSetting->google_tag_manager_for_head !!}
</head>
<body>
    @yield('content')

    <script src="https://kit.fontawesome.com/61c5d3d4be.js" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/turbolinks/5.0.0/turbolinks.min.js" data-turbolinks-eval="false" data-turbo-eval="false"></script>
    <!-- Bootstrap's JavaScript -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script> --}}
    <script src="{{asset('/')}}js/popper.js"></script>
    <script src="{{asset('/')}}js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    {{-- <script src="{{asset('/')}}js/bootstrap.min.js"></script> --}}

    {{-- slick js --}}
    <script type="text/javascript" src="{{asset('/')}}js/slick.min.js"></script>
    
    <!-- Owl carousel -->
    <script src="{{asset('/')}}js/owl.carousel.js"></script>
    <script src="{{ asset('/') }}admin_assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="{{ asset('/') }}admin_assets/global/plugins/Bootstrap-3-Typeahead/bootstrap3-typeahead.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <script src="{{ asset('/') }}admin_assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="{{ asset('/') }}admin_assets/global/plugins/jquery.scrollTo.min.js" type="text/javascript"></script>
    <!-- Revolution Slider -->
    <script type="text/javascript" src="{{ asset('/') }}js/revolution-slider/js/jquery.themepunch.tools.min.js"></script>
    <script type="text/javascript" src="{{ asset('/') }}js/revolution-slider/js/jquery.themepunch.revolution.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
    {!! NoCaptcha::renderJs() !!}
    @stack('scripts')

    <!-- Custom js -->
    <script src="{{asset('/')}}js/script.js"></script>

    <!-- animate on scroll js -->
    <script defer src="{{asset('/')}}js/animate_on_scroll.js"></script>

    <script type="text/JavaScript">
        $(document).ready(function(){
            $(document).scrollTo('.has-error', 2000);
            });
            function showProcessingForm(btn_id){		
            $("#"+btn_id).val( 'Processing .....' );
            $("#"+btn_id).attr('disabled','disabled');		
            }		

		setInterval("hide_savedAlert()",7000);
        function hide_savedAlert(){
          $(document).find('.svjobalert').hide();
        }

        $(document).ready(function(){
            $.ajax({
                type: 'get',
                url: "{{route('check-time')}}",
                success: function(res) {
                        $('.notification').html(res);
                   
                }
            });
        });		
        </script>

</body> 
</html>