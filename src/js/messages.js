var Vue = require('vue');
var Vuex = require('vuex');

Vue.use(Vuex);
module.exports = new Vuex.Store({
    state: {
        messages: []
    },
    mutations: {
        addMessage: function(state, message) {
            let time = new Date();
            state.messages.push({
                message: message.text,
                type: message.type,
                id: time.getTime()
            });
        },
        removeMessage: function(state, id) {
            state.messages = $(state.messages).filter(function(i,m) {
                return m.id != id;
            });
        }
    }
});
