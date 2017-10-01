<template lang="html">
    <div id="dialogModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header" v-if="this.title != null">
                    <h4 class="modal-title">{{ this.title }}</h4>
                </div>
                <div class="modal-body">
                    <p>{{ this.message }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" v-on:click="onAccept(this.closeModal)" class="btn btn-success" data-dismiss="modal">Accept</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    props: ['onAccept','title','message'],
    methods: {
        closeModal: function() {
            $('#dialogModal btn-danger').click();
        }
    }
}
</script>

<style lang="css">
</style>
