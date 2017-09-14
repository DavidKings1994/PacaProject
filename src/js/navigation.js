Vue.use(Vuex);
const navigation = new Vuex.Store({
    state: {
        page: 'home',
        action: ''
    },
    mutations: {
        navigate (state, page) {
            state.page = page;
        }
    }
})
