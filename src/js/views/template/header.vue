<template>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="" data-toggle="collapse" data-target="#lateralNavbar"
                id="lateralNavbarcollapse" v-if="logged">
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
                    <li>
                        <a href="#" v-if="logged && rol == 'user'">
                            <span v-if="stars > 0"><img src="/assets/star.png" alt="stars"> {{ stars }}</span>
                            <span><img src="/assets/shard.png" alt="shards"> {{ shards }}</span>
                        </a>
                    </li>
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
        },
        stars: function() {
            let stars = 0;
            let shards = 0;
            if (this.logged) {
                shards = navigation.state.session.currency;
                stars = Math.floor(navigation.state.session.currency / 500);
            }
            return stars > 0 ? stars : 0;
        },
        shards: function() {
            let stars = 0;
            let shards = 0;
            if (this.logged) {
                shards = navigation.state.session.currency;
                stars = Math.floor(navigation.state.session.currency / 500);
            }
            return shards - (stars * 500);
        }
    },
    methods: {
        menubutton: function() {
            $('#pacaNavbar').removeClass('in');
            this.active = !this.active;
        },
        activate: function() {
            this.active = !this.active;
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
