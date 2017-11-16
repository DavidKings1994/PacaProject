<template lang="html">
    <div :class="'message message-' + this.type" :data-id="id">
        <span :class="this.type">{{ message }}</span>
        <span v-on:click="close">&times;</span>
    </div>
</template>

<script>
export default {
    data: function() {
        timmer: null,
        duration: 10000
    },
    props: ['type', 'message', 'id'],
    methods: {
        open: function() {
            $('.message[data-id="' + this.id + '"]').fadeIn();
        },
        close: function() {
            clearTimeout(this.timmer);
            $('.message[data-id="' + this.id + '"]').fadeOut();
        }
    },
    mounted: function() {
        this.open();
        this.timmer = setTimeout(this.close, this.duration);
    }
}
</script>

<style scoped lang="css">
:root {
    display:none;
    opacity:0.0;
    filter:alpha(opacity=0);
}

.message {
    width: 100px;
    padding: 10px 20px;
    border-radius: 5px;
}

.message .error {
    background: red;
    border: solid 3px red;
}
</style>
