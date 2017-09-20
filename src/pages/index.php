<!DOCTYPE html>
<html>
    <head>
        <!-- src/index.html -->
        <meta charset="utf-8">
        <title>Pacapillars</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="keywords" content="pacas,pacapillars,deviant,art">
        <meta name="description" content="The place to register and manage your pacapillars!">
        <link rel="shortcut icon" type="image/x-icon" href="img/guitar-icon.png"/>

        <!-- External libraries -->
        <script src="https://use.fontawesome.com/65cecb8678.js"></script>
        <!-- /External libraries -->

        <!--[htmlclean-protect]-->
        <!-- inject:css -->
        <!-- endinject -->
        <!--[/htmlclean-protect]-->
    </head>
    <body>
        <div id="App" class="content-fluid">
            <paca-header></paca-header>
            <div v-if="logged" class="row" id="mainContainer">
                <paca-navbar></paca-navbar>
                <div class="mainContent col-md-10">
                    <div v-if="rol == 'admin'">
                        <paca-admin-home v-if="page == 'home'"></paca-admin-home>
                        <!-- <paca-admin-bank v-if="page == 'bank'"></paca-admin-bank> -->
                        <paca-admin-user-list v-if="page == 'users'"></paca-admin-user-list>
                        <paca-admin-item-list v-if="page == 'items'"></paca-admin-item-list>
                        <paca-admin-badge-list v-if="page == 'badges'"></paca-admin-badge-list>
                    </div>
                    <div v-if="rol == 'user'">
                        <paca-user-home v-if="page == 'home'"></paca-user-home>
                        <!-- <paca-user-profile v-if="page == 'profile'"></paca-user-profile> -->
                        <!-- <paca-user-characters v-if="page == 'characters'"></paca-user-characters> -->
                        <!-- <paca-user-items v-if="page == 'items'"></paca-user-items> -->
                    </div>
                </div>
            </div>
            <div v-else class="row" id="mainContainer">
                <div class="container" id="loginMessage">
                    <div class="jumbotron">
                        <h1>Pacapillars</h1>
                        <p>Welcome to pacapillars: the paca manager site. To use it please log in first</p>
                    </div>
                </div>
            </div>
            <paca-login></paca-login>
            <paca-logout></paca-logout>
        </div>

        <!--[htmlclean-protect]-->
        <!-- inject:js -->
        <!-- endinject -->
        <!--[/htmlclean-protect]-->
    </body>
</html>
