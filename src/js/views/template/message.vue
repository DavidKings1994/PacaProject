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
            $('.message[data-id="' + this.id + '"]').fadeOut("slow");
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
    padding: 10px;
    border-radius: 5px;
    display:none;
    margin: 5px;
    color: #ffffff;
}

.message.message-error {
    background: #ac2925;
    border: solid 3px #761c19;
}

.message.message-warning {
    background: #f0ad4e;
    border: solid 3px #eea236;
}

.message.message-info {
    background: #5bc0de;
    border: solid 3px #46b8da;
}

.message.message-success {
    background: #5cb85c;
    border: solid 3px #4cae4c;
}
</style>
