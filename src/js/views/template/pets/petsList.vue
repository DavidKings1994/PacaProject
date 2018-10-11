<template id="">
    <div class="container-fluid">
        <div class="row text-right" id="upperBar"></div>
        <div class="well">
            <div class="" v-for="pet in pets">

            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
var messageStore = require('./../../../messages.js');
var navigation = require('./../../../navigation.js');
export default {
    data: function() {
        return {
            pets: []
        };
    },
    methods: {
        loadPets: function() {
            $.post('./php/controllers/petsController.php', {
                action: 'getPets'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.pets = json.pets;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load logs',
                        type: 'error'
                    });
                }
            });
        }
    },
    mounted: function() {
        this.loadPets()
    }
};
</script>

<style media="screen">

</style>
