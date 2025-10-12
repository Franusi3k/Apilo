<template>
  <div>
    <h4 class="mt-4">Dane ogólne</h4>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Klient (login):</label>
        <input type="text" class="form-control" v-model="client" required />
      </div>
      <div class="col-md-6 mb-3">
        <label>Telefon:</label>
        <input type="text" class="form-control" v-model="phone" required />
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 mb-3">
        <label>Stawka VAT:</label>
        <input type="number" list="vat-options" class="form-control" v-model="vat" />
        <datalist id="vat-options">
          <option value="0"></option>
          <option value="5"></option>
          <option value="23"></option>
        </datalist>
      </div>
      <div class="col-md-3 mb-3">
        <label>Rabat [%]:</label>
        <input type="number" class="form-control" v-model="discount" min="0" max="100" />
      </div>
      <div class="col-md-3 mb-3">
        <label>Koszt dostawy [PLN]:</label>
        <input type="text" class="form-control" v-model="deliveryCost" />
      </div>
      <div class="col-md-3 mb-3">
        <label>Sposób dostawy:</label>
        <select class="form-select" v-model="deliveryMethod">
          <option value="Eurohermes">Eurohermes</option>
          <option value="RohligSuus">Rohlig Suus</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      client: '',
      phone: '',
      vat: 0,
      discount: 0,
      deliveryCost: '0.00',
      deliveryMethod: 'Eurohermes',
    };
  },
};
</script>
