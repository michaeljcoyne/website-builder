<template>
  <div class="flex-1 relative bg-white">
    <div
      :class="[
        'w-full h-full relative',
        { 'bg-blue-50': dragOver, 'cursor-copy': isDragging }
      ]"
      @dragover="handleDragOver"
      @dragleave="handleDragLeave"
      @drop="handleDrop"
      @click="$emit('update:selectedElement', null)"
    >
      <div v-if="elements.length === 0 && !isDragging" class="absolute inset-0 flex items-center justify-center text-gray-400">
        <div class="text-center">
          <div class="text-lg mb-2">Start building your website</div>
          <div class="text-sm">Drag components from the sidebar to get started</div>
        </div>
      </div>

      <div v-if="isDragging" class="absolute inset-0 flex items-center justify-center text-blue-500 bg-blue-50/50 border-2 border-dashed border-blue-300">
        <div class="text-center">
          <div class="text-lg mb-2">Drop component here</div>
          <div class="text-sm">Release to add {{ draggedItem?.name }} to your page</div>
        </div>
      </div>

      <div
        v-for="element in elements"
        :key="element.id"
        :class="[
          'absolute border-2 cursor-pointer',
          selectedElement === element.id ? 'border-blue-500' : 'border-transparent hover:border-gray-300'
        ]"
        :style="{
          left: element.x + 'px',
          top: element.y + 'px',
          width: element.width + 'px',
          height: element.height + 'px',
        }"
        @click.stop="$emit('update:selectedElement', element.id)"
      >
        <!-- Text Element -->
        <div
          v-if="element.type === 'text'"
          class="p-2 h-full flex items-center"
          :style="{
            fontSize: element.properties.fontSize + 'px',
            color: element.properties.color,
          }"
        >
          {{ element.properties.content }}
        </div>

        <!-- Heading Element -->
        <component
          v-else-if="element.type === 'heading'"
          :is="`h${element.properties.level}`"
          class="p-2 h-full flex items-center font-bold"
          :style="{
            fontSize: element.properties.fontSize + 'px',
            color: element.properties.color,
          }"
        >
          {{ element.properties.content }}
        </component>

        <!-- Button Element -->
        <button
          v-else-if="element.type === 'button'"
          class="w-full h-full rounded px-4 py-2"
          :style="{
            backgroundColor: element.properties.backgroundColor,
            color: element.properties.textColor,
          }"
        >
          {{ element.properties.text }}
        </button>

        <!-- Image Element -->
        <img
          v-else-if="element.type === 'image'"
          :src="element.properties.src || '/placeholder.svg'"
          :alt="element.properties.alt"
          class="w-full h-full object-cover rounded"
        />

        <!-- Default Element -->
        <div v-else class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
          {{ element.name }}
        </div>

        <!-- Delete Button for Selected Element -->
        <div v-if="selectedElement === element.id" class="absolute -top-8 left-0 flex gap-1">
          <button
            @click.stop="handleDelete(element.id)"
            class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600"
          >
            <Trash2 class="h-3 w-3" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { Trash2 } from 'lucide-vue-next'

const props = defineProps({
  elements: Array,
  selectedElement: String,
})

const emit = defineEmits(['update:elements', 'update:selectedElement'])

const { draggedItem, isDragging } = inject('dragDrop')
const dragOver = ref(false)

const handleDragOver = (e) => {
  e.preventDefault()
  dragOver.value = true
}

const handleDragLeave = () => {
  dragOver.value = false
}

const handleDrop = (e) => {
  e.preventDefault()
  dragOver.value = false

  if (draggedItem.value) {
    const rect = e.currentTarget.getBoundingClientRect()
    const newElement = {
      id: `${draggedItem.value.type}-${Date.now()}`,
      type: draggedItem.value.type,
      name: draggedItem.value.name,
      x: e.clientX - rect.left,
      y: e.clientY - rect.top,
      width: 200,
      height: 100,
      properties: getDefaultProperties(draggedItem.value.type),
    }
    emit('update:elements', [...props.elements, newElement])
  }
}

const getDefaultProperties = (type) => {
  switch (type) {
    case "text":
      return { content: "Sample text", fontSize: 16, color: "#000000" }
    case "heading":
      return { content: "Heading", fontSize: 24, color: "#000000", level: 1 }
    case "button":
      return { text: "Button", backgroundColor: "#3b82f6", textColor: "#ffffff" }
    case "image":
      return { src: "/placeholder.svg?height=200&width=200", alt: "Image" }
    default:
      return {}
  }
}

const handleDelete = (elementId) => {
  const updatedElements = props.elements.filter(el => el.id !== elementId)
  emit('update:elements', updatedElements)
  emit('update:selectedElement', null)
}
</script>