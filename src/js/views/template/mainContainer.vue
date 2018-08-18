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
                    <h1>Sirnus</h1>
                    <p>Welcome to Sirnus, please log in first</p>
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
