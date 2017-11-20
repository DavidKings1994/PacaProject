<template lang="html">
    <div id="MessagesContainer" v-if="messages.length > 0">
        <message
            v-for="m in messages"
            :key="m.id"
            :type="m.type"
            :message="m.message"
            :id="m.id"
            v-on:close="removeMessage">
        </message>
    </div>
</template>

<script>
var messageStore = require('./../../messages.js');
export default {
    props: ['position'],
    computed: {
        messages: function() {
            return messageStore.state.messages;
        }
    },
    methods: {
        removeMessage: function(id) {
            messageStore.commit('removeMessage', id);
        }
    }
}
</script>

<style lang="css">
#MessagesContainer {
    position: fixed;
    bottom: 0;
    right: 0;
    margin-right: 20px;
    margin-bottom: 10px;
    z-index: 1100;
}
</style>
