<template>
    <v-dialog v-model="active" max-width="600px" v-if="user !== null">
        <v-dialog v-model="confirm" max-width="300px">
            <v-card>
                <v-card-title>Eliminar usuario?</v-card-title>
                <v-card-actions>
                    <v-btn @click="deleteUser" color="success" text>Eliminar/Delete <v-icon>mdi-check</v-icon></v-btn>
                    <v-btn @click="confirm = false" color="error" text>Cancelar/Cancel <v-icon>mdi-cancel</v-icon></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="error.show" max-width="300px">
            <v-card>
                <v-card-title>{{error.message}}</v-card-title>
                <v-card-actions>
                    <v-btn @click="error.show = false" text color="success">Aceptar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-snackbar v-model="snackbar.active" :timeout="1500" :color="snackbar.color">{{ snackbar.message }}</v-snackbar>
        <v-card>
            <v-card-title>Usuario #{{ user.id }}</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="6">
                        <v-text-field v-model="user.email" label="Usuario/User" disabled></v-text-field>
                    </v-col>
                    <v-col cols="6">
                        <v-select v-model="user.role" label="Tipo de usuario/Type of user" :items="roles" item-text="name" return-object :disabled="!editing"></v-select>
                    </v-col>
                    <v-col cols="6">
                        <p>Fecha de registro: {{ new Date(user.created_at).toLocaleDateString('es-MX') }}</p>
                    </v-col>
                    <v-col cols="6">
                        <p>Ultima actualizacion: {{ new Date(user.updated_at).toLocaleDateString('es-MX') }}</p>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn v-if="!editing" color="warning" @click="beginEditing" text>Editar/Edit <v-icon>mdi-pencil</v-icon></v-btn>
                <v-btn v-if="editing" color="success" @click.prevent="saveUser" :loading="loading" text>Guardar/Save <v-icon>mdi-check</v-icon></v-btn>
                <v-btn v-if="editing" color="warning" @click="resetUser" text>Cancelar/Cancel <v-icon>mdi-reload</v-icon></v-btn>
                <v-btn color="error" @click.prevent="confirm = true" :loading="loadingDelete" text>Eliminar/Delete <v-icon>mdi-trash-can</v-icon></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapGetters } from "vuex";
import {getToken} from "../../lib/auth";

export default {
    name: "user",
    data: () => ({
        error: { show: false, message: null },
        confirm: false,
        active: false,
        editing: false,
        userBackup: null,
        loading: false,
        loadingDelete: false,
        snackbar: {
            active: false,
            color: 'success',
            message: ''
        }
    }),
    methods: {
        async saveUser() {
            this.loading = true
            const body = { ...this.user }
            try {
                const response = await axios.patch(`/api/users/${this.user.id}`, body, getToken())
                const user = { id: response.data.id, role: response.data.role.name, created_at: response.data.created_at, email: response.data.email }
                await this.$store.dispatch('users/replaceUser', { user })
                this.snackbar = {
                    active: true,
                    color: 'success',
                    message: 'Usuario actualizado'
                }
            } catch(exception) {
                this.snackbar = {
                    active: true,
                    color: 'error',
                    message: 'Error al actualizar el usuario'
                }
                this.loading = false
                return
            }
            this.loading = false
            this.editing = false
        },
        async deleteUser() {
            try {
                this.confirm = false
                this.loadingDelete = true
                const response = await axios.delete(`/api/users/${ this.user.id }`, getToken())
                this.$emit('userDeleted')
                await this.$store.dispatch('users/deleteUser', { user: { ...this.user } })
            } catch(exception) {
                this.snackbar = {
                    active: true,
                    color: 'error',
                    message: 'Error al eliminar el usuario'
                }
            }
            this.loadingDelete = false
        },
        beginEditing() {
            this.userBackup = { ...this.user }
            this.editing = true
        },
        async resetUser() {
            await this.$store.dispatch('users/setUser', { user: { ...this.userBackup }})
            this.loading = false
            this.editing = false
        },
        show() {
            this.active = true
        },
        hidde() {
            this.active = false
            this.editing = false
            this.loading = false
        }
    },
    computed: {
        ...mapGetters('users', ['user', 'roles'])
    }
}
</script>
