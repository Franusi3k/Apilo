<template>
  <div>
    <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
    <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      errorMessage: '',
      successMessage: '',
    };
  },
};
</script>
