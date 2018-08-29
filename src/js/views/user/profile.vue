<template lang="html">
    <div id="profileView">
        <div class="well">
            <div class="panel panel-default" style="text-align: center;">
                <div class="panel-heading">
                    <h1>
                        <img :src="profilePic" alt="avatar" class="avatar"> {{ profile.name }}
                    </h1>
                </div>
                <div class="panel-body">
                    <h4>My currency: ${{ profile.currency }}</h4>
                </div>
            </div>
            <div class="panel panel-default panels">
                <div class="panel-heading"><h3>My inventory</h3></div>
                <div class="panel-body">
                    <div class="panel panel-default" v-for="item in items" :data-id="item.idItem">
                        <router-link :to="'/user/' + userId + '/inventory'">
                            <div class="panel-body">
                                <img :src="item.image" :alt="item.description" width="50" height="50">
                            </div>
                            <div class="panel-footer">{{ item.name }} <span class="badge">{{ item.total }}</span></div>
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="panel panel-default panels">
                <div class="panel-heading"><h3>My characters</h3></div>
                <div class="panel-body">
                    <div class="panel panel-default" v-for="character in characters" :data-id="character.idcharacter">
                        <router-link :to="'/user/' + userId + '/characters'">
                            <div class="panel-body">
                                <img :src="character.image" :alt="character.name" width="50" height="50">
                            </div>
                            <div class="panel-footer">{{ character.name }} <span :class="'label label-' + (character.status == 'HOME' ? 'success' : 'danger')">{{ character.status }}</span></div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
var navigation = require('./../../navigation.js');
var messageStore = require('./../../messages.js');
export default {
    data: function() {
        return {
            profile: {},
            characters: [],
            items: []
        };
    },
    props: ['id'],
    computed: {
        name: function() {
            return navigation.state.session.name;
        },
        currency: function() {
            return navigation.state.session.currency;
        },
        userId: function() {
            return this.profile != null ? this.profile.name : null;
        },
        profilePic: function() {
            let placeholder = '/assets/profile_pics/avatar_placeholder.png';
            return this.profile != null ? (this.profile.image != null ? this.profile.image : placeholder) : placeholder;
        }
    },
    methods: {
        loadUserInfo: function() {
            return new Promise(resolve => {
                $.post('./php/controllers/userController.php', {
                    action: 'getProfile',
                    name: this.id
                }, (msg) => {
                    var json = JSON.parse(msg);
                    if (json.status == 'OK') {
                        this.profile = json.profile;
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Ups! User does\'t exist',
                            type: 'error'
                        });
                    }
                    resolve();
                });
            });
        },
        loadCharacters: function() {
            $.post('./php/controllers/userController.php', {
                action: 'getCharacters',
                id: this.profile.idUser
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.characters = json.characters;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load user\'s characters',
                        type: 'error'
                    });
                }
            });
        },
        loadItems: function() {
            $.post('./php/controllers/userController.php', {
                action: 'getInventoryCount',
                id: this.profile.idUser
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.items = json.items;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load user\'s inventory',
                        type: 'error'
                    });
                }
            });
        }
    },
    created: function() {
        this.loadUserInfo().then(() => {
            this.loadCharacters();
            this.loadItems();
        });
    }
}
</script>

<style lang="css">
#profileView {
    padding: 25px;
}
#profileView .avatar{
    width: 150px;
    height: 150px;
    object-fit: cover;
}
</style>
