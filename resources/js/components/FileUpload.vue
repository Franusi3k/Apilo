<template>
  <div class="mb-3">
    <input type="file" class="form-control" @change="onFileChange" />
    <button type="button" class="btn btn-outline-info btn-sm mt-2" @click="$emit('preview')">
      👁 Podgląd pliku
    </button>
  </div>
</template>

<script>
export default {
  methods: {
    onFileChange(event) {
      this.$emit('file-selected', event.target.files[0]);
    },
  },
};
</script>
