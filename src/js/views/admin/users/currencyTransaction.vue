<template lang="html">
    <div id="currencyTransactionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bank operation</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="operation">Operation:</label>
                                <div class="col-sm-4">
                                    <bootstrap-toggle
                                        id="operation"
                                        v-model="checked"
                                        :options="{
                                            on: 'Give',
                                            off: 'Charge',
                                            onstyle: 'success',
                                            offstyle: 'danger',
                                            width: 85
                                        }" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="quantity">Quantity:</label>
                                <div class="col-sm-4">
                                    <input type="number" min="1" v-model="amount" class="form-control"
                                        id="quantity" name="quantity" required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" v-on:click="reset">Close</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" v-on:click="save">Save</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import BootstrapToggle from 'vue-bootstrap-toggle';
export default {
    data: function() {
        return {
            checked: false,
            amount: 0
        };
    },
    components: {
        'bootstrap-toggle': BootstrapToggle
    },
    props: ['idUser'],
    methods: {
        reset: function() {
            this.checked = false;
            this.amount = 0;
        },
        save: function() {
            $.post('./php/controllers/bankController.php', {
                action: 'currencyOperation',
                operation: (  this.checked ? 'giveCurrency' : 'chargeCurrency' ),
                amount: this.amount,
                user: this.idUser
            }, (json) => {
                let response = JSON.parse(json);
                if (response.status == 'OK') {
                    this.reset();
                    this.$emit('saved');
                } else {
                    console.error(response.error);
                }
            })
        }
    }
}
</script>

<style lang="css">
</style>
