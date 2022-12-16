import Vue from "vue";

export default {
    namespaced: true,
    state: {
        roles: [],
        users: [],
        user: null,
    },
    mutations: {
        setUsers(state, { users }) {
            state.users = users
        },
        setUser(state, { user }) {
            state.user = user
        },
        setRoles(state, { roles }) {
            state.roles = roles
        },
        replaceUser(state, { user }) {
            const oldUserIndex = state.users.findIndex(stream => stream.id === user.id)
            state.users.splice(oldUserIndex, 1, user)
        },
        deleteUser(state, { user }) {
            const userIndex = state.users.findIndex(stream => stream.id === user.id)
            state.users.splice(userIndex, 1)
        },
        insertUser(state, { user }) {
            state.users.push(user)
        }
    },
    actions: {
        setUsers({ commit }, { users }) {
            commit('setUsers', { users })
        },
        setUser({ commit }, { user }) {
            commit('setUser', { user })
        },
        setRoles({ commit }, { roles }) {
            commit('setRoles', { roles })
        },
        replaceUser( { commit }, { user }) {
            commit('replaceUser', { user })
        },
        deleteUser( { commit }, { user }) {
            commit('deleteUser', { user })
        },
        insertUser( { commit }, { user }) {
            commit('insertUser', { user })
        }
    },
    getters: {
        users: state => {
            return state.users
        },
        user: state => {
            return state.user
        },
        roles: state => {
            return state.roles
        }
    }
}
