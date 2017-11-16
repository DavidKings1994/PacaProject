var Vue = require('vue');
var Vuex = require('vuex');

Vue.use(Vuex);
module.exports = new Vuex.Store({
    state: {
        messages: []
    },
    mutations: {
        addMessage: function(state, message, type) {
            state.messages.push({
                message: message,
                type: type,
                id: (state.messages.length > 0 ? (state.messages[state.messages.length - 1].id + 1) : 1)
            });
        }
    }
});
