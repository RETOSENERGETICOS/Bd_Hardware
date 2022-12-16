<template>
    <v-data-table :items="data" :headers="headers" disable-sort :loading="loading" :items-per-page="5">
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
</template>

<script>
import { getToken} from "../../lib/auth";

export default {
    name: "history",
    data: () => ({
        loading: false,
        headers: [
            {text: 'Item', value: 'tool.item'},
            {text: 'Familia', value: 'family.name'},
            {text: 'Fecha', value: 'created_at'},
            {text: 'Ejecutor', value: 'user.email'},
            {text: 'Actividad', value: 'comment'},
            {text: 'Informacion Actual', value: 'after'},
            {text: 'Informacion Anterior', value: 'before'},
        ],
        data: []
    }),
    methods: {
        getKey(object, index) {
            console.log(object)
            const keys = Object.keys(object)
            return keys[index]
        }
    },
    async mounted() {
        this.loading = true
        const response = await axios.get('/api/history', getToken())
        if (response.status === 200) {
            this.data = response.data.map(item => {
                return {
                    ...item,
                    before: JSON.parse(item.before),
                    after: JSON.parse(item.after)
                }
            })
        }
        this.loading = false
    }
}
</script>

<style scoped>

</style>
