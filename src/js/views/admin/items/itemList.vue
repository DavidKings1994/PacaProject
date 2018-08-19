<template lang="html">
    <div class="content" id="itemList">
        <div class="row text-right" id="upperBar">
            <button type="button" class="btn btn-success" v-on:click="newItem">
                <i class="fa fa-plus"></i> Register new item
            </button>
        </div>
        <paca-admin-item-form
            :item="selectedItem"
            v-on:saved="loadItems"
        >
        </paca-admin-item-form>
        <paca-dialog
            :title="'Delete'"
            :message="'You really want to delete this item?'"
            :onAccept="deleteItem">
        </paca-dialog>
        <vue-bootstrap-table
            :columns="columns"
            :values="items"
            :show-filter="true"
            :show-column-picker="false"
            :sortable="true"
            :paginated="true"
            :multi-column-sortable="true"
            :filter-case-sensitive="false"
        >
        </vue-bootstrap-table>
    </div>
</template>

<script>
var navigation = require('./../../../navigation.js');
var messageStore = require('./../../../messages.js');
var VueBootstrapTable  = require('vue-bootstrap-table');
export default {
    data() {
        return {
            columns: [
                {
                    name: "idItem",
                    title: "ID"
                },
                {
                    name: "Image",
                    title: "Image",
                    renderfunction: this.renderImageColumn
                },
                {
                    name: "name",
                    title: "Name"
                },
                {
                    name: "description",
                    title: "Description"
                },
                {
                    name: "creationDate",
                    title: "Register date"
                },
                {
                    title: "Options",
                    visible: true,
                    renderfunction: this.renderOptionsColumn
                }
            ],
            items: [],
            selectedItem: null
        };
    },
    components: {
        VueBootstrapTable: VueBootstrapTable
    },
    computed: {
        today: function() {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1;
            var yyyy = today.getFullYear();

            if(dd<10) {
                dd = '0'+dd
            }

            if(mm<10) {
                mm = '0'+mm
            }

            return mm + '/' + dd + '/' + yyyy;
        }
    },
    methods: {
        loadItems: function() {
            $.post('./php/controllers/itemController.php', {
                action: 'getItems'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.items = json.items;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load item list',
                        type: 'warning'
                    });
                }
            });
        },
        newItem: function() {
            this.selectedItem = null;
            $('#itemFormModal').modal();
        },
        renderImageColumn: function (colname, entry) {
            return '<img src="' + entry.Image + '" class="" alt="' + entry.name + '" width="50" height="50" />';
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                if ($('ul.dropdown-menu a[data-iditem="' + entry.idItem + '"]').length > 0) {
                    $('ul.dropdown-menu a[data-iditem="' + entry.idItem + '"][data-option="profile"]').click((event) => {
                        var id = $(event.target).attr('data-iditem');
                        this.selectedItem = $(this.items).filter(function(i,n) {
                            return n.idItem == id;
                        })[0];
                        $("#itemFormModal").modal();
                    });
                    $('ul.dropdown-menu a[data-iditem="' + entry.idItem + '"][data-option="delete"]').click((event) => {
                        var id = $(event.target).attr('data-iditem');
                        this.selectedItem = $(this.items).filter(function(i,n) {
                            return n.idItem == id;
                        })[0];
                        $("#dialogModal").modal();
                    })
                    clearTimeout(checker);
                }
            }, 100);
            return '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a data-iditem="' + entry.idItem + '" data-option="profile">Edit item</a></li>' +
                    '<li><a data-iditem="' + entry.idItem + '" data-option="delete">Delete item</a></li>' +
                '</ul>' +
            '</div>';
        },
        deleteItem: function(callback) {
            $.post('./php/controllers/itemController.php', {
                action: 'deleteItem',
                item: this.selectedItem.idItem
            }, (json) => {
                let response = JSON.parse(json);
                if (response.status == 'OK') {
                    this.loadItems();
                    messageStore.commit('addMessage', {
                        text: 'Item ' + this.selectedItem.name + ' deleted',
                        type: 'info'
                    });
                    callback();
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to delete item ' + this.selectedItem.name + '. ' + response.error,
                        type: 'error'
                    });
                    console.error(response.error);
                }
            });
        }
    },
    created: function() {
        this.loadItems();
    }
}
</script>

<style lang="css">
#itemList #upperBar {
    padding: 30px;
}
</style>
