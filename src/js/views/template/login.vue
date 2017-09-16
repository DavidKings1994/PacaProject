<template lang="html">
    <div id="loginModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-body">
                    <form>
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
    mounted: function() {

    },
    methods: {
        login: function() {
            navigation.commit('login', {
                name: $('#loginModal input[name="name"]').val(),
                pass: $('#loginModal input[name="password"]').val()
            });
            $('#loginModal .modal-footer button.btn-danger').click();
        }
    }
}
</script>

<style lang="css">
</style>
