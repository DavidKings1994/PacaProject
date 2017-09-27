<template>
    <div class="lateral-navbar col-md-2">
        <div v-if="rol == 'admin'">
            <router-link to="/home" id="nav-home">
                <i class="fa fa-home"></i> Home
            </router-link>
            <router-link to="/users" id="nav-users">
                <i class="fa fa-users"></i> Users
            </router-link>
            <router-link to="/characters" id="nav-characters">
                <i class="fa fa-heart"></i> Characters
            </router-link>
            <router-link to="/items" id="nav-items">
                <i class="fa fa-briefcase"></i> Items
            </router-link>
            <router-link to="/badges" id="nav-badges">
                <i class="fa fa-certificate"></i> Badges
            </router-link>
            <router-link to="/bank" id="nav-bank">
                <i class="fa fa-university"></i> Bank
            </router-link>
        </div>
        <div v-if="rol == 'user'">
            <a v-on:click="select" href="#" data-destiny="home" class="selected"><i class="fa fa-home"></i> Home</a>
            <a v-on:click="select" href="#" data-destiny="characters"><i class="fa fa-heart"></i> Characters</a>
            <a v-on:click="select" href="#" data-destiny="items"><i class="fa fa-briefcase"></i> Items</a>
        </div>
    </div>
</template>

<script>
var navigation = require('./../../navigation.js');
export default {
    computed: {
        rol: function() {
            return navigation.state.session.rol;
        }
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
