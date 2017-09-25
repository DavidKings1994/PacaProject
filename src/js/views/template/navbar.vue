<template>
    <div class="lateral-navbar col-md-2">
        <div v-if="rol == 'admin'">
            <router-link to="/home" v-on:click.native="select">
                <!-- <a v-on:click="select" href="#" data-destiny="home" class="selected"> -->
                    <i class="fa fa-home"></i> Home
                <!-- </a> -->
            </router-link>
            <router-link to="/users" v-on:click.native="select">
                <!-- <a v-on:click="select" href="#" data-destiny="users"> -->
                    <i class="fa fa-users"></i> Users
                <!-- </a> -->
            </router-link>
            <router-link to="/characters" v-on:click.native="select">
                <!-- <a v-on:click="select" href="#" data-destiny="characters"> -->
                    <i class="fa fa-heart"></i> Characters
                <!-- </a> -->
            </router-link>
            <router-link to="/items" v-on:click.native="select">
                <!-- <a v-on:click="select" href="#" data-destiny="items"> -->
                    <i class="fa fa-briefcase"></i> Items
                <!-- </a> -->
            </router-link>
            <router-link to="/badges" v-on:click.native="select">
                <!-- <a v-on:click="select" href="#" data-destiny="badges"> -->
                    <i class="fa fa-certificate"></i> Badges
                <!-- </a> -->
            </router-link>
            <router-link to="/bank" v-on:click.native="select">
                <!-- <a v-on:click="select" href="#" data-destiny="bank"> -->
                    <i class="fa fa-university"></i> Bank
                <!-- </a> -->
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
    },
    methods: {
        select: function(event) {
            $('.lateral-navbar a').removeClass('selected');
            $(event.target).addClass('selected');
            navigation.commit('navigate', $(event.target).attr('data-destiny'));
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
