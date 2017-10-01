<template lang="html">
    <div id="ticketModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ticket {{ this.ticket.idTicket }}</h4>
                </div>
                <div class="modal-body">
                    <div class="ticketFrame">

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
export default {
    props: ['ticket'],
    methods: {
        save: function() {
            domtoimage.toPng($('#ticketModal .ticketFrame')[0])
            .then((dataUrl) => {
                var link = document.createElement('a');
                link.download = 'ticket ' + this.ticket.idTicket;
                link.href = dataUrl;
                link.click();
                $('#ticketModal .btn-danger').click();
                this.$emit('saved');
            })
            .catch(function (error) {
                console.error('oops, something went wrong!', error);
            });
        }
    }
}
</script>

<style lang="css">
</style>
