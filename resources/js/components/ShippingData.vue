<template>
  <div>
    <h4 class="mt-4">Dane do wysyłki</h4>
    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" @click="copyInvoice">
      Skopiuj dane z faktury
    </button>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Firma:</label>
        <input type="text" class="form-control" v-model="shippingData.company" required />
      </div>
      <div class="col-md-6 mb-3">
        <label>Imię i nazwisko:</label>
        <input type="text" class="form-control" v-model="shippingData.name" required />
      </div>
      <div class="col-md-6 mb-3">
        <label>Ulica i numer:</label>
        <input type="text" class="form-control" v-model="shippingData.street" required />
      </div>
      <div class="col-md-3 mb-3">
        <label>Kod pocztowy:</label>
        <input type="text" class="form-control" v-model="shippingData.postcode" required />
      </div>
      <div class="col-md-3 mb-3">
        <label>Miasto:</label>
        <input type="text" class="form-control" v-model="shippingData.city" required />
      </div>
      <div class="col-md-3 mb-3">
        <label>Kraj:</label>
        <input type="text" class="form-control" v-model="shippingData.country" value="PL" required />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({})
  },
  invoiceData: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update:modelValue'])

const shippingData = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

function copyInvoice() {
  shippingData.value = {
    company: props.invoiceData.company || '',
    name: props.invoiceData.name || '',
    street: props.invoiceData.street || '',
    postcode: props.invoiceData.postcode || '',
    city: props.invoiceData.city || '',
    country: props.invoiceData.country || '',
  }
}
</script>
