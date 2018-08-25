<template id="">
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
</template>

<script type="text/javascript">
var navigation = require('./../../navigation.js');
var messageStore = require('./../../messages.js');
export default {
    methods: {
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
    }
};
</script>

<style media="screen">

</style>
