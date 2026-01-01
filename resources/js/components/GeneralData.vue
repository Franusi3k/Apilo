<template>
  <div>
    <h4 class="mt-4 mb-3 fw-bold">Dane ogólne</h4>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Klient (login):</label>
        <input type="text" class="form-control form-control-lg rounded-3 shadow-sm" v-model="GeneralData.client"
          required />
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold">Telefon:</label>
        <input type="text" class="form-control form-control-lg rounded-3 shadow-sm" v-model="GeneralData.phone"
          required />
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-3">
        <label class="form-label fw-semibold">Stawka VAT:</label>
        <input type="number" list="vat-options" class="form-control form-control-lg rounded-3 shadow-sm"
          v-model="GeneralData.vat" />
        <datalist id="vat-options">
          <option value="0"></option>
          <option value="5"></option>
          <option value="23"></option>
        </datalist>
      </div>

      <div class="col-md-3">
        <label class="form-label fw-semibold">Rabat [%]:</label>
        <input type="number" class="form-control form-control-lg rounded-3 shadow-sm" v-model="GeneralData.discount"
          min="0" max="100" />
      </div>

      <div class="col-md-3">
        <label class="form-label fw-semibold">Sposób dostawy:</label>
        <select class="form-select form-select-lg rounded-3 shadow-sm" v-model="GeneralData.deliveryMethod">
          <option value="Eurohermes">Eurohermes</option>
          <option value="RohligSuus">Rohlig Suus</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label fw-semibold">NIP:</label>
        <input type="text" class="form-control form-control-lg rounded-3 shadow-sm" v-model="GeneralData.taxNumber"
          required />
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
  }
})

const emit = defineEmits(['update:modelValue'])

const GeneralData = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})
</script>
