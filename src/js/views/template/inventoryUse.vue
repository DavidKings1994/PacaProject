<template lang="html">
    <div id="inventoryUseModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Inventory</h4>
                </div>
                <div class="modal-body">
                    <div class="input-group" id="inputSearch">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                        <input id="search" type="text" class="form-control" name="search"
                            :placeholder="'Search item in inventory...'"
                            v-model="search">
                    </div>
                    <div class="well">
                        <div class="panel panel-default" v-for="item in itemsFiltered" v-on:click="select"
                        :data-id="item.idItem" :data-avaliable="item.total">
                            <div class="panel-body">
                                <img :src="item.image" :alt="item.description" width="50" height="50">
                            </div>
                            <div class="panel-footer">{{ item.name }} <span class="badge">{{ item.total }}</span></div>
                        </div>
                        <div v-if="itemsFiltered.length == 0">
                            There are no items in the inventory, sorry
                        </div>
                    </div>
                    <div class="row">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="quantity">Quantity:</label>
                                <div class="col-sm-4">
                                    <input type="number" min="1" :max="this.maxQuantity" v-model="quantity" class="form-control"
                                        id="quantity" name="quantity" required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" v-on:click="close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" v-on:click="save">Use item</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
var messageStore = require('./../../messages.js');
export default {
    data: function() {
        return {
            items: [],
            itemsFiltered: [],
            search: '',
            idSelected: null,
            quantity: 1,
            maxQuantity: 0
        };
    },
    props: ['idCharacter', 'idUser'],
    watch: {
        idCharacter: function() {
            this.loadItems();
        },
        idUser: function() {
            this.loadItems();
        },
        search: function() {
            if (this.search.trim() != '') {
                if (this.items.length > 0) {
                    this.itemsFiltered = $.grep(this.items, (item) => {
                        return item.name.toLowerCase().includes(this.search.toLowerCase());
                    });
                }
            } else {
                this.itemsFiltered = this.items;
            }
        }
    },
    methods: {
        select : function(event) {
            $('#inventoryUseModal .panel').removeClass('selected');
            $(event.target).closest('.panel').addClass('selected');
            this.idSelected = $(event.target).closest('.panel').attr('data-id');

            let selectedAvaliable = $('#inventoryUseModal .panel.selected').attr('data-avaliable');
            this.maxQuantity = (selectedAvaliable != null ? selectedAvaliable : 0);
            this.quantity = 1;
        },
        loadItems: function() {
            if (this.idUser != null) {
                $.post('./php/controllers/userController.php', {
                    action: 'getInventoryCount',
                    id: this.idUser
                }, (json) => {
                    let response = JSON.parse(json);
                    if (response.status == 'OK') {
                        this.items = response.items;
                        this.itemsFiltered = this.items;
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Unable to load user\'s items',
                            type: 'error'
                        });
                        console.error(response.error);
                    }
                });
            } else if (this.idCharacter != null) {
                $.post('./php/controllers/characterController.php', {
                    action: 'getInventoryCount',
                    id: this.idCharacter
                }, (json) => {
                    let response = JSON.parse(json);
                    if (response.status == 'OK') {
                        this.items = response.items;
                        this.itemsFiltered = this.items;
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Unable to load character\'s items',
                            type: 'error'
                        });
                        console.error(response.error);
                    }
                });
            }
        },
        close: function() {
            $('#inventoryUseModal .panel').removeClass('selected');
            this.idSelected = null;
            this.quantity = 1;
            this.$emit('closed');
        },
        save: function() {
            if (this.idSelected != null) {
                if (this.idUser != null) {
                    $.post('./php/controllers/userController.php', {
                        action: 'useItem',
                        user: this.idUser,
                        item: this.idSelected,
                        quantity: (this.quantity == null ? 1 : this.quantity)
                    }, (json) => {
                        let response = JSON.parse(json);
                        if (response.status == 'OK') {
                            $('#inventoryUseModal .btn-danger').click();
                            this.close();
                            messageStore.commit('addMessage', {
                                text: 'Items used',
                                type: 'info'
                            });
                        } else {
                            messageStore.commit('addMessage', {
                                text: 'Cannot use items. ' + response.error,
                                type: 'error'
                            });
                            console.error(response.error);
                        }
                    });
                } else if (this.idCharacter != null) {
                    $.post('./php/controllers/characterController.php', {
                        action: 'useItem',
                        character: this.idCharacter,
                        item: this.idSelected,
                        quantity: (this.quantity == null ? 1 : this.quantity)
                    }, (json) => {
                        let response = JSON.parse(json);
                        if (response.status == 'OK') {
                            $('#inventoryUseModal .btn-danger').click();
                            this.close();
                            messageStore.commit('addMessage', {
                                text: 'Items used',
                                type: 'info'
                            });
                        } else {
                            messageStore.commit('addMessage', {
                                text: 'Cannot use items. ' + response.error,
                                type: 'error'
                            });
                            console.error(response.error);
                        }
                    });
                }
            }
        }
    }
}
</script>

<style lang="css">
#inventoryUseModal #inputSearch {
    margin-bottom: 10px;
}
</style>
