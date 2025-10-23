<template>
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
              SKU: {{ prod.sku }}
            </li>
          </ul>
          <p>Chcesz kontynuować zamówienie bez tych produktów?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
          <button type="button" class="btn btn-success" @click="emit('continue-with-found')" data-bs-dismiss="modal">✅ Kontynuuj bez brakujących</button>
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