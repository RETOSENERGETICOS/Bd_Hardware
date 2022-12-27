<template>
    <div>
        <v-dialog v-model="active">
            <v-card>
                <v-card-title>¿Está usted seguro de guardar?/Confirm?</v-card-title>
                <v-card-actions>
                    <v-btn color="success" text @click.prevent="createTool">Guardar/Save</v-btn>
                    <v-btn color="error" text @click="active = false">Cancelar/Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-snackbar v-model="snackbar.active" :color="snackbar.color" :timeout="1500" > {{ snackbar.message }}</v-snackbar>
        <v-btn @click="active = true" :disabled="disabled" :loading="loading">Guardar/Save</v-btn>
        <v-form v-model="valid">
            <div class="form-container">
                <div class="form-column">
                    <div class="form-row">
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.des" label="Descripcion/Description" :items="dess" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.des" label="Descripcion/Description" :items="dess" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-select>
                    </div>
                    <div class="form-row">
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.brand" label="Marca/Brand" :items="brands" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.brand" label="Marca/Brand" :items="brands" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-select>
                    </div>
                    <div class="form-row">
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.so" label="S Operativo/DOS" :items="sos" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.so" label="S Operativo/DOS" :items="sos" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-select>
                    </div>
                    <div class="form-row">
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.usr" label="Usuario/User" :items="usrs" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.usr" label="Usuario/User" :items="usrs" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-select>
                    </div>
                    <div class="form-row">
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.device" label="N Dispositivo/C Name" :items="devices" item-text="name" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.device" label="N Dispositivo/C Name" :items="devices" item-text="name" clearable item-value="name"></v-select>
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-row">
                        <v-text-field v-model="tool.serial" label="N de Serie/Serial S" :rules="[rules.required]"></v-text-field>
                    </div>
                    <div class="form-row">
                        <v-text-field v-model="tool.model" label="Modelo/Model" :rules="[rules.required]"></v-text-field>
                    </div>
                    <div class="form-row">
                        <v-text-field v-model="tool.processor" label="Procesador/Proc Unit"></v-text-field>
                    </div>
                    <div class="form-row">
                        <v-menu ref="datePickerMenu" v-model="menu" :close-on-content-click="false" offset-y min-width="auto">
                            <template v-slot:activator="{on, attrs}">
                                <v-text-field v-model="tool.installation" label="F Instalacion/Set Up D" v-on="on" v-bind="attrs"></v-text-field>
                            </template>
                            <v-date-picker v-model="tool.installation" label="F Instalacion/Set Up D" no-title></v-date-picker>
                        </v-menu>
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-row">
                        <v-text-field v-model.number="tool.quantity" label="Cantidad/QTY" :rules="[rules.required, v => v > 0 || 'Cantidad invalida']"></v-text-field>
                    </div>
                    <div class="form-row">
                        <v-textarea v-model="tool.comments" label="Comentarios"></v-textarea>
                    </div>
                </div>
            </div>
        </v-form>
    </div>
</template>

<script>
import { getToken, verifyAccess } from "../../lib/auth";
import { required } from "../../lib/rules";
import vueFilePond, { setOptions } from "vue-filepond";
import "filepond/dist/filepond.min.css"
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type"
const FilePond = vueFilePond(FilePondPluginFileValidateType);

export default {
    name: "create",
    data: () => ({
        snackbar: { active: false, message: null, color: 'success' },
        active: false,
        loading: true,
        menu: false,
        valid: false,
        rules : { required: required },
        dess: [],
        brands: [],
        sos: [],
        usrs: [],
        devices: [],
        tool: {
            des: null,
            brand: null,
            so: null,
            usr: null,
            device: null,
            serial: null,
            model: null,
            processor: null,
            installation: null,
            quantity: null,
            comments: null
        }
    }),
    methods: {
        verifyAccess(roles) {
            return verifyAccess(roles)
        },
        async onProcessFile(error, file) {
            if (error === null) {
                this.tool.documents.push(file.serverId)
            }
        },
        async createTool() {
            this.active = false
            this.loading = true
            const response = await axios.post('/api/tools', this.tool, getToken())
            if (response.status === 200) {
                this.snackbar = {
                    active: true,
                    message: 'Herramienta registrada',
                    color: 'success'
                }
                this.clearForm()
            } else {
                this.snackbar = {
                    active: true,
                    message: 'Error al registrar',
                    color: 'error'
                }
            }
            this.loading = false
        },
        clearForm() {
            this.tool = {
                des: null,
                brand: null,
                so: null,
                usr: null,
                device: null,
                serial: null,
                model: null,
                processor: null,
                installation: null,
                quantity: null,
                comments: null
            }
            this.$refs.documents.removeFiles()
        }
    },
    computed: {
        disabled() {
            if (this.tool.hasValidation && this.tool.calibrationExpiration === null) {
                return true
            }
            return !this.valid
        }
    },
    async created() {
        setOptions({
            server: {
                url: '/api/uploads',
                withCredentials: true,
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
            }
        })
        await axios.get('/api/dess', getToken()).then(response => this.dess =  response.data )
        await axios.get('/api/brands', getToken()).then(response => this.brands =  response.data )
        await axios.get('/api/sos', getToken()).then(response => this.sos = response.data)
        await axios.get('/api/usrs', getToken()).then(response => this.usrs = response.data)
        await axios.get('/api/devices', getToken()).then(response => this.devices = response.data)
        this.loading = false
    },
    components: {
        FilePond
    }

}
</script>

<style scoped>
.form-container {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
}
.form-row {
    margin: 1rem 0;
}
</style>
