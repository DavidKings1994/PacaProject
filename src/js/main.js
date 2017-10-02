define(['vue', 'vuex', 'vue-router', 'bootstrap'],
function(Vue, Vuex, VueRouter, Bootstrap) {
    Vue.use(VueRouter);

    // templates
    Vue.component('paca-navbar', require('./views/template/navbar.vue'));
    Vue.component('paca-header', require('./views/template/header.vue'));
    Vue.component('paca-login', require('./views/template/login.vue'));
    Vue.component('paca-logout', require('./views/template/logout.vue'));
    Vue.component('paca-dialog', require('./views/template/dialog.vue'));
    Vue.component('paca-inventory', require('./views/template/inventory.vue'));
    Vue.component('paca-inventory-transaction', require('./views/template/inventoryTransaction.vue'));

    // admin components
    Vue.component('paca-admin-user-currency', require('./views/admin/users/userCurrency.vue'));
    Vue.component('paca-admin-currency-transaction', require('./views/admin/users/currencyTransaction.vue'));
    Vue.component('paca-admin-user-form', require('./views/admin/users/userForm.vue'));
    Vue.component('paca-admin-badge-form', require('./views/admin/badges/badgeForm.vue'));
    Vue.component('paca-admin-item-form', require('./views/admin/items/itemForm.vue'));
    Vue.component('paca-admin-character-form', require('./views/admin/characters/characterForm.vue'));
    Vue.component('paca-admin-ticket', require('./views/admin/bank/ticket.vue'));
    Vue.component('paca-admin-log', require('./views/admin/log/log.vue'));

    // user components
    Vue.component('paca-user-home', require('./views/user/home.vue'));

    const routes = [
        {
            path: '/',
            component: require('./views/admin/home.vue'),
            name: 'default'
        },
        {
            path: '/home',
            component: require('./views/admin/home.vue'),
            name: 'home'
        },
        {
            path: '/users',
            component: require('./views/admin/users/userList.vue'),
            name: 'users'
        },
        {
            path: '/characters',
            component: require('./views/admin/characters/characterList.vue'),
            name: 'characters'
        },
        {
            path: '/items',
            component: require('./views/admin/items/itemList.vue'),
            name: 'items'
        },
        {
            path: '/badges',
            component: require('./views/admin/badges/badgeList.vue'),
            name: 'badges'
        },
        {
            path: '/bank',
            component: require('./views/admin/bank/ticketList.vue'),
            name: 'bank'
        }
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
        watch: {
            $route: function (to, from) {
                $("title").text('Paca manager | ' + to.name.charAt(0).toUpperCase() + to.name.slice(1));
            }
        },
        computed: {
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
