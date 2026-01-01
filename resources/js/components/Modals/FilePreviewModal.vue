<template>
  <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
        <div class="modal-header border-0 px-4 pt-4 pb-3">
          <h5 class="modal-title fw-bold" id="filePreviewLabel">Podgląd produktów z pliku</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
        </div>
        <div class="modal-body px-4 pt-2 pb-0">
          <div class="d-flex justify-content-center mb-3 sticky-top bg-white py-2 z-1">
            <div class="btn-group shadow-sm" role="group">
              <button type="button" class="btn px-4 py-2"
                :class="viewMode === 'order' ? 'btn-primary' : 'btn-outline-primary'" @click="viewMode = 'order'">
                Zamówienie
              </button>
              <button type="button" class="btn px-4 py-2"
                :class="viewMode === 'client' ? 'btn-primary' : 'btn-outline-primary'" @click="viewMode = 'client'">
                Klient
              </button>
            </div>
          </div>
          <div class="border rounded-4 bg-white shadow-sm overflow-hidden">
            <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top z-0">
                  <tr v-if="viewMode === 'order'">
                    <th>Nazwa</th>
                    <th>Ilość</th>
                    <th>Cena</th>
                    <th>SKU</th>
                    <th>Cena netto</th>
                    <th>Waluta</th>
                    <th>EAN</th>
                  </tr>
                  <tr v-else>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Firma</th>
                    <th>Ulica</th>
                    <th>Nr domu</th>
                    <th>Kod pocztowy</th>
                    <th>Miasto</th>
                    <th>Kraj</th>
                    <th>Telefon</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, index) in previewData" :key="index">
                    <template v-if="viewMode === 'order'">
                      <td>{{ row.name }}</td>
                      <td>{{ row.quantity }}</td>
                      <td>{{ row.price }}</td>
                      <td class="font-monospace">{{ row.sku }}</td>
                      <td>{{ row.netto }}</td>
                      <td>{{ row.currency }}</td>
                      <td>{{ row.ean }}</td>
                    </template>
                    <template v-else>
                      <td>{{ row.client.firstname }}</td>
                      <td>{{ row.client.lastname }}</td>
                      <td>{{ row.client.company }}</td>
                      <td>{{ row.client.street }}</td>
                      <td>{{ row.client.housenr }}</td>
                      <td>{{ row.client.zip }}</td>
                      <td>{{ row.client.city }}</td>
                      <td>{{ row.client.country }}</td>
                      <td>{{ row.client.phone }}</td>
                    </template>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 px-4 pb-4 pt-3">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">
            Zamknij
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  previewData: { type: Object, default: () => [] }
})

const viewMode = ref('order')
</script>
