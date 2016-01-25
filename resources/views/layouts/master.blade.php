<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <!-- Bootstrap -->
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <style>
        body {
          padding-top: 70px;
          padding-bottom: 30px;
        }
        </style>
        <script src="/js/jquery-2.1.4/jquery.min.js"></script>
    </head>
    <body>
        @section('sidebar')
            <nav class="navbar navbar-inverse navbar-fixed-top"><div class="container"><div class="navbar-header"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#w1-collapse"><span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span></button><a class="navbar-brand" href="/index.php">起点自动发布系统</a></div><div id="w1-collapse" class="collapse navbar-collapse"><ul id="w2" class="navbar-nav navbar-right nav"></ul></div></div>
            </nav>
        @show

        <div class="container theme-showcase" role="main">
            @yield('content')
        </div>
    </body>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/bootstrap.min.js"></script>
</html>