<template lang="html">
    <div id="logModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Log {{ log.idLog }}</h4>
                </div>
                <div class="modal-body">
                    <div class="logFrame">
                        <div id="title">{{ transactionType }}</div>
                        <div id="itemsFrame" v-if="log.quantity != null && log.quantity <= 10">
                            <div
                                v-for="n in parseInt(log.quantity)"
                                :style="'background: url(' + log.itemImage + ');'">
                            </div>
                        </div>
                        <div id="itemsFrame" v-if="log.quantity != null && log.quantity > 10" class="">
                            <div :style="'background: url(' + log.itemImage + ');'">
                                <span>x{{ log.quantity }}</span>
                            </div>
                        </div>
                        <div class="currencyTransfered">
                            None
                        </div>
                        <div class="logFooter">
                            <span>{{ log.date }}</span>
                            <span>{{ parties }}</span>
                        </div>
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
var filesaver = require('file-saver');
var messageStore = require('./../../../messages.js');
export default {
    props: ['log'],
    computed: {
        parties: function() {
            let description = 'From: ';
            if (this.log.userOrigin != null) {
                description += this.log.userOrigin;
            } else {
                description += 'Bank';
            }
            description += ' / To: ';
            if (this.log.characterDestiny != null) {
                description += this.log.characterOwner + '\'s ' + this.log.characterDestiny;
            } else if (this.log.userDestiny != null) {
                description += this.log.userDestiny;
            }
            return description;
        },
        transactionType: function() {
            return this.log.userOrigin == null ? 'Deposit' : 'Transfer';
        }
    },
    methods: {
        save: function() {
            domtoimage.toBlob($('#logModal .logFrame')[0], {
                height: 600,
                width: 800
            })
            .then((blob) => {
                filesaver.saveAs(blob, 'log ' + this.log.idLog + '.png');
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
