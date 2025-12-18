<template>
  <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header">
          <h5 class="modal-title" id="filePreviewLabel">📦 Podgląd produktów z pliku</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex justify-content-center mb-4">
            <div class="btn-group" role="group" aria-label="Data toggle">
              <button type="button" class="btn" :class="viewMode === 'order' ? 'btn-primary' : 'btn-outline-primary'"
                @click="viewMode = 'order'">
                🧾 Zamówienie
              </button>
              <button type="button" class="btn" :class="viewMode === 'client' ? 'btn-primary' : 'btn-outline-primary'"
                @click="viewMode = 'client'">
                👤 Klient
              </button>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
              <thead class="table-light">
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
                    <td>{{ row.sku }}</td>
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
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  previewData: { type: Array, default: () => [] }
})

const viewMode = ref('order')
</script>
