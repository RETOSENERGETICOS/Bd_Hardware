<template>
    <VueHtml2pdf
      :show-layout="false"
      :float-layout="true"
      :enable-download="true"
      :paginate-elements-by-height="1400"
      filename="consulta"
      :pdf-quality="2"
      :manual-pagination="false"
      pdf-format="a4"
      pdf-orientation="landscape"
      pdf-content-width="800px"
      ref="html2Pdf"
    >
        <template #pdf-content>
            <v-data-table :headers="headers" :items="items" :items-per-page="-1" hide-default-footer>
                <template #top>
                    <v-toolbar flat>
                        <v-toolbar-title>Herramientas</v-toolbar-title>
                        <v-divider class="mx-4" inset vertical></v-divider>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                </template>
            </v-data-table>
        </template>
    </VueHtml2pdf>
</template>

<script>
import VueHtml2pdf from "vue-html2pdf"
export default {
    name: "exportPdf",
    data: () => ({
        headers: [],
        items: []
    }),
    methods: {
        makePdf(headers, items) {
            this.headers = headers
            this.items = items
            this.$nextTick(
              () => this.$refs.html2Pdf.generatePdf()
            )
        }
    },
    components: {
        VueHtml2pdf
    }
}
</script>

<style scoped>

</style>