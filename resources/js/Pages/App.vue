<style>
h1 { color: teal; }
</style>

<template>
  <div class="container py-4">
    <h1 class="mb-4">🧾 Import zamówień do Apilo</h1>

    <Alerts />

    <OrderForm />
    
    <Modals />
  </div>
</template>

<script setup>
import OrderForm from '../components/OrderForm.vue'
import Modals from '../components/Modals.vue'
import Alerts from '../components/Alerts.vue'
</script>
