<template>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#pacaNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="./index.php">Sirnus</a>
            </div>
            <div class="collapse navbar-collapse" id="pacaNavbar">
                <ul class="nav navbar-nav navbar-right">
                    <li v-if="logged">
                        <a href="#" data-toggle="modal" data-target="#logoutModal">
                            <span class="glyphicon glyphicon-log-out"></span> Logout
                        </a>
                    </li>
                    <li v-else>
                        <a href="#" data-toggle="modal" data-target="#loginModal">
                            <span class="glyphicon glyphicon-log-in"></span> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script type="text/javascript">
var navigation = require('./../../navigation.js');
export default {
    computed: {
        logged: function() {
            return navigation.state.session != null;
        }
    }
};
</script>

<style media="screen">

</style>
