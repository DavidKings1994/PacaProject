<template lang="html">
    <div id="profileView">
        <div class="well">
            <div class="panel panel-default">
                <div class="panel-heading"><h1>{{ name }}</h1></div>
                <div class="panel-body">
                    <h4>My currency: ${{ currency }}</h4>
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
            <div class="panel panel-default">
                <div class="panel-heading"><h3>Change password</h3></div>
                <div class="panel-body">
                    <form>
                        <div class="form-group">
                            <input type="password" class="form-control" id="pass" placeholder="New password" required="true">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="newpass" placeholder="Confirm new password" required="true">
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-danger" v-on:click="changePassword">Save changes</button>
                        </div>
                    </form>
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
            return navigation.state.session.idUser;
        }
    },
    methods: {
        loadCharacters: function() {
            $.post('./php/controllers/userController.php', {
                action: 'getCharacters',
                id: this.id
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
                id: this.id
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
        },
        changePassword: function() {
            if (/([^\s])/.test($('input#pass').val().trim()) && /([^\s])/.test($('input#newpass').val())) {
                if ($('input#pass').val() == $('input#newpass').val()) {
                    let newPass = $('input#pass').val();
                    $.post('./php/controllers/userController.php', {
                        action: 'changePassword',
                        id: this.id,
                        newPass: newPass
                    }, (msg) => {
                        var json = JSON.parse(msg);
                        if (json.status != 'ERROR') {
                            messageStore.commit('addMessage', {
                                text: 'Password changed',
                                type: 'success'
                            });
                        } else {
                            messageStore.commit('addMessage', {
                                text: 'Unable to change password',
                                type: 'error'
                            });
                        }
                    });
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Passwords doesn\'t match',
                        type: 'warning'
                    });
                }
            } else {
                messageStore.commit('addMessage', {
                    text: 'Passwords cannot be empty',
                    type: 'warning'
                });
            }
        }
    },
    created: function() {
        this.loadCharacters();
        this.loadItems();
    }
}
</script>

<style lang="css">
#profileView {
    padding: 25px;
}
</style>
