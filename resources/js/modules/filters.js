export default {
    namespaced: true,
    state: {
        items: [],
        filters: {
            des: { text: 'Descripcion', value: 'des.name', active: true, key: 'des' },
            brand: { text: 'Marca', value: 'brand.name' ,active: true, key: 'brand' },
            usr: { text: 'Usuario', value: 'usr.name', active: true, key: 'usr' },
            so: { text: 'S Operativo', value: 'so.name', active: true, key: 'so' },
            device: { text: 'N.Dispositivo', value: 'device.name', active: true, key: 'device' },
            hasValidation: { text: 'Sujeto a validacion', value: 'has_validation', active: false, key: 'has_validation' },
            mainLocalization: { text: 'Localizacion principal', value: 'main_localization', active: false, key: 'main_localization' },
            shelfLocalization: { text: 'Localizacion de estante', value: 'shelf_localization', active: false, key: 'shelf_localization' },
            shelf: { text: 'Estante', value: 'shelf', active: false, key: 'shelf' },
            measurement: { text: 'Medida', value: 'measurement', active: false, key: 'measurement' },
            dispatchable: { text: 'Despachable', value: 'dispatchable', active: false, key: 'dispatchable' },
            minStock: { text: 'Inventario minimo', value: 'min_stock', active: false, key: 'min_stock' },
            quantity: { text: 'Cantidad', value: 'quantity', active: false, key: 'quantity' },
            serialNumber: { text: 'Numero de Serie', value: 'serial_number', active: false, key: 'serial_number' },
            calibrationExpiration: { text: 'Vencimiento de Calibracion', value: 'calibration_expiration', active: false, key: 'calibration_expiration' },
            item: { text: 'Item', value: 'item', active: false, key: 'item' },
            user: { text: 'Usuario', value: 'user.name', active: false, key: 'user_id' },
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
