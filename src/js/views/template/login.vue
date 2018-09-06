<template lang="html">
    <div id="loginModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-body">
                    <form>
                        <span v-if="this.errorMessage != null" class="error">{{ this.errorMessage }}</span>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="name" type="text" class="form-control" name="name" placeholder="Name">
                        </div>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="password" type="password" class="form-control" name="password" placeholder="Password">
                        </div>
                        <br>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" v-on:click="login">Login</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
var navigation = require('./../../navigation.js');
export default {
    data: function() {
        return {
            errorMessage: null
        };
    },
    methods: {
        login: function() {
            navigation.dispatch('login', {
                name: $('#loginModal input[name="name"]').val(),
                pass: $('#loginModal input[name="password"]').val()
            }).then(() => {
                if (navigation.state.session == null) {
                    this.errorMessage = 'Your user or password were incorrect.'
                } else {
                    this.close();
                    this.$router.push('/');
                }
            });
        },
        close: function() {
            this.errorMessage = null;
            $('#loginModal .modal-footer button.btn-danger').click();
            $('.modal-backdrop').remove();
        }
    }
}
</script>

<style lang="css">
    #loginModal span.error {
        display: block;
        color: red;
        text-align: center;
        margin: 10px 0;
        font-size: 14px;
    }
</style>
