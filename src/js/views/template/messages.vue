<template lang="html">
    <div id="MessagesContainer" v-if="this.messages.length > 0">
        <message v-for="m in messages" :type="m.type" :message="m.message" :id="m.id" v-on:close="removeMessage"></message>
    </div>
</template>

<script>
var messageStore = require('./../../messages.js');
var Message = require('./message.js');
export default {
    components: {
        'message': Message
    },
    props: ['position'],
    computed: {
        messages: function() {
            return messageStore.state.messages;
        }
    },
    methods: {
        removeMessage: function(id) {
            
        }
    }
}
</script>

<style scoped lang="css">
:root {
    position: fixed;
    bottom: 0;
    right: 0;
    margin-right: 20px;
    margin-bottom: 10px;
}
</style>
