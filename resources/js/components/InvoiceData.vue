<template>
  <div>
    <h4 class="mt-4">Dane do faktury</h4>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Firma:</label>
        <input type="text" class="form-control" v-model="invoiceData.company" required />
      </div>
      <div class="col-md-6 mb-3">
        <label>Imię i nazwisko:</label>
        <input type="text" class="form-control" v-model="invoiceData.name" required />
      </div>
      <div class="col-md-6 mb-3">
        <label>Ulica i numer:</label>
        <input type="text" class="form-control" v-model="invoiceData.street" required />
      </div>
      <div class="col-md-3 mb-3">
        <label>Kod pocztowy:</label>
        <input type="text" class="form-control" v-model="invoiceData.postcode" required />
      </div>
      <div class="col-md-3 mb-3">
        <label>Miasto:</label>
        <input type="text" class="form-control" v-model="invoiceData.city" required />
      </div>
      <div class="col-md-3 mb-3">
        <label>Kraj:</label>
        <input type="text" class="form-control" v-model="invoiceData.country" value="PL" required />
      </div>
      <div class="col-md-3 mb-3">
        <label>NIP:</label>
        <input type="text" class="form-control" v-model="invoiceData.taxNumber" required />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: Object,
      default: () => ({})
    }
  },
  emits: ['update:modelValue'],
  computed: {
    invoiceData: {
      get() { return this.modelValue; },
      set(value) { this.$emit('update:modelValue', value); }
    }
  }
};
</script>
