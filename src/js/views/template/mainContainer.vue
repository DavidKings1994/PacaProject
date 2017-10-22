<template lang="html">
    <div class="">
        <div v-if="logged" class="row" id="mainContainer">
            <paca-navbar></paca-navbar>
            <div class="mainContent col-md-10">
                <router-view></router-view>
            </div>
            <paca-logout></paca-logout>
        </div>
        <div v-else class="row" id="mainContainer">
            <div class="container" id="loginMessage">
                <div class="jumbotron">
                    <h1>Pacapillars</h1>
                    <p>Welcome to pacapillars: the paca manager site. To use it please log in first</p>
                </div>
            </div>
            <paca-login></paca-login>
        </div>
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
</style>
