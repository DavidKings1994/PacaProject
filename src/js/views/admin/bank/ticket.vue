<template lang="html">
    <div id="ticketModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ticket {{ this.ticket.idTicket }}</h4>
                </div>
                <div class="modal-body">
                    <div class="logFrame">
                        <div id="title">
                            {{ this.ticket.type }}
                        </div>
                        <div id="itemsFrame">
                            None
                        </div>
                        <div class="currencyTransfered">
                            ${{ this.ticket.currency }}
                        </div>
                        <div class="logFooter">
                            <span>{{ this.ticket.date }}</span>
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
    props: ['ticket'],
    computed: {
        parties: function() {
            let description = '';
            let from = '';
            let to = '';
            if (this.ticket.userOrigin != null) {
                from = this.ticket.userOrigin;
            } else {
                from = 'Bank';
            }
            if (this.ticket.characterDestiny != null) {
                to = this.ticket.characterOwner + '\'s ' + this.ticket.characterDestiny;
            } else if (this.ticket.userDestiny != null) {
                to = this.ticket.userDestiny;
            }
            if (this.ticket.type == 'DEPOSIT') {
                description = 'From: ' + from + ' / To: ' + to;
            } else {
                description = 'From: ' + to + ' / To: ' + from;
            }
            return description;
        }
    },
    methods: {
        save: function() {
            domtoimage.toBlob($('#ticketModal .logFrame')[0], {
                height: 600,
                width: 800
            })
            .then((blob) => {
                filesaver.saveAs(blob, 'ticket ' + this.ticket.idTicket + '.png');
                $('#ticketModal .btn-danger').click();
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
