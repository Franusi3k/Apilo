<template>
  <form class="bg-white p-4 p-md-5 rounded-4 shadow-sm border mb-5" @submit.prevent="submitForm()">
    <Loading v-model:active="loading" :is-full-page="true" background-color="rgba(0,0,0,0.4)" color="#ffffff"
      loader="spinner" :can-cancel="false" :z-index="9999" :lock-scroll="true" />

    <div class="mb-4 pb-2 border-bottom">
      <FileUpload @file-selected="file = $event" @preview="emit('preview', file)" />
    </div>

    <div class="mb-4">
      <GeneralData v-model="generalData" />
    </div>

    <div class="mb-4">
      <label class="form-label fw-semibold">Uwagi sprzedawcy:</label>
      <textarea class="form-control rounded-3 shadow-sm" v-model="notes" rows="3"></textarea>
    </div>

    <div class="d-flex justify-content-end pt-2">
      <button type="submit" class="btn btn-primary btn-lg rounded-3 px-4 shadow-sm">
        Wyślij do Apilo
      </button>
    </div>
  </form>
</template>



<script setup>
import { ref } from 'vue'
import FileUpload from './FileUpload.vue'
import GeneralData from './GeneralData.vue'
import Loading from 'vue3-loading-overlay'
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css'
import axios from 'axios'

const emit = defineEmits(['preview', 'success', 'error', 'missing-products', 'lowStockList'])

const file = ref(null)
const generalData = ref({
  client: '',
  phone: '',
  vat: 0,
  discount: 0,
  deliveryMethod: 'Eurohermes',
  taxNumber: ''
})
const notes = ref('')

const loading = ref(false)

const submitForm = async (ignoreMissingSku = false, confirmed_only = false, ignore_low_stock = false) => {
  if (loading.value) return
  loading.value = true
  try {
    emit('error', '')
    const formData = new FormData()
    if (file.value) formData.append('file', file.value)
    formData.append('generalData', JSON.stringify(generalData.value))
    formData.append('notes', notes.value)
    formData.append('ignore_missing_sku', ignoreMissingSku)
    formData.append('confirmed_only', confirmed_only)
    formData.append('ignore_low_stock', ignore_low_stock)

    const { data } = await axios.post(route('api.order.send'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    if (data.status === 'success') {
      emit('success', data.message)
    } else {
      emit('error', data.message)
    }

  } catch (e) {
    const res = e.response?.data

    if (res?.data?.notFound) {
      emit('missing-products', {
        missingProducts: res.data.notFound,
        message: res.message
      })
    } else if (res?.data?.missingProducts) {
      emit('lowStockList', res.data.missingProducts);
    } else {
      emit('error', res?.message || 'Wystąpił nieoczekiwany błąd.')
    }
  } finally {
    loading.value = false
  }
}

defineExpose({
  submitForm
})
</script>
