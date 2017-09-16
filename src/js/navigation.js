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
        login (state, data) {
            $.post('/php/controllers/userController.php', {
                action: 'login',
                name: data.name,
                pass: data.pass
            }, function(json) {
                var session = $.parseJSON(json);
                state.session = session;
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
