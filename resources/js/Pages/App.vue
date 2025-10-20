<style>
h1 { color: teal; }
</style>

<template>
  <div class="container py-4">
    <h1 class="mb-4">🧾 Import zamówień do Apilo</h1>

    <Alerts :error-message="errorMessage" :success-message="successMessage" />
  
    <OrderForm @preview="handlePreview" @success="showSuccess" @error="showError" @missing-products="showMissingProductsModal" ref="orderForm"/>

    <Modals :preview-data="previewData" :missing-message="missingMessage" :missing-products="missingProducts" @continue-with-found="handleContinueWithoutMissing" />
  </div>
</template>

<script setup>
import OrderForm from '../components/OrderForm.vue'
import Modals from '../components/Modals.vue'
import Alerts from '../components/Alerts.vue'
import { ref } from 'vue'
import axios from 'axios'
import { Modal } from 'bootstrap'

const successMessage = ref('')
const errorMessage = ref('')

const showSuccess = (msg) => {
  successMessage.value = msg
  errorMessage.value = ''
}

const showError = (msg) => {
  errorMessage.value = msg
  successMessage.value = ''
}

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

const missingProducts = ref([])
const missingMessage = ref('')

const showMissingProductsModal = (data) => {
  missingProducts.value = data.missingProducts
  missingMessage.value = data.message

  const modal = new Modal(document.getElementById('missingProductsModal'))
  modal.show()
}

const orderForm = ref(null)

const handleContinueWithoutMissing = () => {
  orderForm.value.submitForm(true);
}
</script>
