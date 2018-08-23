<template lang="html">
    <div class="">
        <div v-if="logged" class="row" id="mainContainer">
            <paca-navbar></paca-navbar>
            <div class="mainContent col-md-10">
                <router-view></router-view>
            </div>
            <paca-logout></paca-logout>
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
