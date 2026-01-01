<template>
  <div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
      <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
        <div class="modal-header border-0 px-4 pt-4 pb-2">
          <h5 class="modal-title fw-bold" id="lowStockLabel">Brak wystarczającej ilości w magazynie</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
        </div>

        <div class="modal-body px-4">
          <p class="text-secondary mb-3">Niektóre produkty mają za małą ilość na magazynie:</p>
          <div class="border rounded-3 bg-light p-2 mb-3" style="max-height: 820px; overflow: auto;">
            <ul class="list-group list-group-flush">
              <li v-for="(item, i) in lowStockList" :key="i"
                class="list-group-item bg-transparent d-flex align-items-center justify-content-between py-3">
                <div class="me-3">
                  <div class="fw-semibold">Produkt: {{ item.name }}</div>
                  <div class="text-secondary small">brak {{ item.missing }} szt.</div>
                </div>
              </li>
            </ul>
          </div>
          <p class="mb-0 fw-semibold">Wybierz sposób kontynuacji:</p>
        </div>

        <div class="modal-footer border-0 px-4 pb-4 pt-3 gap-2">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">
            Anuluj
          </button>
          <button type="button" class="btn btn-success rounded-3 px-4 shadow-sm" @click="$emit('continue-available')"
            data-bs-dismiss="modal">
            Tylko dostępne
          </button>
          <button type="button" class="btn btn-warning rounded-3 px-4 shadow-sm" @click="$emit('continue-all')"
            data-bs-dismiss="modal">
            Kontynuuj mimo braków
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
defineProps({
  lowStockList: { type: Array, default: () => [] }
})
defineEmits(['continue-available', 'continue-all'])
</script>
