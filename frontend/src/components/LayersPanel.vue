<template>
  <div class="border-b">
    <div class="p-3 border-b">
      <h3 class="font-semibold text-sm">Layers</h3>
    </div>

    <div class="h-48 overflow-y-auto">
      <div class="p-2">
        <div v-if="elements.length === 0" class="text-center text-gray-400 text-sm py-4">
          No elements added yet
        </div>
        <div v-else class="space-y-1">
          <div
            v-for="element in elements"
            :key="element.id"
            :class="[
              'flex items-center gap-2 p-2 rounded text-sm cursor-pointer hover:bg-gray-100',
              { 'bg-blue-100': selectedElement === element.id }
            ]"
            @click="$emit('update:selectedElement', element.id)"
          >
            <div class="flex-1 truncate">{{ element.name }}</div>
            <button class="p-1 hover:bg-gray-200 rounded">
              <Eye class="h-3 w-3" />
            </button>
            <button class="p-1 hover:bg-gray-200 rounded">
              <Unlock class="h-3 w-3" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Eye, Unlock } from 'lucide-vue-next'

defineProps({
  elements: Array,
  selectedElement: String,
})

defineEmits(['update:selectedElement'])
</script>