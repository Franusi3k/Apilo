<template>
  <div class="modal fade" id="missingProductsModal" tabindex="-1" aria-labelledby="missingProductsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
        <div class="modal-header border-0 px-4 pt-4 pb-2">
          <div>
            <h5 class="modal-title fw-bold" id="missingProductsLabel">Nie znaleziono niektórych produktów</h5>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
        </div>

        <div class="modal-body px-4 pb-0">
          <p class="text-secondary mb-3">{{ missingMessage }}</p>

          <div class="border rounded-3 bg-light p-2 mb-3">
            <ul class="list-group list-group-flush">
              <li v-for="(prod, i) in missingProducts" :key="i"
                class="list-group-item bg-transparent d-flex align-items-center justify-content-between py-3">
                <span class="fw-bolder">SKU: {{ prod }}</span>
              </li>
            </ul>
          </div>

          <p class="mb-0 fw-semibold">Chcesz kontynuować zamówienie bez tych produktów?</p>
        </div>

        <div class="modal-footer border-0 px-4 pb-4 pt-3">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">
            Anuluj
          </button>
          <button type="button" class="btn btn-success rounded-3 px-4 shadow-sm" @click="emit('continue-with-found')"
            data-bs-dismiss="modal">
            Kontynuuj bez brakujących
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  missingMessage: { type: String, default: '' },
  missingProducts: { type: Array, default: () => [] }
})

const emit = defineEmits(['continue-with-found'])
</script>