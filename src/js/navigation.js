var Vue = require('vue');
var Vuex = require('vuex');

Vue.use(Vuex);
module.exports = new Vuex.Store({
    state: {
        page: 'home',
        action: '',
        session: null
    },
    mutations: {
        navigate (state, page) {
            state.page = page;
        },
        checkSession (state) {
            $.post('/php/controllers/userController.php', {
                action: 'checkSession'
            }, function(json) {
                var session = $.parseJSON(json);
                if (session.status == "logged") {
                    state.session = session.data;
                }
            });
        },
        login (state, data) {
            $.post('/php/controllers/userController.php', {
                action: 'login',
                name: data.name,
                pass: data.pass
            }, function(json) {
                var session = $.parseJSON(json);
                if (session.status == "OK") {
                    state.session = session.data;
                }
            });
        },
        logout (state) {
            $.post('/php/controllers/userController.php', {
                action: 'logout'
            }, function(msg) {
                state.session = null;
            });
        }
    }
});
