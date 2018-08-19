<template lang="html">
    <div :id="'balanceModal' + this.userId" class="modal fade balanceModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User currency</h4>
                </div>
                <div class="modal-body">
                    <div class="logFrame">
                        <div id="title">{{ this.userName }}</div>
                        <h3>${{ this.userCurrency }}</h3>
                        <div class="logFooter">
                            {{ this.date }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" v-on:click="save">Save image</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
var navigation = require('./../../../navigation.js');
var messageStore = require('./../../../messages.js');
var domtoimage = require('dom-to-image');
var filesaver = require('file-saver');
export default {
    props: [
        'userId',
        'userName',
        'userCurrency',
        'date'
    ],
    methods: {
        save: function() {
            domtoimage.toBlob($('#balanceModal' + this.userId + ' .logFrame')[0], {
                height: 600,
                width: 800
            })
            .then((blob) => {
                filesaver.saveAs(blob, this.userName + ' balance ' + this.date + '.png');
                $('#balanceModal' + this.userId + ' .btn-danger').click();
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
