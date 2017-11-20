<template lang="html">
    <div id="userFormModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User information</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Name:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter user name" :value="this.userName" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="status">Status:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="status" name="status" v-model="userStatus">
                                    <option v-for="s in statusList" :value="s.idStatus">{{ s.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" v-if="this.user == null">
                            <label class="control-label col-sm-2" for="pwd">Password:</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="pass" id="pwd" placeholder="Enter password" autocomplete="off">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" v-on:click="save">{{ buttonText }}</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
var messageStore = require('./../../../messages.js');
export default {
    data: function() {
        return {
            statusList: [],
            userStatus: 1
        };
    },
    props: ['user'],
    computed: {
        userName: function() { return this.user == null ? '' : this.user.userName; },
        buttonText: function() { return this.user == null ? 'Register' : 'Save'; }
    },
    watch: {
        user: function() {
            this.userStatus = this.user == null ? 1 : (this.user.status == 'ACTIVE' ? 1 : 2);
        }
    },
    methods: {
        save: function() {
            if (this.user == null) {
                $.post('./php/controllers/userController.php', {
                    action: 'registerUser',
                    name: $('#userFormModal input[name="name"]').val(),
                    pass: $('#userFormModal input[name="pass"]').val(),
                    status: $('#userFormModal select[name="status"]').val()
                }, (json) => {
                    var result = JSON.parse(json);
                    if (result.status == 'OK') {
                        this.$emit('saved');
                        $('#userFormModal .btn-danger').click();
                        messageStore.commit('addMessage', {
                            text: 'User registered',
                            type: 'success'
                        });
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Unable to save user\'s information. ' + response.error,
                            type: 'error'
                        });
                        console.error(result.error);
                    }
                });
            } else {
                $.post('./php/controllers/userController.php', {
                    action: 'adminUpdateUser',
                    idUser: this.user.idUser,
                    name: $('#userFormModal input[name="name"]').val(),
                    status: $('#userFormModal select[name="status"]').val()
                }, (json) => {
                    var result = JSON.parse(json);
                    if (result.status == 'OK') {
                        this.$emit('saved');
                        $('#userFormModal .btn-danger').click();
                        messageStore.commit('addMessage', {
                            text: 'User\'s info updated',
                            type: 'success'
                        });
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Unable to update user\'s information. ' + response.error,
                            type: 'error'
                        });
                        console.error(result.error);
                    }
                });
            }
        },
        loadStatus: function() {
            $.post('./php/controllers/statusController.php', {
                action: 'getStatus'
            }, (json) => {
                var result = JSON.parse(json);
                if (result.status == 'OK') {
                    this.statusList = result.statusList;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load status list',
                        type: 'error'
                    });
                }
            });
        },
        isSelected: function(n) {
            return (this.user == null ? 'false' : (n == 'ACTIVE' ? 'true' : 'false'));
        }
    },
    created: function() {
        this.loadStatus();
    }
}
</script>

<style lang="css">
</style>
