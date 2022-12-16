<template>
    <div class="app-container">
        <v-navigation-drawer permanent>
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title class="text-h6">{{get_user.email}}</v-list-item-title>
                    <v-list-item-subtitle>{{get_user.role.name}}</v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list dense nav>
                <v-list-item-subtitle v-if="verifyAccess([1])">Administración/Administration</v-list-item-subtitle>
                <v-list-item link @click.prevent="goToRoute('users.index')" v-if="verifyAccess([1])">
                    <v-list-item-icon>
                        <v-icon>mdi-account-multiple</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>Usuarios/Users</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-divider v-if="verifyAccess([1])"></v-divider>
                <v-list-item-subtitle>Base de datos/Data Base</v-list-item-subtitle>
                <v-list-item v-for="(menu, i) in menus" :key="i" link @click="goToRoute(menu.routeName)" v-if="verifyAccess(menu.requiredRoles)">
                    <v-list-item-icon>
                        <v-icon>{{ menu.icon }}</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>{{ menu.name }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item @click.prevent="closeSession" link>
                    <v-list-item-icon><v-icon>mdi-exit-to-app</v-icon></v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>Cerrar Sesión/Log out</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        <v-container fluid>
            <transition>
                <router-view></router-view>
            </transition>
        </v-container>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { validateToken, verifyAccess, getToken } from "../lib/auth";

export default {
    name: "app",
    data: () => ({
        menus: [
            { name: 'Agregar/Add', icon: 'mdi-plus', routeName: 'tools.create', requiredRoles: [1,2] },
            { name: 'Eliminar/Delete', icon: 'mdi-minus', routeName: 'tools.destroy', requiredRoles: [1,2] },
            { name: 'Editar/Edit', icon: 'mdi-pencil', routeName: 'tools.edit', requiredRoles: [1,2] },
            { name: 'Consultar/Look for', icon: 'mdi-magnify', routeName: 'search.index', requiredRoles: [] },
            // { name: 'Historial', icon: 'mdi-history', routeName: 'tools.history', requiredRoles: [1,2] }
        ]
    }),
    methods: {
        ...mapActions('user', ['set_user']),
        async closeSession() {
            const response = await axios.delete('/api/logout', getToken())
            if (response.status === 200) {
                window.location.href = '/login'
            }
        },
        verifyAccess(roles) {
            return verifyAccess(roles)
        },
        goToRoute(routeName) {
            this.$router.push({ name: routeName })
        }
    },
    computed: {
        ...mapGetters('user', ['get_user'])
    },
    async created() {
        try {
            const user = await validateToken()
            this.set_user({ user: user.data })
        } catch (exception) {
            window.location.href = '/login'
        }
    },
}
</script>

<style scoped>
    .app-container {
        display: grid;
        grid-template-columns: 0fr 1fr;
        height: 100vh;
    }
</style>
