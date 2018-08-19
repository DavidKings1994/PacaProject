<template lang="html">
    <div id="badgeFormModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Badge information</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <input type="hidden" name="action" :value="action">
                        <input type="hidden" name="badge" :value="badgeId">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Name:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter badge name" :value="this.badgeName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="desc">Description:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="desc" id="desc" placeholder="Enter badge description" :value="this.badgeDesc">
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="image">Image:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="image" id="image" placeholder="Enter badge image link" :value="this.badgeImage">
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
    props: ['badge'],
    computed: {
        badgeId: function() { return this.badge == null ? '' : this.badge.idBadge; },
        badgeName: function() { return this.badge == null ? '' : this.badge.name; },
        badgeDesc: function() { return this.badge == null ? '' : this.badge.description; },
        badgeImage: function() { return this.badge == null ? '' : this.badge.Image; },
        buttonText: function() { return this.badge == null ? 'Register' : 'Save'; },
        action: function() { return this.badge == null ? 'registerBadge' : 'updateBadge'; }
    },
    methods: {
        save: function() {
            $.post('./php/controllers/badgeController.php',
            $("#badgeFormModal form").serialize(),
            (json) => {
                let result = JSON.parse(json);
                console.log(result);
                if (result.status == 'OK') {
                    this.$emit('saved');
                    $('#badgeFormModal .btn-danger').click();
                    messageStore.commit('addMessage', {
                        text: 'Badge ' + (this.badge == null ? 'registered' : 'updated'),
                        type: 'success'
                    });
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to save badge\'s information. ' + result.error,
                        type: 'error'
                    });
                    console.error(result.error);
                }
            });
        }
    }
}
</script>

<style lang="css">
</style>
