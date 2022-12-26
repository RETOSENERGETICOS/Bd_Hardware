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
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.des" label="Descripcion" :items="dess" item-text="name" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.des" label="Descripcion" :items="dess" item-text="name" clearable item-value="name"></v-select>
                    </div>
                    <div class="form-row">
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.group" label="Sub Grupo" :items="groups" item-text="name" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.group" label="Sub Grupo" :items="groups" item-text="name" clearable item-value="name"></v-select>
                    </div>
                    <div class="form-row">
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.family" label="Familia" :items="families" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.family" label="Familia" :items="families" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-select>
                    </div>
                    <div class="form-row">
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.brand" label="Marca" :items="brands" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.brand" label="Marca" :items="brands" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-select>
                    </div>
                    <div class="form-row">
                        <v-combobox v-if="verifyAccess([1])" v-model.trim="tool.device" label="N.Dispositivo" :items="devices" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-combobox>
                        <v-select v-else v-model.trim="tool.device" label="N.Dispositivo" :items="devices" item-text="name" :rules="[rules.required]" clearable item-value="name"></v-select>
                    </div>
                </div>
                    <div class="form-row">
                        <v-text-field v-model="tool.serial" label="N de Serie"></v-text-field>
                    </div>
                    <div class="form-row">
                        <p>Sujeto a validacion</p>
                        <v-radio-group v-model="tool.has_validation" mandatory>
                            <v-radio label="Si" :value="true"></v-radio>
                            <v-radio label="No" :value="false"></v-radio>
                        </v-radio-group>
                    </div>
                    <div class="form-row">
                        <v-menu ref="datePickerMenu" v-model="menu" :close-on-content-click="false" offset-y min-width="auto">
                            <template v-slot:activator="{on, attrs}">
                                <v-text-field v-model="tool.calibration_expiration" label="Vencimiento de calibracion" v-on="on" v-bind="attrs" :disabled="!tool.has_validation"></v-text-field>
                            </template>
                            <v-date-picker v-model="tool.calibration_expiration" label="Vencimiento de calibracion" no-title></v-date-picker>
                        </v-menu>
                    </div>
                    <div class="form-row">
                        <v-text-field v-model="tool.main_localization" label="Localizacion principal" :rules="[rules.required]"></v-text-field>
                    </div>
                    <div class="form-row">
                        <v-text-field v-model="tool.shelf_localization" label="Localizacion de estante"></v-text-field>
                    </div>
                    <div class="form-row">
                        <v-text-field v-model="tool.shelf" label="# Estante"></v-text-field>
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-row">
                        <p>Despachable</p>
                        <v-radio-group mandatory>
                            <v-radio label="Si" :value="true"></v-radio>
                            <v-radio label="No" :value="false"></v-radio>
                        </v-radio-group>
                    </div>
                    <div class="form-row">
                        <v-text-field v-model.number="tool.quantity" label="Cantidad" :rules="[rules.required, v => v > 0 || 'Cantidad invalida']"></v-text-field>
                    </div>
                    <div class="form-row">
                        <v-text-field v-model="tool.measurement" label="Unidad de medida" :rules="[rules.required]"></v-text-field>
                    </div>
                    <div class="form-row">
                        <v-text-field v-model="tool.min_stock" label="Inventario minimo"></v-text-field>
                    </div>
                    <div class="form-row">
                        <v-textarea v-model="tool.comments" label="Comentarios"></v-textarea>
                    </div>
                    <div class="form-row">
                        <file-pond name="documents" ref="documents" label-idle="Archivos" accepted-file-types="application/pdf" @processfile="onProcessFile" :allow-multiple="true"></file-pond>
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
        groups: [],
        families: [],
        brands: [],
        devices: [],
        tool: {
            des: null,
            group: null,
            family: null,
            brand: null,
            device: null,
            serial: null,
            calibration_expiration: null,
            has_validation: false,
            main_localization: null,
            shelf_localization: null,
            shelf: null,
            measurement: null,
            min_stock: null,
            quantity: null,
            documents: [],
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
                group: null,
                family: null,
                brand: null,
                device: null,
                serial: null,
                calibrationExpiration: null,
                hasValidation: false,
                mainLocalization: null,
                shelfLocalization: null,
                shelf: null,
                measurement: null,
                minStock: null,
                quantity: null,
                documents: [],
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
        await axios.get('/api/groups', getToken()).then(response => this.groups =  response.data )
        await axios.get('/api/families', getToken()).then(response => this.families = response.data)
        await axios.get('/api/brands', getToken()).then(response => this.brands = response.data)
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
