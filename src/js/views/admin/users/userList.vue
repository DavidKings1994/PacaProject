<template lang="html">
    <div class="content" id="userList">
        <div class="row text-right" id="upperBar">
            <button type="button" class="btn btn-success" v-on:click="newUser">
                <i class="fa fa-plus"></i> Register new user
            </button>
        </div>
        <paca-admin-user-form
            :user="selectedUser"
            v-on:saved="loadUsers">
        </paca-admin-user-form>
        <paca-admin-user-currency v-for="user in users"
            :userId="user.idUser"
            :userName="user.userName"
            :userCurrency="user.currency"
            :date="today">
        </paca-admin-user-currency>
        <paca-admin-currency-transaction
            :idUser="idUser"
            v-on:saved="loadUsers"
            v-on:closed="resetInventory">
        </paca-admin-currency-transaction>
        <paca-inventory-transaction
            :idUser="idUser"
            :transaction="transaction"
            v-on:closed="resetInventory"
        >
        </paca-inventory-transaction>
        <paca-inventory-use
            :idUser="idUser"
            v-on:closed="resetInventory"
        >
        </paca-inventory-use>
        <paca-inventory
            :user="selectedUser"
            v-on:closed="resetInventory"
        >
        </paca-inventory>
        <vue-bootstrap-table
            :columns="columns"
            :values="users"
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
                    name: "idUser",
                    title: "ID"
                },
                {
                    name: "userName",
                    title: "Name"
                },
                {
                    name: "status",
                    title: "Status",
                    renderfunction: this.renderStatusColumn
                },
                {
                    name: "currency",
                    title: "Currency"
                },
                {
                    name: "options",
                    title: "Options",
                    visible: true,
                    renderfunction: this.renderOptionsColumn
                }
            ],
            users: [],
            selectedUser: null,
            transaction: null
        };
    },
    components: {
        VueBootstrapTable: VueBootstrapTable
    },
    computed: {
        idUser: function() {
            return (this.selectedUser == null ? null : this.selectedUser.idUser);
        },
        today: function() {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1;
            var yyyy = today.getFullYear();
            var hh = today.getHours();
            var Mm = today.getMinutes();

            if(dd<10) {
                dd = '0'+dd
            }

            if(mm<10) {
                mm = '0'+mm
            }

            return mm + '/' + dd + '/' + yyyy + ' ' + hh + ':' + Mm;
        }
    },
    methods: {
        resetInventory: function() {
            this.selectedUser = null;
        },
        loadUsers: function() {
            $.post('./php/controllers/usercontroller.php', {
                action: 'getUsers'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.users = json.users;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load users',
                        type: 'warning'
                    });
                }
            });
        },
        newUser: function() {
            this.selectedUser = null;
            $('#userFormModal').modal();
        },
        renderStatusColumn: function (colname, entry) {
            return '<span class="label label-' + (entry.status == 'ACTIVE' ? 'success' : 'danger') + '">' + entry.status + '</span>';
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                if ($('ul.dropdown-menu a[data-iduser="' + entry.idUser + '"]').length > 0) {
                    // set up the form button
                    $('ul.dropdown-menu a[data-iduser="' + entry.idUser + '"][data-option="profile"]').click((event) => {
                        var id = $(event.target).attr('data-iduser');
                        this.selectedUser = null;
                        this.selectedUser = $(this.users).filter(function(i,n) {
                            return n.idUser == id;
                        })[0];
                        $("#userFormModal").modal();
                    });
                    // set up the give-item button
                    $('ul.dropdown-menu a[data-iduser="' + entry.idUser + '"][data-option="giveItem"]').click((event) => {
                        var id = $(event.target).attr('data-iduser');
                        this.selectedUser = null;
                        this.selectedUser = $(this.users).filter(function(i,n) {
                            return n.idUser == id;
                        })[0];
                        this.transaction = 'giveItem';
                        $("#inventoryTransactionModal").modal();
                    });
                    // set up the inventory button
                    $('ul.dropdown-menu a[data-iduser="' + entry.idUser + '"][data-option="inventory"]').click((event) => {
                        var id = $(event.target).attr('data-iduser');
                        this.selectedUser = null;
                        this.selectedUser = $(this.users).filter(function(i,n) {
                            return n.idUser == id;
                        })[0];
                        $("#inventoryModal").modal();
                    });
                    // set up the currency-transaction button
                    $('ul.dropdown-menu a[data-iduser="' + entry.idUser + '"][data-option="currency"]').click((event) => {
                        var id = $(event.target).attr('data-iduser');
                        this.selectedUser = null;
                        this.selectedUser = $(this.users).filter(function(i,n) {
                            return n.idUser == id;
                        })[0];
                        $("#currencyTransactionModal").modal();
                    });
                    // set up the inventory use button
                    $('ul.dropdown-menu a[data-iduser="' + entry.idUser + '"][data-option="useItem"]').click((event) => {
                        var id = $(event.target).attr('data-iduser');
                        this.selectedUser = null;
                        this.selectedUser = $(this.users).filter(function(i,n) {
                            return n.idUser == id;
                        })[0];
                        $("#inventoryUseModal").modal();
                    });
                    clearTimeout(checker);
                }
            }, 100);
            var target = "#balanceModal" + entry.idUser;
            return '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options ' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a data-iduser="' + entry.idUser + '" data-option="profile">Edit user</a></li>' +
                    '<li class="divider"></li>' +
                    '<li class="dropdown-header">Download images</li>' +
                    '<li><a data-toggle="modal" data-target="' + target + '">Currency</a></li>' +
                    '<li><a data-iduser="' + entry.idUser + '" data-option="inventory">Inventory</a></li>' +
                    '<li><a>Profile</a></li>' +
                    '<li class="dropdown-header">Transactions</li>' +
                    '<li><a data-iduser="' + entry.idUser + '" data-option="currency">Currency</a></li>' +
                    '<li><a data-iduser="' + entry.idUser + '" data-option="giveItem">Give item</a></li>' +
                    '<li><a data-iduser="' + entry.idUser + '" data-option="useItem">Use item</a></li>' +
                '</ul>' +
            '</div>';
        }
    },
    created: function() {
        this.loadUsers();
    }
}
</script>

<style lang="css">
#userList #upperBar {
    padding: 30px;
}
</style>
