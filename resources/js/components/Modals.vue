<template>
  <!-- Podgląd pliku -->
  <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header">
          <h5 class="modal-title" id="filePreviewLabel">📦 Podgląd produktów z pliku</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
              <thead class="table-light">
                <tr>
                  <th>Nazwa</th>
                  <th>Ilość</th>
                  <th>Cena</th>
                  <th>SKU</th>
                  <th>Cena netto</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, index) in previewData" :key="index">
                  <td>{{ row.name }}</td>
                  <td>{{ row.quantity }}</td>
                  <td>{{ row.price }}</td>
                  <td>{{ row.sku }}</td>
                  <td>{{ row.netto }}</td>
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

  <!-- Niski stan magazynowy -->
  <div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content shadow">
        <div class="modal-header">
          <h5 class="modal-title" id="lowStockLabel">Brak wystarczającej ilości w magazynie</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
        </div>
        <div class="modal-body">
          <p>Niektóre produkty mają za małą ilość na magazynie:</p>
          <ul class="list-group mb-3">
            <li v-for="(item, i) in lowStockList" :key="i" class="list-group-item">
              Produkt: {{ item.name }} – brak {{ item.missing_quantity }} szt.
            </li>
          </ul>
          <p>Wybierz sposób kontynuacji:</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
          <button type="button" class="btn btn-success" @click="emit('continue-available')">✅ Tylko dostępne</button>
          <button type="button" class="btn btn-warning" @click="emit('continue-all')">⚠️ Kontynuuj mimo braków</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Brakujące produkty -->
  <div class="modal fade" id="missingProductsModal" tabindex="-1" aria-labelledby="missingProductsLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content shadow">
        <div class="modal-header">
          <h5 class="modal-title" id="missingProductsLabel">Nie znaleziono niektórych produktów</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
        </div>
        <div class="modal-body">
          <p>{{ missingMessage }}</p>
          <ul class="list-group mb-3">
            <li v-for="(prod, i) in missingProducts" :key="i" class="list-group-item fw-bolder">
              SKU: {{ prod.product.sku }}
            </li>
          </ul>
          <p>Chcesz kontynuować zamówienie bez tych produktów?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
          <button type="button" class="btn btn-success" @click="emit('continue-with-found')">✅ Kontynuuj bez brakujących</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  previewData: { type: Array, default: () => [] },
  lowStockList: { type: Array, default: () => [] },
  missingProducts: { type: Array, default: () => [] },
  missingMessage: { type: String, default: '' }
})

const emit = defineEmits(['continue-available', 'continue-all', 'continue-with-found'])
</script>
