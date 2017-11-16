<template lang="html">
    <div :class="'message message-' + this.type">
        <span :class="this.type">{{ message }}</span>
    </div>
</template>

<script>
export default {
    data: function() {
        timmer: null
    },
    props: ['type', 'message'],
    methods: {
        open: function() {
            
        },
        close: function() {

        }
    },
    mounted: function() {

    }
}
</script>

<style lang="css">
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
