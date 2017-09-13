<template>
    <div class="lateral-navbar col-md-2">
        <ul>
            <li>
                <a href="#">opcion 1</a>
            </li>
            <li>
                <a href="#">opcion 2</a>
            </li>
            <li>
                <a href="#">opcion 3</a>
            </li>
            <li>
                <a href="#">opcion 4</a>
            </li>
        </ul>
    </div>
</template>

<script>
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

        }
    },
    mounted: function() {
        console.log(this.usuario);
    }
}
</script>

<style>

</style>
