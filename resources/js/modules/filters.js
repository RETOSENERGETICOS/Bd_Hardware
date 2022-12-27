export default {
    namespaced: true,
    state: {
        items: [],
        filters: {
            des: { text: 'Descripcion/Description', value: 'des.name', active: true, key: 'des' },
            brand: { text: 'Marca/Brand', value: 'brand.name' ,active: true, key: 'brand' },
            usr: { text: 'Usuario/User', value: 'usr.name', active: true, key: 'usr' },
            so: { text: 'S Operativo/DOS', value: 'so.name', active: true, key: 'so' },
            device: { text: 'N Dispositivo/C Name', value: 'device.name', active: true, key: 'device' },
            model: { text: 'Modelo/Model', value: 'model', active: false, key: 'model' },
            processor: { text: 'Procesador/Proc Unit', value: 'processor', active: false, key: 'processor' },
            installation: { text: 'F Instalacion/Set Up D', value: 'installation', active: false, key: 'installation' },
            quantity: { text: 'Cantidad/QTY', value: 'quantity', active: false, key: 'quantity' },
            serialNumber: { text: 'N de Serie/Serial S', value: 'serial_number', active: false, key: 'serial_number' },
            item: { text: 'Item', value: 'item', active: false, key: 'item' },
        },
        historyMode: false,
        historyItems: [],
    },
    mutations: {
        setFilters(state, { filters }) {
            state.filters = filters
        },
        setItems(state, { items }) {
            state.items = items
        },
        setHistoryMode(state, { value }) {
            state.historyMode = value
        },
        setHistoryItems(state, { items }) {
            state.historyItems = items
        }
    },
    actions: {
        setHistoryItems({ commit }, { items }) {
            commit('setHistoryItems', { items })
        },
        setHistoryMode({ commit }, { value }) {
            commit('setHistoryMode', { value })
        },
        setFilters({ commit }, { filters }) {
            commit('setFilters', { filters })
        },
        setItems({ commit }, { items }) {
            commit('setItems', { items })
        }
    },
    getters: {
        historyItems: state => {
            return state.historyItems
        },
        historyMode: state => {
            return state.historyMode
        },
        activeFilters: state => {
            const activeFiltersKeys = Array.from(Object.keys(state.filters)).filter(key => state.filters[key].active)
            return activeFiltersKeys.map(key => state.filters[key])
        },
        filters: state => {
            return JSON.parse(JSON.stringify(state.filters))
        },
        items: state => {
            return state.items
        }
    }
}
