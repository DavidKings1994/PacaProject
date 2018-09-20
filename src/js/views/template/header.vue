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
                    <li class="dropdown" v-if="logged && rol == 'user'">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" v-if="!this.$root.$el.className.includes('compact')">
                            <i id="notificationbell" v-bind:class="{ active: hasUnreadNotifications }"
                            class="glyphicon glyphicon-bell dropdown-toggle">
                            </i>
                            <span v-if="hasUnreadNotifications" class="badge">{{ unreadNotifications }}</span>
                        </a>
                        <router-link :to="'/user/' + userName + '/requests'" class="dropdown-toggle" data-toggle="dropdown" v-else>
                            <i id="notificationbell" v-bind:class="{ active: hasUnreadNotifications }"
                            class="glyphicon glyphicon-bell dropdown-toggle">
                            </i>
                            <span v-if="hasUnreadNotifications" class="badge">{{ unreadNotifications }}</span>
                        </router-link>
                        <ul class="dropdown-menu" id="notificationList">
                            <li v-if="!hasNotifications">Nothing here yet, come back later!</li>
                            <li v-for="notification in notifications" v-if="showNotification(notification)" :data-id="notification.id" v-bind:class="{ active: notification.status == 1 }">
                                <router-link :to="'/user/' + userName + '/requests#' + notification.id" :id="'nav-requests' + notification.id">
                                    <div class="media">
                                        <div class="media-left">
                                            <img :src="notification.characterImage" class="media-object" style="width:60px">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading" v-html="notificationMessage(notification)">
                                                {{ notificationMessage(notification) }}
                                            </h4>
                                            <p>{{ notification.date }}</p>
                                        </div>
                                    </div>
                                </router-link>
                            </li>
                        </ul>
                    </li>
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
                    <li v-if="!logged">
                        <router-link to="/passwordRestore" id="passwordRestore">
                            <span>Forgot your password?</span>
                        </router-link>
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
            active: false,
            notifications: [],
            unreadNotifications: 0
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
        userName: function() {
            return this.logged ? navigation.state.session.name : null;
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
        },
        hasUnreadNotifications: function() {
            return this.unreadNotifications > 0;
        },
        hasNotifications: function() {
            return this.notifications.length > 0;
        }
    },
    methods: {
        showNotification: function(notification) {
            switch (notification.status) {
                case 1:
                    return notification.ownerName != this.userName;
                    break;
                case 2:
                case 3:
                    return true;
                    break;
            }
        },
        notificationMessage: function(notification) {
            switch (notification.status) {
                case 1: {
                    return '<b>' + notification.ownerName + '</b> sent you a transfer request for ' + notification.characterName;
                    break;
                }
                case 2: {
                    if (notification.ownerName == this.userName) {
                        return '<b>' + notification.userName + '</b> has declined your transfer request';
                    } else {
                        return 'You declined <b>' + notification.ownerName + '</b> transfer request for ' + notification.characterName;
                    }
                    break;
                }
                case 3: {
                    if (notification.ownerName == this.userName) {
                        return '<b>' + notification.userName + '</b> has accepted your transfer request';
                    } else {
                        return 'you accepted <b>' + notification.ownerName + '</b> transfer request';
                    }
                    break;
                }
            }
        },
        menubutton: function() {
            $('#pacaNavbar').removeClass('in');
            this.active = !this.active;
        },
        activate: function() {
            this.active = !this.active;
        },
        loadNotifications: function() {
            $.post('./php/controllers/userController.php', {
                action: 'getNotifications',
                user: this.userId
            }, (msg) => {
                let json = JSON.parse(msg);
                // console.log(json);
                if (json.status == 'OK') {
                    this.$set(this, 'notifications', json.notifications);
                    $(document).ready(() => {
                        this.$set(this, 'unreadNotifications', $('#notificationList li.active').length);
                        let self = this;
                        $('#notificationList li').mouseleave(function() {
                            $(this).removeClass('active');
                            self.$set(self, 'unreadNotifications', $('#notificationList li.active').length);
                        });
                    });
                } else {
                    console.error(msg.error);
                }
            });
        }
    },
    created: function() {
        var pusher = new Pusher('1f4e2261136ad4420076', {
            cluster: 'us2'
        });

        var channel = pusher.subscribe('sirnusNotifications');
        channel.bind('newNotification', (data) => {
            this.loadNotifications();
            window.eventBus.$emit('updateCharacters');
        });
    },
    mounted: function() {
        this.loadNotifications();
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

#notificationbell.active {
    color: #1fe0c9 !important;
}

#notificationList {
    width: 300px;
    max-height: 60vh;
    padding: 0;
    overflow: auto;
}

#notificationList li {
    padding: 10px 15px;
    border-bottom: lightgray;
    border-bottom-style: solid;
    border-width: 0.4px;
}

#notificationList li a {
    background: none;
    color: black !important;
    white-space: inherit;
}

#notificationList li:hover {
    cursor: pointer;
    background: #1fe0c9;
}

#notificationList li.active {
    /* background: #1fe0c9; */
}

</style>
