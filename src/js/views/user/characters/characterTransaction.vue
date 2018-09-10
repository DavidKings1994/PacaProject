<template lang="html">
    <div id="objectTransactionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transfer item</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="owner">To user:</label>
                            <div class="col-sm-10">
                                <v-select
                                    v-model="selected"
                                    :debounce="250"
                                    :options="users"
                                    :on-search="getUsers"
                                    :clearSearchOnSelect="true"
                                    placeholder="Search user">
                                </v-select>
                            </div>
                        </div>
                        <div class="form-group" v-if="selected != null">
                            <label class="control-label col-sm-8" for="operation">Transfer item to a character of this user:</label>
                            <div class="col-sm-4">
                                <bootstrap-toggle
                                    id="operation"
                                    v-model="checked"
                                    :options="{
                                        on: 'Yes',
                                        off: 'No',
                                        onstyle: 'success',
                                        offstyle: 'danger',
                                        width: 85
                                    }" />
                            </div>
                        </div>
                        <div class="well" v-if="characters.length > 0">
                            <div class="panel panel-default" v-for="character in characters"
                            :data-id="character.idCharacter" v-on:click="select">
                                <div class="panel-body">
                                    <img :src="character.image" :alt="character.name" width="50" height="50">
                                </div>
                                <div class="panel-footer">
                                    {{ character.name }}
                                    <span :class="'label label-' + (character.status == 'HOME' ? 'success' : 'danger')">
                                        {{ character.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="quantity">Quantity:</label>
                            <div class="col-sm-4">
                                <input type="number" min="1" :max="total" v-model="quantity"
                                    class="form-control" id="quantity" name="quantity" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" v-on:click="close" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" v-on:click="save">Transfer</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import vSelect from 'vue-select';
import BootstrapToggle from 'vue-bootstrap-toggle';
var messageStore = require('./../../../messages.js');
export default {
    data: function() {
        return {
            quantity: 1,
            selected: null,
            selectedCharacter: null,
            users: [],
            characters: [],
            checked: false
        };
    },
    props: ['id', 'item'],
    watch: {
        checked: function() {
            this.selectedCharacter = null;
            $('#objectTransactionModal .panel').removeClass('selected');
            if (this.checked && this.selected != null) {
                this.getUsersCharacters();
            } else {
                this.characters = [];
            }
        }
    },
    components: {
        'v-select': vSelect,
        'bootstrap-toggle': BootstrapToggle
    },
    computed: {
        total: function() {
            return this.item == null ? 1 : this.item.total;
        }
    },
    methods: {
        close: function() {
            $('#objectTransactionModal .panel').removeClass('selected');
            this.selected = null;
            this.quantity = 1;
            this.characters = [];
            this.checked = false;
            this.$emit('closed');
        },
        getUsers: function(search, loading) {
            loading(true);
            $.post('./php/controllers/userController.php', {
                action: 'search',
                name: search
            }, (json) => {
                var result = JSON.parse(json);
                this.users = result.users;
                this.characters = [];
                this.checked = false;
                this.selected = null;
                loading(false);
            });
        },
        getUsersCharacters: function() {
            $.post('./php/controllers/userController.php', {
                action: 'getCharacters',
                id: this.selected.value
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
        select : function(event) {
            $('#objectTransactionModal .panel').removeClass('selected');
            $(event.target).closest('.panel').addClass('selected');
            this.selectedCharacter = $(event.target).closest('.panel').attr('data-id');
        },
        save: function() {
            if (this.selected != null) {
                $.post('./php/controllers/userController.php', {
                    action: 'transferItem',
                    owner: this.id,
                    character: this.selectedCharacter,
                    user: (this.selectedCharacter == null ? this.selected.value : null),
                    item: this.item.idItem,
                    quantity: this.quantity
                }, (json) => {
                    let response = JSON.parse(json);
                    if (response.status == 'OK') {
                        $('#objectTransactionModal .btn-danger').click();
                        this.close();
                        this.$emit('saved');
                        messageStore.commit('addMessage', {
                            text: 'Items transfered',
                            type: 'success'
                        });
                    } else {
                        messageStore.commit('addMessage', {
                            text: response.error,
                            type: 'error'
                        });
                    }
                });
            } else {
                messageStore.commit('addMessage', {
                    text: 'please select who you wanna transfer the item',
                    type: 'warning'
                });
            }
        }
    }
}
</script>

<style lang="css">
#objectTransactionModal #inputSearch {
    margin-bottom: 10px;
}
</style>
