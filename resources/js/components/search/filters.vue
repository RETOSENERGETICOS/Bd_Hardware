<template>
    <v-expansion-panels v-model="panel">
        <v-expansion-panel>
            <v-expansion-panel-header>Filtros/Filters</v-expansion-panel-header>
            <v-expansion-panel-content>
                <v-row>
                    <v-col>
                        <active-filters />
                    </v-col>
                    <v-col>
                        <v-btn color="success" text @click.prevent="search">Aplicar filtros/Apply field <v-icon>mdi-download</v-icon></v-btn>
                    </v-col>
                    <v-col>
                        <v-btn color="cyan" text @click.prevent="history">Consultar Historial/History <v-icon>mdi-history</v-icon></v-btn>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="4" v-if="filters.des.active"><v-select v-model="filter.des" label="Descripcion" :items="dess" item-text="name" return-object clearable></v-select></v-col>
                    <v-col cols="4" v-if="filters.brand.active"><v-select v-model="filter.brand" label="Marca" :items="brands" item-text="name" return-object clearable></v-select></v-col>
                    <v-col cols="4" v-if="filters.usr.active"><v-select v-model="filter.usr" label="Usuario" :items="usrs" item-text="name" return-object clearable></v-select></v-col>
                    <v-col cols="4" v-if="filters.so.active"><v-select v-model="filter.so" label="S Operativo" :items="sos" item-text="name" return-object clearable></v-select></v-col>
                    <v-col cols="4" v-if="filters.device.active"><v-select v-model="filter.device" label="N.Dispositivo" :items="devices" item-text="name" return-object clearable></v-select></v-col>

                    <v-col cols="4" v-if="filters.model.active"><v-text-field v-model="filter.model" label="Modelo" clearable></v-text-field></v-col>
                    <v-col cols="4" v-if="filters.processor.active"><v-text-field v-model="filter.processor" label="Procesador" clearable></v-text-field></v-col>
                    <v-col cols="4" v-if="filters.installation.active">
                        <v-menu ref="datePickerMenu" v-model="menu" :close-on-content-click="false" offset-y min-width="auto">
                            <template v-slot:activator="{on, attrs}">
                                <v-text-field v-model="filter.installation" label="F. Instalacion" v-on="on" v-bind="attrs"></v-text-field>
                            </template>
                            <v-date-picker v-model="filter.installation" label="F. Instalacion" no-title></v-date-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="4" v-if="filters.quantity.active"><v-text-field v-model.number="filter.quantity" label="Cantidad" clearable></v-text-field></v-col>
                    <v-col cols="4" v-if="filters.serialNumber.active"><v-text-field v-model="filter.serialNumber" label="Serie" clearable></v-text-field></v-col>
                    <v-col cols="4" v-if="filters.item.active"><v-text-field v-model="filter.item" label="Item" clearable></v-text-field></v-col>
                    <v-col cols="4" v-if="filters.user.active"><v-select v-model="filter.user" label="Usuario/User" :items="users" item-text="email" return-object clearable></v-select></v-col>
                </v-row>
            </v-expansion-panel-content>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script>
import { getToken } from "../../lib/auth";
import activeFilters from "./activeFilters";
import { mapGetters } from "vuex"

export default {
    name: "filters",
    data: () => ({
        panel: 0,
        des: [{id: 0, name: 'TODOS'}],
        brands: [{id: 0, name: 'TODOS'}],
        usrs: [{id: 0, name: 'TODOS'}],
        sos: [{id: 0, name: 'TODOS'}],
        devices: [{id: 0, name: 'TODOS'}],
        users: [{id: 0, email: 'TODOS'}],
        menu: false,
        filter: {
            des: null,
            brand: null,
            usr: null,
            so: null,
            device: null,
            model: null,
            processor: null,
            installation: null,
            quantity: 0,
            serialNumber: null,
            item: null,
            user: null,
            
        },
        historyHeaders: [
            {text: 'Item', value: 'tool.item'},
            {text: 'S Operativo', value: 'so.name'},
            {text: 'Fecha', value: 'created_at'},
            {text: 'Ejecutor', value: 'user.email'},
            {text: 'Actividad', value: 'comment'},
            {text: 'Informacion Actual', value: 'after'},
            {text: 'Informacion Anterior', value: 'before'}
        ]
    }),
    methods: {
        async history() {
            await this.$store.dispatch('filters/setHistoryMode', { value: true })
            this.$emit('loading', true)
            const response = await axios.get('/api/history', getToken())
            if (response.status === 200) {
                const items = response.data.map(item => {
                    return {
                        ...item,
                        before: JSON.parse(item.before),
                        after: JSON.parse(item.after)
                    }
                })
                await this.$store.dispatch('filters/setHistoryItems', { items })
            }
            this.$emit('loading', false)
        },
        async search() {
            this.$emit('loading', true)
            await this.$store.dispatch('filters/setHistoryMode', { value: false })
            const query = {}
            const activeFilters = Object.keys(this.filters).filter(filter => this.filters[filter].active)
            for (let key of activeFilters) {
                query[key] = this.filter[key]
            }
            const response = await axios.post('/api/search', query, getToken())
            if (response.status === 200) {
                await this.$store.dispatch('filters/setItems', { items: response.data })
            }
            this.$emit('loading', false)
        }
    },
    computed: {
        ...mapGetters('filters', ['filters','activeFilters'])
    },
    created() {
        axios.get('/api/dess', getToken())
            .then(response => {
                this.dess = this.dess.concat(response.data)
                this.filter.des = this.dess[0]
            })
        axios.get('/api/brands', getToken())
            .then(response => {
                this.brands = this.brands.concat(response.data)
                this.filter.brand = this.brands[0]
            })
        axios.get('/api/usrs', getToken())
            .then(response => {
                this.usrs = this.usrs.concat(response.data)
                this.filter.usr = this.usrs[0]
            })
        axios.get('/api/sos', getToken())
            .then(response => {
                this.sos = this.sos.concat(response.data)
                this.filter.so = this.sos[0]
            })
        axios.get('/api/devices', getToken())
            .then(response => {
                this.devices = this.devices.concat(response.data)
                this.filter.device = this.devices[0]
            })
        axios.get('/api/users', getToken())
          .then(response => {
              this.users = this.users.concat(response.data)
          })
    },
    components: {
        activeFilters
    }

}
</script>

<style scoped>

</style>
