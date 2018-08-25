var Vue = require('vue');
var Vuex = require('vuex');

Vue.use(Vuex);
module.exports = new Vuex.Store({
    state: {
        session: null,
        guest: false
    },
    mutations: {
        updateSession: function(state, session) {
            state.session = session;
        },
        setGuestState: function(state, isGuest) {
            state.guest = isGuest;
        }
    },
    actions: {
        checkSession (context) {
            return new Promise((resolve, reject) => {
                $.post('/php/controllers/userController.php', {
                    action: 'checkSession'
                }, function(json) {
                    var session = $.parseJSON(json);
                    if (session.status == "logged") {
                        context.commit('updateSession', session.data);
                    }
                    resolve();
                });
            });
        },
        login (context, data) {
            return new Promise((resolve, reject) => {
                $.post('/php/controllers/userController.php', {
                    action: 'login',
                    name: data.name,
                    pass: data.pass
                }, function(json) {
                    var session = $.parseJSON(json);
                    if (session.status == "OK") {
                        context.commit('updateSession', session.data);
                    } else {
                        console.error(session);
                    }
                    resolve();
                });
            });
        },
        logout (context) {
            return new Promise((resolve, reject) => {
                $.post('/php/controllers/userController.php', {
                    action: 'logout'
                }, function(msg) {
                    context.commit('updateSession', null);
                    resolve();
                });
            });
        },
        allowGuest (context) {
            context.commit('setGuestState', true);
        },
        blockGuest (context) {
            context.commit('setGuestState', false);
        }
    }
});
