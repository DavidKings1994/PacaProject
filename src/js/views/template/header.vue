<template>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="" data-toggle="collapse" data-target="#lateralNavbar"
                id="lateralNavbarcollapse" v-on:click="changeSize">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#pacaNavbar"
                 v-on:click="activate">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="./index.php">
                    <img src="/assets/sirnus_white.png">
                </a>
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
                    <!-- <li v-if="rol == 'admin' && active" v-on:click="menubutton">
                        <router-link to="/admin/home" id="nav-home">
                            <i class="fa fa-home"></i> Home
                        </router-link>
                    </li>
                    <li v-if="rol == 'admin' && active" v-on:click="menubutton">
                        <router-link to="/admin/users" id="nav-users">
                            <i class="fa fa-users"></i> Users
                        </router-link>
                    </li>
                    <li v-if="rol == 'admin' && active" v-on:click="menubutton">
                        <router-link to="/admin/characters" id="nav-characters">
                            <i class="fa fa-heart"></i> Characters
                        </router-link>
                    </li>
                    <li v-if="rol == 'admin' && active" v-on:click="menubutton">
                        <router-link to="/admin/items" id="nav-items">
                            <i class="fa fa-briefcase"></i> Items
                        </router-link>
                    </li>
                    <li v-if="rol == 'admin' && active" v-on:click="menubutton">
                        <router-link to="/admin/badges" id="nav-badges">
                            <i class="fa fa-certificate"></i> Badges
                        </router-link>
                    </li>
                    <li v-if="rol == 'admin' && active" v-on:click="menubutton">
                        <router-link to="/admin/bank" id="nav-bank">
                            <i class="fa fa-university"></i> Bank
                        </router-link>
                    </li>
                    <li v-if="rol == 'user' && active" v-on:click="menubutton">
                        <router-link :to="'/user/' + userId + '/profile'" id="nav-profile">
                            <i class="fa fa-user"></i> Profile
                        </router-link>
                    </li>
                    <li v-if="rol == 'user' && active" v-on:click="menubutton">
                        <router-link :to="'/user/' + userId + '/characters'" id="nav-characters">
                            <i class="fa fa-heart"></i> Characters
                        </router-link>
                    </li>
                    <li v-if="rol == 'user' && active" v-on:click="menubutton">
                        <router-link :to="'/user/' + userId + '/inventory'" id="nav-items">
                            <i class="fa fa-briefcase"></i> Inventory
                        </router-link>
                    </li> -->
                </ul>
            </div>
        </div>
    </nav>
</template>

<script type="text/javascript">
var navigation = require('./../../navigation.js');
export default {
    data: function() {
        return {
            active: false
        }
    },
    computed: {
        logged: function() {
            return navigation.state.session != null;
        },
        rol: function() {
            return this.logged ? navigation.state.session.rol : null;
        },
        userId: function() {
            return this.logged ? navigation.state.session.idUser : null;
        }
    },
    methods: {
        menubutton: function() {
            $('#pacaNavbar').removeClass('in');
            this.active = !this.active;
        },
        activate: function() {
            this.active = !this.active;
        },
        changeSize: function() {
            if($('.mainContent').hasClass('col-md-10')) {
                $('.mainContent').removeClass('col-md-10');
                $('.mainContent').addClass('col-md-12');
            } else {
                $('.mainContent').removeClass('col-md-12');
                $('.mainContent').addClass('col-md-10');
            }
        }
    }
};
</script>

<style media="screen">
#lateralNavbarcollapse {
    position: relative;
    float: right;
    padding: 5px 10px;
    margin-top: 8px;
    margin-right: 15px;
    margin-bottom: 8px;
    background-color: transparent;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
    float: left !important;
    /* margin-left: 15px !important;
    margin-right: 0px !important; */
    border-color: #333;
}

#lateralNavbarcollapse .icon-bar {
    background-color: #fff;
    display: block;
    width: 22px;
    height: 2px;
    border-radius: 1px;
    margin-top: 4px;
}
</style>
