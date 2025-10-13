<template>
  <form class="bg-white p-4 shadow-sm rounded-3 mb-5" @submit.prevent="submitForm">
    <FileUpload @file-selected="file = $event" @preview="emit('preview', file)" />

    <GeneralData v-model="generalData" />
    <InvoiceData v-model="invoiceData" />
    <ShippingData v-model="shippingData" :invoiceData="invoiceData" />

    <div class="mb-3">
      <label>Uwagi sprzedawcy:</label>
      <textarea class="form-control" v-model="notes" rows="3"></textarea>
    </div>

    <div class="mb-4">
      <button type="submit" class="btn btn-primary">📤 Wyślij do Apilo</button>
    </div>
  </form>

  {{ temp }}

</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import FileUpload from './FileUpload.vue'
import GeneralData from './GeneralData.vue'
import InvoiceData from './InvoiceData.vue'
import ShippingData from './ShippingData.vue'

const emit = defineEmits(['preview'])

const file = ref(null)
const generalData = ref({
  client: '',
  phone: '',
  vat: 0,
  discount: 0,
  deliveryCost: '0.00',
  deliveryMethod: 'Eurohermes',
})
const invoiceData = ref({
  company: '',
  name: '',
  street: '',
  postcode: '',
  city: '',
  country: 'PL',
  taxNumber: ''
})
const shippingData = ref({
  company: '',
  name: '',
  street: '',
  postcode: '',
  city: '',
  country: 'PL'
})
const notes = ref('')

const temp = ref('')

const submitForm = async () => {
  try {
    const formData = new FormData()
    if (file.value) formData.append('file', file.value)
    formData.append('generalData', JSON.stringify(generalData.value))
    formData.append('invoiceData', JSON.stringify(invoiceData.value))
    formData.append('shippingData', JSON.stringify(shippingData.value))
    formData.append('notes', notes.value)

    const response = await axios.post(route('api.order.send'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    temp.value = response.data

  } catch (e) {
    console.error(e);
  }
}

</script>
