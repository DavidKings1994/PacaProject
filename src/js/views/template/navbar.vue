<template>
    <div class="lateral-navbar col-sm-2 collapse in" id="lateralNavbar">
        <div v-if="rol == 'admin'">
            <router-link to="/admin/home" id="nav-home">
                <i class="fa fa-home"></i> Home
            </router-link>
            <router-link to="/admin/users" id="nav-users">
                <i class="fa fa-users"></i> Users
            </router-link>
            <router-link to="/admin/characters" id="nav-characters">
                <i class="fa fa-heart"></i> Characters
            </router-link>
            <router-link to="/admin/items" id="nav-items">
                <i class="fa fa-briefcase"></i> Items
            </router-link>
            <router-link to="/admin/badges" id="nav-badges">
                <i class="fa fa-certificate"></i> Badges
            </router-link>
            <router-link to="/admin/bank" id="nav-bank">
                <i class="fa fa-university"></i> Bank
            </router-link>
        </div>
        <div v-if="rol == 'user'">
            <router-link :to="'/user/' + userId + '/profile'" id="nav-profile">
                <i class="fa fa-user"></i> Profile
            </router-link>
            <router-link :to="'/user/' + userId + '/characters'" id="nav-characters">
                <i class="fa fa-heart"></i> Characters
            </router-link>
            <router-link :to="'/user/' + userId + '/inventory'" id="nav-items">
                <i class="fa fa-briefcase"></i> Inventory
            </router-link>
        </div>
    </div>
</template>

<script>
var navigation = require('./../../navigation.js');
export default {
    computed: {
        rol: function() {
            return navigation.state.session.rol;
        },
        userId: function() {
            return navigation.state.session.idUser;
        }
    },
    mounted: function() {
        $(document).ready(function(){
            $('#lateralNavbar').on('hidden.bs.collapse', function () {
                $('.mainContent').removeClass('col-md-10');
                $('.mainContent').addClass('col-md-12');
            });

            $('#lateralNavbar').on('show.bs.collapse', function () {
                $('.mainContent').removeClass('col-md-12');
                $('.mainContent').addClass('col-md-10');
            });

            if ($(window).width() <= 780 || $(window).height() <= 480){
                $('#lateralNavbar').collapse('hide');
                $('#lateralNavbar').addClass('compact');
            }
        });
        $(window).resize(function(){
            if ($(window).width() >= 780 && $(window).height() >= 480){
                $('#lateralNavbar').collapse('show');
                $('#lateralNavbar').removeClass('compact');
            }
            if ($(window).width() <= 780 || $(window).height() <= 480){
                $('#lateralNavbar').collapse('hide');
                $('#lateralNavbar').addClass('compact');
            }
        });
    }
}
</script>

<style>
    .lateral-navbar {
        font-size: 14px;
    }

    .lateral-navbar .fa {
        margin-right: 10px;
    }
</style>
