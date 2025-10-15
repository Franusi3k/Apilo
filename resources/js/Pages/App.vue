<style>
h1 { color: teal; }
</style>

<template>
  <div class="container py-4">
    <h1 class="mb-4">🧾 Import zamówień do Apilo</h1>

    <Alerts />

    <OrderForm @preview="handlePreview"/>
    
    <Modals :preview-data="previewData" />
  </div>
</template>

<script setup>
import OrderForm from '../components/OrderForm.vue'
import Modals from '../components/Modals.vue'
import Alerts from '../components/Alerts.vue'
import { ref } from 'vue'
import axios from 'axios'
import { Modal } from 'bootstrap'

const previewData = ref([])

const handlePreview = async (file) => {
  try {
    const formData = new FormData()
    formData.append('excel_file', file)

    const { data } = await axios.post(route('preview.file'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    previewData.value = data 

    const modal = new Modal(document.getElementById('filePreviewModal'))
    modal.show()
  } catch (e) {
    console.error(e)
  }
}
</script>
