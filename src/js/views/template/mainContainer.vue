<template lang="html">
    <div class="">
        <div v-if="logged || isGuest" class="row" id="mainContainer">
            <paca-navbar v-if="logged"></paca-navbar>
            <div class="mainContent" v-bind:class="{ 'col-md-10': logged,  'col-md-12': !logged }">
                <router-view></router-view>
            </div>
            <paca-logout v-if="logged"></paca-logout>
            <paca-login v-else></paca-login>
        </div>
        <div v-else class="row fondo" id="mainContainer">
            <div class="container col-md-8" id="loginMessage">
                <div class="jumbotron">
                    <img src="/assets/sirnus_white.png" alt="Sirnus logo">
                    <p>Welcome to Sirnus, please log in first</p>
                </div>
            </div>
            <paca-login></paca-login>
        </div>
        <messages></messages>
    </div>
</template>

<script>
var navigation = require('./../../navigation.js');
export default {
    computed: {
        logged: function() {
            return navigation.state.session != null;
        },
        isGuest: function() {
            return navigation.state.guest;
        }
    }
}
</script>

<style lang="css">
#loginMessage img {
    width: 100%;
    display: block;
    margin: 0px auto 15px;
}
</style>
