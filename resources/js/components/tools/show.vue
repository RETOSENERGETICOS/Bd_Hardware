<template>
    <div>
        <v-dialog v-model="active">
            <v-card>
                <v-card-title>¿Está usted seguro de guardar?/Confirm?</v-card-title>
                <v-card-actions>
                    <v-btn color="success" text @click.prevent="update">Guardar/Save</v-btn>
                    <v-btn color="error" text @click="active = false">Cancelar/Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="show" v-if="tool !== null" scrollable>
            <v-card>
                <v-card-title>Herramienta {{ tool.item }}</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-form v-model="valid">
                        <v-row>
                            <v-col cols="4">
                                <v-combobox label="Description" v-model="tool.des" item-text="name" :items="dess" clearable item-value="name"></v-combobox>
                            </v-col>
                            <v-col cols="4">
                                <v-combobox label="Marca" v-model="tool.brand" item-text="name" :items="brands" clearable item-value="name"></v-combobox>
                            </v-col>
                            <v-col cols="4">
                                <v-combobox label="S Operativo" v-model="tool.so" item-text="name" :items="sos" :rules="[rules.required]" clearable item-value="name" disabled></v-combobox>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="4">
                                <v-combobox label="Usuario" v-model="tool.usr" item-text="name" :items="usrs" item-value="name" disabled></v-combobox>
                            </v-col>
                            <v-col cols="4">
                                <v-combobox label="N.Dispositivo" v-model="tool.device" item-text="name" :items="devices" item-value="name" disabled></v-combobox>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field label="# Serie" v-model="tool.serial" disabled></v-text-field>
                            </v-col>
                        </v-row>
                            <v-col cols="4">
                                <v-text-field label="Localizacion principal" v-model="tool.main_localization" :rules="[rules.required]"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="4">
                                <v-text-field label="Localizacion de estante" v-model="tool.shelf_localization"></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field label="# de estante" v-model="tool.shelf"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="4">
                                <v-text-field label="Cantidad" v-model.number="tool.quantity" :rules="[rules.required, v => v > 0 || 'Cantidad invalida']"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="4">
                                <v-text-field label="Cantidad a mover" v-model.number="movingQuantity" type="number"></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-textarea label="Comentarios" v-model="tool.comments" :rows="1"></v-textarea>
                            </v-col>
                            <v-col cols="4">
                                <file-pond name="documents" ref="documents" label-idle="Archivos" accepted-file-types="application/pdf" :disabled="true"></file-pond>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn text color="success" @click="active = true">Guardar/Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import { getToken } from "../../lib/auth";
import { required } from "../../lib/rules";
import vueFilePond, { setOptions } from "vue-filepond";
import "filepond/dist/filepond.min.css"
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type"
const FilePond = vueFilePond(FilePondPluginFileValidateType);

export default {
    name: "show",
    data: () => ({
        active: false,
        loading: false,
        valid: false,
        show: false,
        tool: null,
        movingQuantity: 0,
        menu: false,
        rules : { required: required },
        dess: [],
        brands: [],
        sos: [],
        usrs: [],
        devices: [],
    }),
    methods: {
        async update() {
            this.active = false
            this.tool = { ...this.tool, movingQuantity: this.movingQuantity }
            const response = await axios.put(`/api/tools/${this.tool.id}`, this.tool, getToken())
            if (response.status === 200) {
                const newItem = {
                    id: this.tool.id,
                    item: this.tool.item,
                    des: this.tool.des,
                    brand: this.tool.brand,
                    so: this.tool.so,
                    usr: this.tool.device,
                    device: this.tool.model,
                    serial_number: this.tool.serial_number
                }
                this.$emit('updated', newItem)
                this.show = false
                this.movingQuantity = 0
            }

        },
        async open(tool) {
            const response = await axios.get(`/api/tools/${tool.id}`, getToken())
            this.tool = response.data
            this.show = true
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
    async mounted() {
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
