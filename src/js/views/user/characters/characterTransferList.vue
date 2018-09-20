<template id="">
    <div class="container-fluid" id="requestList">
        <div class="well" v-if="!hasNotifications">
            <h4 style="text-align: center;">Nothing here yet!</h4>
        </div>
        <div class="panel panel-default" v-for="notification in notifications" :data-id="notification.id" v-if="notification.status == 1">
          <div class="panel-heading row">
              <div class="col-md-2">
                  <img :src="notification.characterImage" class="media-object" style="width:100px">
              </div>
              <div class="col-md-10">
                  <h4>{{ notification.characterName }}</h4>
                  <p>owner: <b>{{ notification.ownerName }}</b><span class="date">{{ notification.date }}</span></p>
              </div>
          </div>
          <div class="panel-body">
              <button type="button" name="accept" class="btn btn-success" v-on:click="updateRequest(notification.id, 3)">Accept</button>
              <button type="button" name="decline" class="btn btn-danger" v-on:click="updateRequest(notification.id, 2)">Decline</button>
          </div>
        </div>
    </div>
</template>

<script type="text/javascript">
var navigation = require('./../../../navigation.js');
var messageStore = require('./../../../messages.js');
export default {
    data: function() {
        return {
            notifications: []
        }
    },
    computed: {
        logged: function() {
            return navigation.state.session != null;
        },
        userId: function() {
            return this.logged ? navigation.state.session.idUser : null;
        },
        hasNotifications: function() {
            return this.notifications.find(n => { return n.status == 1; });
        }
    },
    methods: {
        loadNotifications: function() {
            $.post('./php/controllers/userController.php', {
                action: 'getNotifications',
                user: this.userId
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.$set(this, 'notifications', json.notifications);
                } else {
                    console.error(msg.error);
                }
            });
        },
        updateRequest: function(id, status) {
            $.post('./php/controllers/characterController.php', {
                action: 'updateTransferRequest',
                request: id,
                status: status
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.loadNotifications();
                    messageStore.commit('addMessage', {
                        text: (status == 3 ? 'The character is now yours!' : 'Request declined'),
                        type: (status == 3 ? 'success' : 'info')
                    });
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Ups! something went wrong.',
                        type: 'error'
                    });
                    console.error(msg.error);
                }
            });
        }
    },
    created: function() {
        var pusher = new Pusher('1f4e2261136ad4420076', {
            cluster: 'us2'
        });

        var channel = pusher.subscribe('sirnusNotifications');
        channel.bind('newNotification', (data) => {
            this.loadNotifications();
        });
    },
    mounted: function() {
        this.loadNotifications();
    }
}
</script>

<style media="screen">
#requestList {
    margin: 20px 0;
}

#requestList .date{
    float: right;
}
</style>
