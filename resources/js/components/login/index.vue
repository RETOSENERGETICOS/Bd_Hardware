<template>
    <div class="container">
        <v-snackbar v-model="snackbar" color="error" timeout="1500">Correo o contrase;a incorrectos</v-snackbar>
        <v-card>
            <v-card-title class="text--center">Iniciar sesión/Log in</v-card-title>
            <v-card-text>
                <v-form>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="email" label="Correo/Email" prepend-inner-icon="mdi-account"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="password" label="Contraseña/Password" type="password" prepend-inner-icon="mdi-lock"></v-text-field>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-btn :loading="loading" @click.prevent="login" :disabled="loading">Enviar/Send</v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import { setToken } from "../../lib/auth";

export default {
    name: "index",
    data: () => ({
        email: null,
        password: null,
        loading: false,
        snackbar: false
    }),
    methods: {
        async login() {
            this.loading = true
            const data = {
                email: this.email,
                password: this.password
            }
            try {
                const response = await axios.post('/api/login', data);
                setToken(response.data.token)
                await this.$store.dispatch('user/set_user', { user: response.data.user })
                window.location.href = '/app'
            } catch (exception) {
                this.snackbar = true
            }
            this.loading = false
        }
    }
}
</script>

<style scoped>
    .container {
        width: 100%;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>