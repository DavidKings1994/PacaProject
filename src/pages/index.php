<!DOCTYPE html>
<html>
    <head>
        <!-- src/index.html -->
        <meta charset="utf-8">
        <title>Paca Manager</title>
        <link rel="shortcut icon" type="image/x-icon" href="img/guitar-icon.png"/>

        <!--[htmlclean-protect]-->
        <!-- inject:css -->
        <!-- endinject -->
        <!--[/htmlclean-protect]-->
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="./index.php">Paca Manager</a>
                </div>
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">Juego</a></li>
                    <li><a href="#">Ranking</a></li>
                    <li><a href="#" data-toggle="modal" data-target="#configurations">Configuracion</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="./php/logout.php"><span class="glyphicon glyphicon-log-out"></span> Cerrar sesion</a></li>
                </ul>
            </div>
        </nav>
        <div id="App">
            <paca-navbar></paca-navbar>
        </div>

        <!--[htmlclean-protect]-->
        <!-- inject:js -->
        <!-- endinject -->
        <!--[/htmlclean-protect]-->
    </body>
</html>
