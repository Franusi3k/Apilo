<template>
  <FilePreviewModal :previewData="previewData" />
  <LowStockModal
    :lowStockList="lowStockList"
    @continue-available="$emit('continue-available')"
    @continue-all="$emit('continue-all')"
  />
  <MissingProductsModal
    :missingProducts="missingProducts"
    :missingMessage="missingMessage"
    @continue-with-found="$emit('continue-with-found')"
  />
</template>

<script setup>
import { FilePreviewModal, LowStockModal, MissingProductsModal } from '@/components/modals'

defineProps({
  previewData: Array,
  lowStockList: Array,
  missingProducts: Array,
  missingMessage: String
})

defineEmits(['continue-available', 'continue-all', 'continue-with-found'])
</script>
