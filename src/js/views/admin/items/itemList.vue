<template lang="html">
    <div class="content" id="itemList">
        <div class="row text-right" id="upperBar">
            <button type="button" class="btn btn-success" v-on:click="newItem">
                <i class="fa fa-plus"></i> Register new item
            </button>
        </div>
        <paca-admin-item-form
            :item="selectedItem"
        >
        </paca-admin-item-form>
        <paca-admin-item-currency v-for="item in items"
            :itemId="item.idItem"
            :itemName="item.itemName"
            :itemCurrency="item.currency"
            :date="today"
        >
        </paca-admin-item-currency>
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
var VueBootstrapTable  = require('vue-bootstrap-table');
export default {
    data() {
        return {
            columns: [
                {
                    name: "idObject",
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
                    // renderfunction: this.renderOptionsColumn
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
            $.post('./php/controllers/itemcontroller.php', {
                action: 'getItems'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.items = json.items;
                }
            });
        },
        newItem: function() {
            this.selectedItem = null;
            $('#itemFormModal').modal();
        },
        renderImageColumn: function (colname, entry) {
            return '<img src="' + entry.image + '" class="" alt="' + entry.name + '" width="50" height="50" />';
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                if ($('ul.dropdown-menu a[data-iditem="' + entry.idItem + '"]').length > 0) {
                    $('ul.dropdown-menu a[data-iditem="' + entry.idItem + '"]').click((event) => {
                        var id = $(event.target).attr('data-iditem');
                        this.selectedItem = $(this.items).filter(function(i,n) {
                            return n.idItem == id;
                        })[0];
                        $("#itemFormModal").modal();
                    })
                    clearTimeout(checker);
                }
            }, 100);
            var target = "#balanceModal" + entry.idItem;
            return '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a href="#" data-iditem="' + entry.idItem + '" data-option="profile">Edit item</a></li>' +
                    '<li class="divider"></li>' +
                    '<li class="dropdown-header">Download images</li>' +
                    '<li><a href="#" data-toggle="modal" data-target="' + target + '">Currency</a></li>' +
                    '<li><a href="#">Profile</a></li>' +
                    '<li class="dropdown-header">Transactions</li>' +
                    '<li><a href="#">Currency</a></li>' +
                    '<li><a href="#">Items</a></li>' +
                    '<li><a href="#">Badges</a></li>' +
                '</ul>' +
            '</div>';
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
