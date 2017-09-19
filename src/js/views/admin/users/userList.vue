<template lang="html">
    <div class="content" id="userList">
        <!-- <div class="table-responsive">
            <paginate name="users" :list="users" :per="10" tag="div">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-1">ID</th>
                            <th class="col-md-5">Name</th>
                            <th class="col-md-2">Status</th>
                            <th class="col-md-2">Currency</th>
                            <th class="col-md-2">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr :key="user.idUser" v-for="user in paginated('users')">
                            <td>{{ user.idUser }}</td>
                            <td>{{ user.userName }}</td>
                            <td><span class="label label-success">{{ user.status }}</span></td>
                            <td>{{ user.currency }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                        Options
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-header">Download images</li>
                                        <li><a href="#" data-toggle="modal" :data-target="'#balanceModal' + user.idUser">Currency</a></li>
                                        <li><a href="#">Profile</a></li>
                                        <li class="dropdown-header">Transactions</li>
                                        <li><a href="#">Currency</a></li>
                                        <li><a href="#">Items</a></li>
                                        <li><a href="#">Badges</a></li>
                                    </ul>
                                </div>
                            </td>
                            <paca-admin-user-currency
                                :userId="user.idUser"
                                :userName="user.userName"
                                :userCurrency="user.currency"
                                :date="today"
                            >
                            </paca-admin-user-currency>
                        </tr>
                    </tbody>
                </table>
            </paginate>
            <div class="text-center">
                <paginate-links for="users" :hide-single-page="true" :classes="{'ul': 'pagination'}">
                </paginate-links>
            </div>
        </div> -->
        <vue-bootstrap-table
            :columns="columns"
            :values="users"
            :show-filter="true"
            :show-column-picker="true"
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
                    renderfunction: this.renderOptionsColumn
                }
            ],
            users: []
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
                }
            });
        },
        renderStatusColumn: function (colname, entry) {
            return '<span class="label label-' + (entry.status == 'ACTIVE' ? 'success' : 'danger') + '">' + entry.status + '</span>';
        },
        renderOptionsColumn: function(colname, entry) {
            return
            '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li class="dropdown-header">Download images</li>' +
                    '<li><a href="#" data-toggle="modal" :data-target="\'#balanceModal\'' + entry.idUser + '">Currency</a></li>' +
                    '<li><a href="#">Profile</a></li>' +
                    '<li class="dropdown-header">Transactions</li>' +
                    '<li><a href="#">Currency</a></li>' +
                    '<li><a href="#">Items</a></li>' +
                    '<li><a href="#">Badges</a></li>' +
                '</ul>' +
                '<paca-admin-user-currency' +
                    ':userId="' + entry.idUser + '"' +
                    ':userName="' + entry.userName + '"' +
                    ':userCurrency="' + entry.currency + '"' +
                    ':date="' + today + '"' +
                '>' +
                '</paca-admin-user-currency>' +
            '</div>';
        }
    },
    created: function() {
        this.loadUsers();
    }
}
</script>

<style lang="css">
</style>
