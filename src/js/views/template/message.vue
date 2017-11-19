<template lang="html">
    <div :class="'row message message-' + this.type" :data-id="id">
        <span class="col-xs-10">{{ message }}</span>
        <span class="col-xs-2" v-on:click="close">&times;</span>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            timmer: null,
            duration: 10000
        };
    },
    props: ['type', 'message', 'id'],
    methods: {
        open: function() {
            $('.message[data-id="' + this.id + '"]').fadeIn();
        },
        close: function() {
            clearTimeout(this.timmer);
            $('.message[data-id="' + this.id + '"]').fadeOut();
            this.$emit('close', this.id);
        }
    },
    mounted: function() {
        this.open();
        this.timmer = setTimeout(this.close, this.duration);
    }
}
</script>

<style lang="css">
.message {
    width: 300px;
    padding: 10px 20px;
    border-radius: 5px;
    display:none;
    margin: 5px;
}

.message.message-error {
    background: red;
    border: solid 3px black;
    color: white;
}

.message.message-warning {
    background: yellow;
    border: solid 3px black;
    color: rgb(50,50,50);
}

.message.message-info {
    background: blue;
    border: solid 3px black;
    color: white;
}

.message.message-success {
    background: green;
    border: solid 3px black;
    color: white;
}
</style>
