<template>
    <div class="lateral-navbar col-md-2">
        <a v-on:click="select" href="#"><span class="glyphicon glyphicon-user"></span> opcion 1</a>
        <a v-on:click="select" href="#"><span class="glyphicon glyphicon-envelope"></span> opcion 2</a>
        <a v-on:click="select" href="#"><span class="glyphicon glyphicon-print"></span> opcion 3</a>
        <a v-on:click="select" href="#"><span class="glyphicon glyphicon-calendar"></span> opcion 4</a>
    </div>
</template>

<script>
var navigation = require('./../../navigation.js');
export default {
    data() {
        return {
            datos: null
        };
    },
    props:[
        'usuario'
    ],
    computed:{

    },
    methods: {
        cambiarPagina: function(pagina) {

        },
        inicio: function() {

        },
        select: function(event) {
            $('.lateral-navbar a').removeClass('selected');
            $(event.target).addClass('selected');
        }
    },
    mounted: function() {
        
    }
}
</script>

<style>

</style>
