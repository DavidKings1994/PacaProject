<template lang="html">
    <div class="content" id="itemList">
        <div class="row text-right" id="upperBar">
        </div>
        <paca-user-item-transaction
            :owner="id"
            :item="selectedItem"
        >
        </paca-user-item-transaction>
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
                    name: "total",
                    title: "Quantity"
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
    props: ['id'],
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
            $.post('./php/controllers/userController.php', {
                action: 'getInventoryCount',
                id: this.id
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.items = json.items;
                }
            });
        },
        renderImageColumn: function (colname, entry) {
            return '<img src="' + entry.image + '" class="" alt="' + entry.name + '" width="50" height="50" />';
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                if ($('ul.dropdown-menu a[data-iditem="' + entry.idItem + '"]').length > 0) {
                    $('ul.dropdown-menu a[data-iditem="' + entry.idItem + '"][data-option="transfer"]').click((event) => {
                        var id = $(event.target).attr('data-iditem');
                        this.selectedItem = $(this.items).filter(function(i,n) {
                            return n.idItem == id;
                        })[0];
                        $("#objectTransactionModal").modal();
                    });
                    clearTimeout(checker);
                }
            }, 100);
            return '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a data-iditem="' + entry.idItem + '" data-option="transfer">Transfer item</a></li>'
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
