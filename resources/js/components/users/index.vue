<template>
    <div>
        <v-snackbar v-model="snackbar.active" :timeout="1500" :color="snackbar.color">{{ snackbar.message }}</v-snackbar>
        <user ref="userDialog" @userDeleted="userDeleted"/>
        <v-data-table :headers="headers" :items="users" :loading="loading" @click:row="viewUser">
            <template v-slot:top>
                <v-toolbar flat>
                    <v-toolbar-title>Usuarios/Users</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-spacer></v-spacer>
                    <create @userCreated="userCreated"/>
                </v-toolbar>
            </template>
            <template v-slot:item.created_at="{ item }">
                {{ new Date(item.created_at).toLocaleDateString('es-MX') }}
            </template>
        </v-data-table>
    </div>
</template>

<script>
import { getToken } from "../../lib/auth";
import { mapGetters } from "vuex";
import user from './user'
import create from "./create";

export default {
    name: "index",
    data: () => ({
        loading: true,
        roles: [],
        headers: [
            { text: 'Correo/Email', value: 'email' },
            { text: 'Tipo/Type', value: 'role' },
            { text: 'Fecha de registro/Recorded date', value: 'created_at' }
        ],
        snackbar: {
            active: false,
            color: '',
            message: null
        }
    }),
    methods: {
        async viewUser(user) {
            const response = await axios.get(`/api/users/${user.id}`, getToken())
            await this.$store.dispatch('users/setUser', { user: response.data })
            this.$refs.userDialog.show()
        },
        userDeleted() {
            this.$refs.userDialog.hidde()
            this.snackbar = {
                active: true,
                color: 'success',
                message: 'Usuario eliminado correctamente'
            }
        },
        userCreated() {
            this.snackbar = {
                active: true,
                color: 'success',
                message: 'Usuario registrado correctamente'
            }
        }
    },
    computed: {
        ...mapGetters('users', ['users'])
    },
    async created() {
        const response = await axios.get('/api/users', getToken())
        await this.$store.dispatch('users/setUsers', { users: response.data })
        const rolesResponse = await axios.get('/api/roles', getToken())
        await this.$store.dispatch('users/setRoles', { roles: rolesResponse.data })
        this.loading = false
    },
    components: {
        user,
        create
    }
}
</script>

<style scoped>

</style>
