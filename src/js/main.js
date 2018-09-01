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

    const routes = [
        {
            path: '/',
            component: require('./views/template/mainContainer.vue'),
            name: 'login'
        },
        {
            path: '/admin',
            component: require('./views/template/mainContainer.vue'),
            redirect: '/admin/home',
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
                    component: require('./views/admin/characters/characterList.vue'),
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
                    next();
                }
            } else {
                if (navigation.state.session == null) {
                    if (to.name == 'user profile'){
                        navigation.dispatch('allowGuest').then(() => {
                            next();
                        });
                    } else {
                        navigation.dispatch('blockGuest').then(() => {
                            next('/');
                        });
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

    //Make the dropdown part of the body instead of the table to move it easier
    $(document).ready(() => {
        window.button = null;
        $(document).on("shown.bs.dropdown", ".dropdown", function () {
            $(this).closest('td').attr('data-anchor', 'here');
            let $ul = $(this).children(".dropdown-menu");
            let $div = $(this).closest('table');
            let ulOffset = $ul.offset();
            let divOffset = $div.offset();
            let spaceDown = (ulOffset.top + $ul.height()) - (divOffset.top + $div.height());
            if (spaceDown > 0) {
                $(this).addClass("dropup");
            }
            $('#App').append($(this).css({
                position: 'absolute',
                left: $(this).offset().left,
                top: $(this).offset().top
            }).detach());
        }).on("hidden.bs.dropdown", ".dropdown", function() {
            $(this).removeClass("dropup");
            $("[data-anchor='here']").append($(this).css({
                position: 'initial',
                left: 0,
                top: 0
            }).detach());
            $(this).closest('td').removeAttr('data-anchor');
        });
    });
});
