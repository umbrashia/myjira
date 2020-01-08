
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>AdminLTE 3 | Fixed Sidebar</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('dashboard/plugins/fontawesome-free/css/all.min.css') }}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="{{asset('dashboard/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
        <!--E X T R A   P A G E   C S S-->     
        @yield('styles')
        <!--E X T R A   P A G E   C S S   E N D S--> 
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('dashboard/dist/css/adminlte.min.css')}}">
        <!-- Google Font: Source Sans Pro -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <!-- Site wrapper -->
        
           @include('dashboard.layouts.header')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                @yield('content')

                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            @include('dashboard.layouts.footer')
            @yield('scripts')
            
    </body>
</html>
