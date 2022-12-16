export default {
    namespaced: true,
    state: {
        user: { email: '', role: {} }
    },
    mutations: {
      set_user(state, { user }) {
          state.user = user
      }
    },
    actions: {
        set_user({ commit }, { user }) {
            commit('set_user', { user })
        }
    },
    getters: {
        get_user: state => {
            return state.user
        }
    }
}
