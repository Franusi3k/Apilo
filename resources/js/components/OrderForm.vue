<template>
  <form class="bg-white p-4 shadow-sm rounded-3 mb-5" @submit.prevent="$emit('submit')">
    <FileUpload @file-selected="file = $event" @preview="$emit('preview', file)" />

    <GeneralData v-model="generalData" />
    <InvoiceData v-model="invoiceData" />
    <ShippingData v-model="shippingData" />

    <div class="mb-3">
      <label>Uwagi sprzedawcy:</label>
      <textarea class="form-control" v-model="notes" rows="3"></textarea>
    </div>

    <div class="mb-4">
      <button type="submit" class="btn btn-primary">📤 Wyślij do Apilo</button>
    </div>
  </form>
</template>

<script>
import FileUpload from './FileUpload.vue';
import GeneralData from './GeneralData.vue';
import InvoiceData from './InvoiceData.vue';
import ShippingData from './ShippingData.vue';

export default {
  components: { FileUpload, GeneralData, InvoiceData, ShippingData },
  data() {
    return {
      file: null,
      generalData: {},
      invoiceData: {},
      shippingData: {},
      notes: '',
    };
  },
};
</script>
