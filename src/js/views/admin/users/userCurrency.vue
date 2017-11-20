<template lang="html">
    <div :id="'balanceModal' + this.userId" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User currency</h4>
                </div>
                <div class="modal-body">
                    <div class="currencyFrame">
                        <div class="data">
                            <h4>Balance</h4>
                            <h2>{{ this.userName }}</h2>
                            <h3>${{ this.userCurrency }}</h3>
                        </div>
                        <div class="currencyFooter">
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
export default {
    props: [
        'userId',
        'userName',
        'userCurrency',
        'date'
    ],
    methods: {
        save: function() {
            domtoimage.toPng($('#balanceModal' + this.userId + ' .currencyFrame')[0])
            .then((dataUrl) => {
                var link = document.createElement('a');
                link.download = this.userName + ' balance ' + this.date;
                link.href = dataUrl;
                link.click();
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
