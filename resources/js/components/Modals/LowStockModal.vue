<template>
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
              Produkt: {{ item.product.name }} – brak {{ item.missing_quantity }} szt.
            </li>
          </ul>
          <p>Wybierz sposób kontynuacji:</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
          <button type="button" class="btn btn-success" @click="$emit('continue-available')" data-bs-dismiss="modal">✅ Tylko dostępne</button>
          <button type="button" class="btn btn-warning" @click="$emit('continue-all')" data-bs-dismiss="modal">⚠️ Kontynuuj mimo braków</button>
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
