<template lang="html">
    <div class="content" id="userList">
        <div class="table-responsive">
            <paginate name="users" :list="users" :per="2" tag="div">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Currency</th>
                            <th>Options</th>
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
                                        <li><a href="#">Currency</a></li>
                                        <li><a href="#">Profile</a></li>
                                        <li class="dropdown-header">Transactions</li>
                                        <li><a href="#">Currency</a></li>
                                        <li><a href="#">Items</a></li>
                                        <li><a href="#">Badges</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </paginate>
            <div class="text-center">
                <paginate-links for="users" :hide-single-page="true" :classes="{'ul': 'pagination'}">
                </paginate-links>
            </div>
        </div>
        <button type="button" class="btn btn-success" v-on:click="save">Save image</button>
    </div>
</template>

<script>
var navigation = require('./../../../navigation.js');
var domtoimage = require('dom-to-image');
var VuePaginate  = require('vue-paginate');
export default {
    data() {
        return {
            users: [],
            paginate: ['users']
        };
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
        save: function() {
            domtoimage.toPng($('.table')[0])
            .then(function (dataUrl) {
                var link = document.createElement('a');
                link.download = 'my-image-name.jpeg';
                link.href = dataUrl;
                link.click();
            })
            .catch(function (error) {
                console.error('oops, something went wrong!', error);
            });
        }
    },
    created: function() {
        this.loadUsers();
    }
}
</script>

<style lang="css">
</style>
