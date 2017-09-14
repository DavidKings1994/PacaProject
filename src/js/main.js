define(['vue'], function(Vue) {
    Vue.component('paca-navbar', require('./views/template/navbar.vue'));
    Vue.component('paca-header', require('./views/template/header.vue'));

    new Vue({
        el: '#App',
        data: {
            
        },
        props: [ 'session' ],
        methods: { },
        computed: {
            actualPage: function() {
                var titulo  = store.state.navegacion.currentPage;
                $("title").text('CRM - ' + titulo.charAt(0).toUpperCase() + titulo.slice(1));
                return store.state.navegacion.currentPage;
            },
            accion: function() {
                return store.state.navegacion.currentAction;
            }
        },
        filters: {
            capitalize: function (value) {
                if (!value) return '';
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            }
        }
    });
});
