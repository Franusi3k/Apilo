<template>
  <div>
    <h4 class="mt-4">Dane ogólne</h4>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Klient (login):</label>
        <input type="text" class="form-control" v-model="GeneralData.client" required />
      </div>
      <div class="col-md-6 mb-3">
        <label>Telefon:</label>
        <input type="text" class="form-control" v-model="GeneralData.phone" required />
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 mb-3">
        <label>Stawka VAT:</label>
        <input type="number" list="vat-options" class="form-control" v-model="GeneralData.vat" />
        <datalist id="vat-options">
          <option value="0"></option>
          <option value="5"></option>
          <option value="23"></option>
        </datalist>
      </div>
      <div class="col-md-3 mb-3">
        <label>Rabat [%]:</label>
        <input type="number" class="form-control" v-model="GeneralData.discount" min="0" max="100" />
      </div>
      <div class="col-md-3 mb-3">
        <label>Koszt dostawy [PLN]:</label>
        <input type="text" class="form-control" v-model="GeneralData.deliveryCost" />
      </div>
      <div class="col-md-3 mb-3">
        <label>Sposób dostawy:</label>
        <select class="form-select" v-model="GeneralData.deliveryMethod">
          <option value="Eurohermes">Eurohermes</option>
          <option value="RohligSuus">Rohlig Suus</option>
        </select>
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
