define(['vue', 'vuex', 'bootstrap'], function(Vue, Vuex, Bootstrap) {
    // templates
    Vue.component('paca-navbar', require('./views/template/navbar.vue'));
    Vue.component('paca-header', require('./views/template/header.vue'));
    Vue.component('paca-login', require('./views/template/login.vue'));
    Vue.component('paca-logout', require('./views/template/logout.vue'));

    // admin components
    Vue.component('paca-admin-home', require('./views/admin/home.vue'));

    // user components
    Vue.component('paca-user-home', require('./views/user/home.vue'));

    var navigation = require('./navigation.js');
    new Vue({
        el: '#App',
        created: function() {
            navigation.commit('checkSession');
        },
        computed: {
            page: function() {
                var titulo  = navigation.state.page;
                $("title").text('Paca manager | ' + titulo.charAt(0).toUpperCase() + titulo.slice(1));
                return navigation.state.page;
            },
            action: function() {
                return store.state.navegacion.currentAction;
            },
            rol: function() {
                return navigation.state.session != null ? navigation.state.session.rol : '';
            },
            logged: function() {
                return navigation.state.session != null;
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
