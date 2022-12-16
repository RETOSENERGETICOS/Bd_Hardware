<template>
    <v-dialog v-model="active" max-width="500px">
        <template #activator="{on, attrs}">
            <v-btn color="primary" text v-on="on" v-bind="attrs">Agregar campos/Add field <v-icon>mdi-tune</v-icon></v-btn>
        </template>
        <v-card>
            <v-card-title>Campos</v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-row>
                    <v-col v-for="(filter, i) in filters" :key="i">
                        <v-checkbox v-model="filter.active" :label="filter.text"></v-checkbox>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn color="success" text @click="saveFilters">Guardar/Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "activeFilters",
    data: () => ({
        active: false,
        filtersBackup: null,
    }),
    methods: {
      async saveFilters() {
          this.filtersBackup = this.filters
          await this.$store.dispatch('filters/setFilters', { filters: this.filtersBackup })
          this.active = false
      }
    },
    computed: {
        ...mapGetters('filters', ['filters'])
    }
}
</script>

<style scoped>

</style>
