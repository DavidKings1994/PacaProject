<template lang="html">
    <div class="content">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Currency</th>
                    </tr>
                </thead>
                <tbody>
                    <tr :key="user.idUser" v-for="user in users">
                        <td>{{ user.idUser }}</td>
                        <td>{{ user.userName }}</td>
                        <td>{{ user.status }}</td>
                        <td>{{ user.currency }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
var navigation = require('./../../../navigation.js');
export default {
    data() {
        return {
            users: []
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
        }
    },
    created: function() {
        this.loadUsers();
    }
}
</script>

<style lang="css">
</style>
