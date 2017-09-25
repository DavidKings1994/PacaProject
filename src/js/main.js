define(['vue', 'vuex', 'vue-router', 'bootstrap'], function(Vue, Vuex, VueRouter, Bootstrap) {
    Vue.use(VueRouter);
    // templates
    Vue.component('paca-navbar', require('./views/template/navbar.vue'));
    Vue.component('paca-header', require('./views/template/header.vue'));
    Vue.component('paca-login', require('./views/template/login.vue'));
    Vue.component('paca-logout', require('./views/template/logout.vue'));

    // admin components
    Vue.component('paca-admin-home', require('./views/admin/home.vue'));
    Vue.component('paca-admin-user-list', require('./views/admin/users/userList.vue'));
    Vue.component('paca-admin-user-currency', require('./views/admin/users/userCurrency.vue'));
    Vue.component('paca-admin-user-form', require('./views/admin/users/userForm.vue'));
    Vue.component('paca-admin-item-list', require('./views/admin/items/itemList.vue'));
    Vue.component('paca-admin-badge-list', require('./views/admin/badges/badgeList.vue'));
    Vue.component('paca-admin-character-list', require('./views/admin/characters/characterList.vue'));

    // user components
    Vue.component('paca-user-home', require('./views/user/home.vue'));

    const routes = [
        { path: '/home', component: require('./views/admin/home.vue') },
        { path: '/users', component: require('./views/admin/users/userList.vue') },
        { path: '/characters', component: require('./views/admin/characters/characterList.vue') },
        { path: '/items', component: require('./views/admin/items/itemList.vue') },
        { path: '/badges', component: require('./views/admin/badges/badgeList.vue') },
        // { path: '/bank', component: Bar },
    ];

    const router = new VueRouter({
        routes: routes
    });

    var navigation = require('./navigation.js');
    new Vue({
        el: '#App',
        router: router,
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
