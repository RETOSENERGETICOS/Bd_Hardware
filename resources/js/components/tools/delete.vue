<template>
    <div>
        <v-snackbar v-model="snackbar.active" :timeout="1500" :color="snackbar.color">{{ snackbar.message }}</v-snackbar>
        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title>¿Está usted seguro de eliminar?/Confirm?</v-card-title>
                <v-card-actions>
                    <v-btn color="success" @click.prevent="deleteTools">Eliminar/Delete</v-btn>
                    <v-btn color="error">Cancelar/Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-data-table :headers="headers" :items="items" show-select v-model="selected" :loading="loading">
            <template v-slot:top>
                <v-toolbar flat>
                    <v-toolbar-title>Herramientas</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-spacer></v-spacer>
                    <v-btn color="error" :disabled="selected.length === 0" @click="dialog = true">Eliminar/Delete</v-btn>
                </v-toolbar>
            </template>
            <template #item.has_validation="{item}">{{ item.has_validation ? 'Si' : 'No' }}</template>
        </v-data-table>
    </div>
</template>

<script>
import {getToken} from "../../lib/auth";

export default {
    name: "delete",
    data: () => ({
        snackbar: {
            active: false,
            color: null,
            message: null
        },
        selected: [],
        items: [],
        dialog: false,
        loading: false,
        headers: [
            {text: 'ITEM', value: 'item'},
            {text: 'Descripcion', value: 'description'},
            {text: 'Dimension Principal', value: 'measurement'},
            {text: 'Subgrupo', value: 'group.name'},
            {text: 'Familia', value: 'family.name'},
            {text: 'Marca', value: 'brand.name'},
            {text: 'N.Dispositivo', value: 'device.name'},
            {text: '# Serie', value: 'serial_number'},
            {text: 'Vencimiento de calibracion', value: 'calibration_expiration'},
            {text: 'Sujeto a validacion', value: 'has_validation'},
            {text: '', value: 'edit'}
        ]
    }),
    methods: {
        async deleteTools() {
            this.loading = true
            try {
                for (let tool of this.selected) {
                    await axios.delete(`/api/tools/${tool.id}`, getToken())
                    const itemIndex = this.items.findIndex(stream => stream.id === tool.id)
                    this.items.splice(itemIndex, 1)
                }
                this.snackbar = {
                    active: true,
                    color: "success",
                    message: "Registros eliminados"
                }
            } catch (Exception) {
                this.snackbar = {
                    active: true,
                    color: "error",
                    message: "Error al eliminar registros"
                }
            }
            this.loading = false
        }
    },
    async created() {
        this.loading = true
        const response = await axios.get('/api/tools', getToken())
        if (response.status === 200) {
            this.items = response.data
            this.loading = false
        }
    },
}
</script>

<style scoped>

</style>
