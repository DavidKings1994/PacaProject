<template lang="html">
    <div id="inventoryTransactionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ transactionType }}</h4>
                </div>
                <div class="modal-body">
                    <div class="input-group" id="inputSearch">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                        <input id="search" type="text" class="form-control" name="search"
                            :placeholder="'Search ' + (this.transactionType == 'Give item' ? 'item' : 'badge') + '...'"
                            v-model="search">
                    </div>
                    <div class="well">
                        <div class="panel panel-default" v-for="item in itemsFiltered" v-on:click="select" :data-id="item.idItem">
                            <div class="panel-body">
                                <img :src="item.Image" :alt="item.description" width="50" height="50">
                            </div>
                            <div class="panel-footer">{{ item.name }}</div>
                        </div>
                        <div class="panel panel-default" v-for="badge in badgesFiltered" v-on:click="select" :data-id="badge.idBadge">
                            <div class="panel-body">
                                <img :src="badge.Image" :alt="badge.description" width="50" height="50">
                            </div>
                            <div class="panel-footer">{{ badge.name }}</div>
                        </div>
                    </div>
                    <div class="container" v-if="this.transactionType == 'Give item'">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="quantity">Quantity:</label>
                                <div class="col-sm-4">
                                    <input type="number" min="1" max="99" v-model="quantity" class="form-control"
                                        id="quantity" name="quantity" required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" v-on:click="close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" v-on:click="save">Give</button>
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
            badges: [],
            badgesFiltered: [],
            search: '',
            idSelected: null,
            quantity: 1
        };
    },
    props: ['idCharacter', 'idUser', 'transaction'],
    watch: {
        transaction: function() {
            switch (this.transaction) {
                case 'giveItem': {
                    this.badges = [];
                    this.badgesFiltered = this.badges;
                    this.loadItems();
                    break;
                }
                case 'giveBadge': {
                    this.items = [];
                    this.itemsFiltered = this.items;
                    this.loadBadges();
                    break;
                }
            }
        },
        search: function() {
            if (this.search.trim() != '') {
                if (this.items.length > 0) {
                    this.itemsFiltered = $.grep(this.items, (item) => {
                        return item.name.toLowerCase().includes(this.search.toLowerCase());
                    });
                } else if (this.badges.length > 0) {
                    this.badgesFiltered = $.grep(this.badges, (badge) => {
                        return badge.name.toLowerCase().includes(this.search.toLowerCase());
                    });
                }
            } else {
                this.itemsFiltered = this.items;
                this.badgesFiltered = this.badges;
            }
        }
    },
    computed: {
        transactionType: function() {
            return (this.transaction == null ? '' : (this.transaction == 'giveItem' ? 'Give item' : 'Give Badge'));
        }
    },
    methods: {
        select : function(event) {
            $('#inventoryTransactionModal .panel').removeClass('selected');
            $(event.target).closest('.panel').addClass('selected');
            this.idSelected = $(event.target).closest('.panel').attr('data-id');
        },
        loadBadges: function() {
            $.post('./php/controllers/badgeController.php', {
                action: 'getBadges'
            }, (json) => {
                let response = JSON.parse(json);
                if (response.status == 'OK') {
                    this.badges = response.badges;
                    this.badgesFiltered = this.badges;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load badges',
                        type: 'error'
                    });
                }
            });
        },
        loadItems: function() {
            $.post('./php/controllers/itemController.php', {
                action: 'getItems'
            }, (json) => {
                let response = JSON.parse(json);
                if (response.status == 'OK') {
                    this.items = response.items;
                    this.itemsFiltered = this.items;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load items',
                        type: 'error'
                    });
                }
            });
        },
        close: function() {
            $('#inventoryTransactionModal .panel').removeClass('selected');
            this.idSelected = null;
            this.quantity = 1;
            this.$emit('closed');
        },
        save: function() {
            if (this.idSelected != null) {
                if (this.idUser != null) {
                    $.post('./php/controllers/userController.php', {
                        action: 'giveItem',
                        user: this.idUser,
                        item: this.idSelected,
                        quantity: (this.quantity == null ? 1 : this.quantity)
                    }, (json) => {
                        let response = JSON.parse(json);
                        if (response.status == 'OK') {
                            $('#inventoryTransactionModal .btn-danger').click();
                            this.close();
                            messageStore.commit('addMessage', {
                                text: 'Items given to user',
                                type: 'success'
                            });
                        } else {
                            messageStore.commit('addMessage', {
                                text: 'Unable to give items to user. ' + response.error,
                                type: 'error'
                            });
                            console.error(response.error);
                        }
                    });
                } else if (this.idCharacter != null) {
                    switch (this.transaction) {
                        case 'giveItem': {
                            $.post('./php/controllers/characterController.php', {
                                action: 'giveItem',
                                character: this.idCharacter,
                                item: this.idSelected,
                                quantity: this.quantity
                            }, (json) => {
                                let response = JSON.parse(json);
                                if (response.status == 'OK') {
                                    $('#inventoryTransactionModal .btn-danger').click();
                                    this.close();
                                    messageStore.commit('addMessage', {
                                        text: 'Items given to character',
                                        type: 'success'
                                    });
                                } else {
                                    messageStore.commit('addMessage', {
                                        text: 'Unable to give items to character. ' + response.error,
                                        type: 'error'
                                    });
                                    console.error(response.error);
                                }
                            });
                            break;
                        }
                        case 'giveBadge': {
                            $.post('./php/controllers/characterController.php', {
                                action: 'giveBadge',
                                character: this.idCharacter,
                                badge: this.idSelected
                            }, (json) => {
                                let response = JSON.parse(json);
                                if (response.status == 'OK') {
                                    $('#inventoryTransactionModal .btn-danger').click();
                                    this.close();
                                    messageStore.commit('addMessage', {
                                        text: 'Badge given to character',
                                        type: 'success'
                                    });
                                } else {
                                    messageStore.commit('addMessage', {
                                        text: 'Unable to give badge to character. ' + response.error,
                                        type: 'error'
                                    });
                                    console.error(response.error);
                                }
                            });
                            break;
                        }
                    }
                }
            }
        }
    }
}
</script>

<style lang="css">
#inventoryTransactionModal #inputSearch {
    margin-bottom: 10px;
}
</style>
