<template>
    <v-dialog v-model="active" max-width="500px" persistent>
        <v-snackbar v-model="snackbar.active" :timeout="1500" :color="snackbar.color">{{ snackbar.message }}</v-snackbar>
        <v-dialog v-model="error.show" max-width="300px">
            <v-card>
                <v-card-title>{{error.message}}</v-card-title>
            </v-card>
        </v-dialog>
        <template v-slot:activator="{on, attrs}">
            <v-btn v-on="on" v-bind="attrs" color="success" text>Registrar usuario/Add user</v-btn>
        </template>
        <v-card>
            <v-card-title>Crear usuario/Create user</v-card-title>
            <v-card-text>
                <v-form v-model="valid">
                    <v-row>
                        <v-col cols="4">
                            <v-text-field v-model="user.email" label="Usuario/User" clearable :rules="[rules.required, rules.email]"></v-text-field>
                        </v-col>
                        <v-col cols="4">
                            <v-select v-model="user.role" label="Tipo de usuario/Type of user" :items="roles" item-text="name" return-object clearable :rules="[rules.required]"></v-select>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field v-model="user.password" label="Contraseña/Password" type="password" clearable :rules="[rules.required]"></v-text-field>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-btn color="success" :disabled="!valid" @click.prevent="register" text>Registrar/To register</v-btn>
                <v-btn color="error" @click="active = false" text>Cancelar/Cancel</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { getToken } from "../../lib/auth";
import { email, required } from "../../lib/rules"
import { mapGetters } from "vuex";

export default {
    name: "create",
    data: () => ({
        error: { message: null, show: false },
        snackbar: { active: false, message: null },
        valid: false,
        active: false,
        user: {
            email: null,
            role: null,
            password: null
        },
        rules: {
            email,
            required
        }
    }),
    methods: {
        async register() {
            try {
                const response = await axios.post('/api/users', { ...this.user }, getToken())
                const user = { id: response.data.id, email: response.data.email, role: response.data.role.name, created_at: response.data.created_at }
                await this.$store.dispatch('users/insertUser', { user })
                this.active = false
                this.$emit('userCreated')
                this.user = {
                    email: null,
                    role: null,
                    password: null
                }
            } catch(error) {
                if (error.response.data.errors === undefined) {
                    this.snackbar = { active: true, message: 'Error al registrar usuario' }
                    return
                }
                const errors = error.response.data.errors
                if (errors.email !== undefined) {
                    this.error = { message: 'Este email ya esta en uso', show: true }
                }
            }
        }
    },
    computed: {
        ...mapGetters('users', ['roles'])
    }
}
</script>
