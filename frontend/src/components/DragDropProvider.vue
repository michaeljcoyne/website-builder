<template>
  <div>
    <slot />
  </div>
</template>

<script setup>
import { ref, provide } from 'vue'

const draggedItem = ref(null)
const isDragging = ref(false)

const setDraggedItem = (item) => {
  draggedItem.value = item
}

const setIsDragging = (dragging) => {
  isDragging.value = dragging
}

provide('dragDrop', {
  draggedItem,
  setDraggedItem,
  isDragging,
  setIsDragging,
})
</script>