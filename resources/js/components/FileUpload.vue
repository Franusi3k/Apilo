<template>
  <div class="mb-3">
    <input type="file" class="form-control form-control-lg rounded-3 shadow-sm" @change="onFileChange" />
    <button type="button" class="btn btn-outline-info btn-sm mt-3 rounded-3" :disabled="!fileSelected"
      @click="$emit('preview')">
      Podgląd pliku
    </button>
  </div>
</template>


<script setup>
import { ref } from 'vue'

const emit = defineEmits(['file-selected', 'preview'])

const fileSelected = ref(false)

function onFileChange(event) {
  const file = event.target.files[0]
  fileSelected.value = !!file
  emit('file-selected', file)
}
</script>
