define(['vue', 'vuex'], function(Vue, Vuex) {
    // templates
    Vue.component('paca-navbar', require('./views/template/navbar.vue'));
    Vue.component('paca-header', require('./views/template/header.vue'));

    // admin components
    Vue.component('paca-admin-home', require('./views/admin/home.vue'));

    // user components
    Vue.component('paca-user-home', require('./views/user/home.vue'));

    new Vue({
        el: '#App',
        data: {

        },
        methods: { },
        computed: {
            actualPage: function() {
                var titulo  = store.state.navegacion.currentPage;
                $("title").text('Paca manager | ' + titulo.charAt(0).toUpperCase() + titulo.slice(1));
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
