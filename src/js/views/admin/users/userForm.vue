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
                            <label class="control-label col-sm-2" for="email">Email:</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="status">Status:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="status">
                                    <option v-for="s in statusList" :value="s.idStatus">{{ s.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" v-if="this.user == null">
                            <label class="control-label col-sm-2" for="pwd">Password:</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="pwd" placeholder="Enter password">
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
export default {
    data: function() {
        return {
            statusList: []
        };
    },
    props: ['user'],
    computed: {
        userName: function() { return this.user.userName == null ? '' : this.user.userName; },
        status: function() { return this.user.status == null ? '' : this.user.status; },
        buttonText: function() { return this.user == null ? 'Register' : 'Save'; }
    },
    methods: {
        save: function() {
            $.post('./php/controllers/userController.php', {
                action: 'registerUser'
            }, (json) => {
                var result = JSON.parse(json);
                if (result.status == 'OK') {
                    $('#userFormModal .btn-danger').click();
                } else {

                }
            });
        },
        loadStatus: function() {
            $.post('./php/controllers/statusController.php', {
                action: 'getStatus'
            }, (json) => {
                var result = JSON.parse(json);
                if (result.status == 'OK') {
                    this.statusList = result.statusList;
                }
            });
        }
    },
    created: function() {
        this.loadStatus();
    }
}
</script>

<style lang="css">
</style>
