<template>
    <div>
        <filters @loading="setLoading"/>
        <export-pdf ref="exportPdf" />
        <v-data-table v-if="!historyMode" :headers="activeFilters" :loading="loading" :items="filterableItems">
            <template #top>
                <v-toolbar flat>
                    <v-toolbar-title>Hardware</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="exportExcel" color="success" :disabled="filterableItems.length === 0"><v-icon>mdi-file-excel-box</v-icon></v-btn>
                    <v-btn icon @click="exportPdf" color="error" :disabled="filterableItems.length === 0"><v-icon>mdi-file-pdf-box</v-icon></v-btn>
                    <v-text-field v-model="search" label="Buscar/Search" hide-details></v-text-field>
                </v-toolbar>
            </template>
        </v-data-table>
        <v-data-table v-else-if="historyMode" :headers="historyHeaders" :items="historyItems" :items-per-page="5" :loading="loading">
            <template #item.created_at="{item}">{{ new Date(item.created_at).toLocaleDateString("es-MX") }}</template>
            <template #item.after="{item}">
            <span v-if="item.after !== null" v-for="(key, i) of Object.keys(item.after)" :key="i">
                {{ key }} -> {{ item.after[key] }} <br>
            </span>
            </template>
            <template #item.before="{item}">
            <span v-if="item.before !== null" v-for="(key, i) of Object.keys(item.after)" :key="i">
                {{ key }} -> {{ item.before[key] }} <br>
            </span>
            </template>
        </v-data-table>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import exportPdf from "./exportPdf";
import filters from "./filters";
const XLSX = require("xlsx");

export default {
    name: "index",
    data: () => ({
        loading: false,
        search: null,
        headers: [],
        specialKeys: ['des','brand','so','usr','device'],
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
        exportExcel() {
            let workbook = XLSX.utils.book_new()
            const data = []
            for (let item of this.filterableItems) {
                const newItem = {}
                for (let filter of this.activeFilters) {
                    if (this.specialKeys.find(item => item === filter.key) !== undefined) {
                        newItem[filter.text] = item[filter.key] === null ? '' : item[filter.key].name
                    } else {
                        newItem[filter.text] = item[filter.value]
                    }
                }
                data.push(newItem)
            }
            const worksheet = XLSX.utils.json_to_sheet(data)
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Hoja 1')
            XLSX.writeFileXLSX(workbook, 'Busqueda.xlsx')
        },
        exportPdf() {
          this.$refs.exportPdf.makePdf(this.activeFilters, this.filterableItems)
        },
        setLoading(data){
            this.loading = data
        }
    },
    computed: {
        ...mapGetters('filters', ['activeFilters','items','historyMode','historyItems']),
        filterableItems() {
            if (this.search === '' || this.search === null) {
                return this.items
            }
            const filteredItems = []
            this.items.forEach(item => {
                const objectString = JSON.stringify(Object.values(item)).toLowerCase()
                if (objectString.includes(this.search)) {
                    filteredItems.push(item)
                }
            })
            return filteredItems
        }
    },
    components: {
        filters,
        exportPdf
    }
}
</script>
