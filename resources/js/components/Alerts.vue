<template>
  <div>
    <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
    <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>
  </div>
</template>

<script setup>
defineProps({
  errorMessage: String,
  successMessage: String
})
</script>
