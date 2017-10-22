<template lang="html">
    <div id="logoutModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="modal-title">You really want to leave?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" v-on:click="logout">Logout</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
var navigation = require('./../../navigation.js');
export default {
    methods: {
        logout: function() {
            navigation.dispatch('logout');
            $('#logoutModal .modal-footer button.btn-danger').click();
        }
    }
}
</script>

<style lang="css">
</style>
