<template lang="html">
    <div id="objectTransactionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transfer item</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="quantity">Quantity:</label>
                                <div class="col-sm-4">
                                    <input type="number" min="1" :max="total" v-model="quantity"
                                        class="form-control" id="quantity" name="quantity" required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" v-on:click="close" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" v-on:click="save">Transfer</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            quantity: 1
        };
    },
    props: ['id', 'item'],
    computed: {
        total: function() {
            return this.item == null ? 1 : this.item.total;
        }
    },
    methods: {
        close: function() {
            $('#objectTransactionModal .panel').removeClass('selected');
            this.item = null;
            this.id = null;
            this.quantity = 1;
            this.$emit('closed');
        },
        save: function() {
            $.post('./php/controllers/characterController.php', {
                action: 'giveItem',
                owner: this.id,
                character: this.idCharacter,
                item: this.item.idItem,
                quantity: this.quantity
            }, (json) => {
                let response = JSON.parse(json);
                if (response.status == 'OK') {
                    $('#objectTransactionModal .btn-danger').click();
                    this.close();
                } else {
                    console.error(response.error);
                }
            });
        }
    }
}
</script>

<style lang="css">
#objectTransactionModal #inputSearch {
    margin-bottom: 10px;
}
</style>
