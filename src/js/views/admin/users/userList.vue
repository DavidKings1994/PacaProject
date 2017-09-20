<template lang="html">
    <div class="content" id="userList">
        <div class="row text-right" id="upperBar">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#userFormModal">
                <i class="fa fa-plus"></i> Register new user
            </button>
        </div>
        <paca-admin-user-form
            :user="selectedUser"
        >
        </paca-admin-user-form>
        <paca-admin-user-currency v-for="user in users"
            :userId="user.idUser"
            :userName="user.userName"
            :userCurrency="user.currency"
            :date="today"
        >
        </paca-admin-user-currency>
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
            selectedUser: null
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
        loadUsers: function() {
            $.post('./php/controllers/usercontroller.php', {
                action: 'getUsers'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.users = json.users;
                    var checker = setTimeout(() => {
                        if ($('ul.dropdown-menu a[data-option="profile"]').length > 0) {
                            $('ul.dropdown-menu a[data-option="profile"]').click((event) => {
                                var id = $(event.target).attr('data-iduser');
                                this.selectedUser = $(this.users).filter(function(i,n) {
                                    return n.idUser == id;
                                })[0];
                                console.log(this.selectedUser);
                                $("#userFormModal").modal();
                            })
                            clearTimeout(checker);
                        }
                    }, 100);
                }
            });
        },
        renderStatusColumn: function (colname, entry) {
            return '<span class="label label-' + (entry.status == 'ACTIVE' ? 'success' : 'danger') + '">' + entry.status + '</span>';
        },
        renderOptionsColumn: function(colname, entry) {
            var target = "#balanceModal" + entry.idUser;
            return '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a href="#" data-iduser="' + entry.idUser + '" data-option="profile">Edit user</a></li>' +
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
        this.loadUsers();
    }
}
</script>

<style lang="css">
#userList #upperBar {
    padding: 30px;
}
</style>
