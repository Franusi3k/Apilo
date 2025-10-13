<template>
  <div class="mb-3">
    <input type="file" class="form-control" @change="onFileChange" />
    <button type="button" class="btn btn-outline-info btn-sm mt-2" @click="$emit('preview')">
      👁 Podgląd pliku
    </button>
  </div>
</template>

<script setup>
const emit = defineEmits(['file-selected', 'preview'])

function onFileChange(event) {
  emit('file-selected', event.target.files[0])
}
</script>
