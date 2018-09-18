<template lang="html">
    <div id="characterTransactionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transfer character</h4>
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
            selected: null,
            users: []
        };
    },
    props: ['id', 'character'],
    components: {
        'v-select': vSelect,
        'bootstrap-toggle': BootstrapToggle
    },
    methods: {
        close: function() {
            this.selected = null;
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
                this.selected = null;
                loading(false);
            });
        },
        save: function() {
            if (this.selected != null) {
                $.post('./php/controllers/characterController.php', {
                    action: 'sendTransferRequest',
                    character: this.character.idCharacter,
                    user: this.selected.value
                }, (json) => {
                    let response = JSON.parse(json);
                    if (response.status == 'OK') {
                        $('#characterTransactionModal .btn-danger').click();
                        this.close();
                        this.$emit('saved');
                        messageStore.commit('addMessage', {
                            text: 'Character transfered!',
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
                    text: 'please select who you wanna transfer your character',
                    type: 'warning'
                });
            }
        }
    }
}
</script>

<style lang="css">
#characterTransactionModal #inputSearch {
    margin-bottom: 10px;
}
</style>
