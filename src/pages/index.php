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
        <div id="App" class="content-fluid">
            <paca-header></paca-header>
            <div class="row" id="mainContainer">
                <paca-navbar></paca-navbar>
                <div class="mainContent col-md-10">
                    <!-- <div v-if="session.rol == 'admin'"> -->
                        <paca-admin-home></paca-admin-home>
                    <!-- </div>
                    <div v-if="session.rol == 'user'"> -->
                        <paca-user-home></paca-user-home>
                    <!-- </div> -->
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
