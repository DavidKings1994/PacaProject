<template id="">
    <div class="jumbotron" id="restorePasswordMessage">
        <form class="offset-md-1 col-md-10" v-if="tempkey == null">
            <div class="form-group">
                <p>Enter your user name bellow to send you a link to your e-mail for you to change your password</p>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" id="username" placeholder="User name" required="true">
            </div>
            <button type="button" class="btn btn-success" v-on:click="sendPaswordRestoreRequest">Submit</button>
        </form>
        <form class="offset-md-1 col-md-10" v-else>
            <div class="form-group">
                <p>Enter your new password bellow</p>
            </div>
            <div class="form-group">
                <input type="password" autocomplete="new-password" class="form-control" id="pass" placeholder="Password" required="true">
            </div>
            <div class="form-group">
                <input type="password" autocomplete="new-password" class="form-control" id="newpass" placeholder="Confirm password" required="true">
            </div>
            <button type="button" class="btn btn-success" v-on:click="sendNewPassword">Submit</button>
        </form>
    </div>
</template>

<script type="text/javascript">
var messageStore = require('./../../messages.js');
export default {
    data: function() {
        return {
            idUser: null
        }
    },
    props: ['tempkey'],
    methods: {
        sendNewPassword: function() {
            if (this.idUser != null) {
                if (/([^\s])/.test($('input#pass').val().trim()) && /([^\s])/.test($('input#newpass').val())) {
                    if ($('input#pass').val() == $('input#newpass').val()) {
                        let newPass = $('input#pass').val();
                        $.post('./php/controllers/userController.php', {
                            action: 'changePassword',
                            id: this.idUser,
                            newPass: newPass
                        }, (msg) => {
                            let json = JSON.parse(msg);
                            if (json.status != 'ERROR') {
                                messageStore.commit('addMessage', {
                                    text: 'Password changed',
                                    type: 'success'
                                });
                                this.$router.push('/');
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
        sendPaswordRestoreRequest: function() {
            let user = $('input#username').val().trim();
            if (user != '') {
                $.post('./php/controllers/userController.php',{
                    action: 'restorePassword',
                    user: user
                }, (msg) => {
                    let json = JSON.parse(msg);
                    if (json.status != 'ERROR') {
                        messageStore.commit('addMessage', {
                            text: 'An e-mail has been send to you',
                            type: 'success'
                        });
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Ups! User does\'t exist or does not have an e-mail associated to his account',
                            type: 'error'
                        });
                    }
                });
            }
        }
    },
    created: function() {
        if (this.tempkey != null) {
            $.post('./php/controllers/userController.php', {
                action: 'checkPasswordRestoreKey',
                key: this.tempkey
            }, (msg) => {
                let json = JSON.parse(msg);
                if (json.status == 'ERROR') {
                    this.$router.push('/passwordRestore');
                    messageStore.commit('addMessage', {
                        text: 'Ups! the link has expired of doesn\'t exist',
                        type: 'error'
                    });
                } else {
                    this.$set(this, 'idUser', json.user);
                }
            });
        }
    }
}
</script>

<style language="sass">
#restorePasswordMessage {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    max-width: 90vw;
    width: 700px;
}
</style>
