var Vue = require('vue');
var Vuex = require('vuex');

Vue.use(Vuex);
module.exports = new Vuex.Store({
    state: {
        page: 'home',
        action: ''
    },
    mutations: {
        navigate (state, page) {
            state.page = page;
        }
    }
});
