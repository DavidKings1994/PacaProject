<template lang="html">
    <div id="logModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Log {{ this.log.idLog }}</h4>
                </div>
                <div class="modal-body">
                    <div class="logFrame">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" v-on:click="save">Save image</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
var domtoimage = require('dom-to-image');
var messageStore = require('./../../../messages.js');
export default {
    props: ['log'],
    methods: {
        save: function() {
            domtoimage.toPng($('#logModal .logFrame')[0])
            .then((dataUrl) => {
                var link = document.createElement('a');
                link.download = 'log ' + this.log.idLog;
                link.href = dataUrl;
                link.click();
                $('#logModal .btn-danger').click();
                this.$emit('saved');
            })
            .catch(function (error) {
                messageStore.commit('addMessage', {
                    text: 'Sorry, an error occurred while creating the image. Error: ' + error,
                    type: 'error'
                });
                console.error('oops, something went wrong!', error);
            });
        }
    }
}
</script>

<style lang="css">
</style>
