define(['vue', 'vuex', 'vue-router', 'bootstrap', 'swal'],
function(Vue, Vuex, VueRouter, Bootstrap, swal) {
    Vue.use(VueRouter);

    // templates
    Vue.component('paca-main-container', require('./views/template/mainContainer.vue'));
    Vue.component('paca-navbar', require('./views/template/navbar.vue'));
    Vue.component('paca-header', require('./views/template/header.vue'));
    Vue.component('paca-login', require('./views/template/login.vue'));
    Vue.component('paca-logout', require('./views/template/logout.vue'));
    Vue.component('paca-dialog', require('./views/template/dialog.vue'));
    Vue.component('paca-inventory', require('./views/template/inventory.vue'));
    Vue.component('paca-inventory-use', require('./views/template/inventoryUse.vue'));
    Vue.component('paca-inventory-transaction', require('./views/template/inventoryTransaction.vue'));
    Vue.component('paca-character-card', require('./views/template/characterCard.vue'));
    Vue.component('messages', require('./views/template/messages.vue'));
    Vue.component('message', require('./views/template/message.vue'));

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
    Vue.component('paca-user-item-transaction', require('./views/user/items/itemTransaction.vue'));
    Vue.component('paca-user-character-form', require('./views/user/characters/characterForm.vue'));
    Vue.component('paca-user-character-transaction', require('./views/user/characters/characterTransaction.vue'));
    // Vue.component('paca-user-character-transfer-list', require('./views/user/characters/characterTransferList.vue'));

    const routes = [
        {
            path: '/',
            component: require('./views/template/mainContainer.vue'),
            name: 'login',
            children: [
                {
                    path: '/passwordRestore',
                    component: require('./views/template/passwordRestore.vue'),
                    name: 'password restore'
                },
                {
                    path: '/passwordRestore/:tempkey',
                    component: require('./views/template/passwordRestore.vue'),
                    name: 'password restore key',
                    props: true
                }
            ]
        },
        {
            path: '/admin',
            component: require('./views/template/mainContainer.vue'),
            children: [
                {
                    path: 'home',
                    component: require('./views/admin/home.vue'),
                    name: 'home',
                    alias: '/admin'
                },
                {
                    path: 'users',
                    component: require('./views/admin/users/userList.vue'),
                    name: 'users'
                },
                {
                    path: 'characters',
                    component: require('./views/template/characterGrid.vue'),
                    name: 'characters'
                },
                {
                    path: 'items',
                    component: require('./views/admin/items/itemList.vue'),
                    name: 'items'
                },
                {
                    path: 'badges',
                    component: require('./views/admin/badges/badgeList.vue'),
                    name: 'badges'
                },
                {
                    path: 'bank',
                    component: require('./views/admin/bank/ticketList.vue'),
                    name: 'bank'
                },
            ]
        },
        {
            path: '/user/:id',
            component: require('./views/template/mainContainer.vue'),
            props: true,
            children: [
                {
                    path: 'profile',
                    component: require('./views/user/profile.vue'),
                    name: 'user profile',
                    alias: '/user/:id',
                    props: true
                },
                {
                    path: 'inventory',
                    component: require('./views/user/items/inventory.vue'),
                    name: 'user inventory',
                    props: true
                },
                {
                    path: 'characters',
                    component: require('./views/user/characters/characterList.vue'),
                    name: 'user characters',
                    props: true
                },
                {
                    path: 'config',
                    component: require('./views/user/config.vue'),
                    name: 'user config',
                    props: true
                },
                {
                    path: 'requests',
                    component: require('./views/user/characters/characterTransferList.vue'),
                    name: 'transfer requests',
                    props: true
                }
            ]
        }
    ];

    const router = new VueRouter({
        routes: routes
    });

    var navigation = require('./navigation.js');
    navigation.dispatch('checkSession').then(() => {
        router.beforeEach((to, from, next) => {
            navigation.dispatch('checkSession').then(() => {
                if (to.path == "/") {
                    if (navigation.state.session != null) {
                        switch (navigation.state.session.rol) {
                            case 'admin': {
                                next('/admin');
                                break;
                            }
                            case 'user': {
                                next('/user/' + navigation.state.session.name);
                                break;
                            }
                        }
                    } else {
                        navigation.dispatch('blockGuest').then(() => {
                            next();
                        });
                    }
                } else {
                    if (navigation.state.session == null) {
                        switch (to.name) {
                            case 'password restore':
                            case 'password restore key':
                            case 'user profile': {
                                navigation.dispatch('allowGuest').then(() => {
                                    next();
                                });
                                break;
                            }
                            default: {
                                navigation.dispatch('blockGuest').then(() => {
                                    next('/');
                                });
                                break;
                            }
                        }
                    } else {
                        if ((to.path.includes('admin') && navigation.state.session.rol != 'admin') ||
                            (!to.path.includes('admin') && navigation.state.session.rol != 'user')) {
                            next(false);
                        } else {
                            if (navigation.state.session.rol == 'user') {
                                if (to.path.split('/')[2] == navigation.state.session.name) {
                                    next();
                                } else {
                                    next(false);
                                }
                            } else {
                                next();
                            }
                        }
                    }
                }
            });
        });

        window.eventBus = new Vue();

        new Vue({
            el: '#App',
            router: router,
            watch: {
                $route: function (to, from) {
                    if (to.path == "/") {
                        $("#mainContainer").addClass('fondo');
                    } else {
                        $("#mainContainer").removeClass('fondo');
                    }
                    $("title").text('Sirnus | ' + to.name.charAt(0).toUpperCase() + to.name.slice(1));
                }
            },
            computed: {
                rol: function() {
                    return navigation.state.session != null ? navigation.state.session.rol : '';
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
});
