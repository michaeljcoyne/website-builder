<template>
  <div class="flex-1">
    <div class="p-3 border-b">
      <h3 class="font-semibold text-sm">Properties</h3>
      <p v-if="element" class="text-xs text-gray-500">{{ element.name }}</p>
    </div>

    <div v-if="!element" class="p-4 text-center text-gray-400 text-sm">
      Select an element to edit its properties
    </div>

    <div v-else class="flex-1 overflow-y-auto">
      <div class="p-4 space-y-4">
        <!-- Position & Size -->
        <div class="space-y-3">
          <label class="text-xs font-medium text-gray-700">Position & Size</label>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="text-xs">X</label>
              <input
                type="number"
                :value="element.x"
                @input="updatePosition('x', parseInt($event.target.value) || 0)"
                class="w-full h-8 px-2 border rounded text-sm"
              />
            </div>
            <div>
              <label class="text-xs">Y</label>
              <input
                type="number"
                :value="element.y"
                @input="updatePosition('y', parseInt($event.target.value) || 0)"
                class="w-full h-8 px-2 border rounded text-sm"
              />
            </div>
            <div>
              <label class="text-xs">Width</label>
              <input
                type="number"
                :value="element.width"
                @input="updatePosition('width', parseInt($event.target.value) || 0)"
                class="w-full h-8 px-2 border rounded text-sm"
              />
            </div>
            <div>
              <label class="text-xs">Height</label>
              <input
                type="number"
                :value="element.height"
                @input="updatePosition('height', parseInt($event.target.value) || 0)"
                class="w-full h-8 px-2 border rounded text-sm"
              />
            </div>
          </div>
        </div>

        <!-- Text/Heading Properties -->
        <div v-if="element.type === 'text' || element.type === 'heading'" class="space-y-3">
          <label class="text-xs font-medium text-gray-700">Text</label>
          <div>
            <label class="text-xs">Content</label>
            <textarea
              :value="element.properties.content"
              @input="updateProperty('content', $event.target.value)"
              class="w-full h-20 px-2 py-1 border rounded text-sm resize-none"
            ></textarea>
          </div>
          <div>
            <label class="text-xs">Font Size</label>
            <div class="flex items-center gap-2">
              <input
                type="range"
                :value="element.properties.fontSize"
                @input="updateProperty('fontSize', parseInt($event.target.value))"
                min="8"
                max="72"
                class="flex-1"
              />
              <span class="text-xs w-8">{{ element.properties.fontSize }}px</span>
            </div>
          </div>
          <div>
            <label class="text-xs">Color</label>
            <input
              type="color"
              :value="element.properties.color"
              @input="updateProperty('color', $event.target.value)"
              class="w-full h-8 border rounded"
            />
          </div>
          <div v-if="element.type === 'heading'">
            <label class="text-xs">Heading Level</label>
            <select
              :value="element.properties.level"
              @change="updateProperty('level', parseInt($event.target.value))"
              class="w-full h-8 px-2 border rounded text-sm"
            >
              <option value="1">H1</option>
              <option value="2">H2</option>
              <option value="3">H3</option>
              <option value="4">H4</option>
              <option value="5">H5</option>
              <option value="6">H6</option>
            </select>
          </div>
        </div>

        <!-- Button Properties -->
        <div v-if="element.type === 'button'" class="space-y-3">
          <label class="text-xs font-medium text-gray-700">Button</label>
          <div>
            <label class="text-xs">Text</label>
            <input
              type="text"
              :value="element.properties.text"
              @input="updateProperty('text', $event.target.value)"
              class="w-full h-8 px-2 border rounded text-sm"
            />
          </div>
          <div>
            <label class="text-xs">Background Color</label>
            <input
              type="color"
              :value="element.properties.backgroundColor"
              @input="updateProperty('backgroundColor', $event.target.value)"
              class="w-full h-8 border rounded"
            />
          </div>
          <div>
            <label class="text-xs">Text Color</label>
            <input
              type="color"
              :value="element.properties.textColor"
              @input="updateProperty('textColor', $event.target.value)"
              class="w-full h-8 border rounded"
            />
          </div>
        </div>

        <!-- Image Properties -->
        <div v-if="element.type === 'image'" class="space-y-3">
          <label class="text-xs font-medium text-gray-700">Image</label>
          <div>
            <label class="text-xs">Image URL</label>
            <input
              type="text"
              :value="element.properties.src"
              @input="updateProperty('src', $event.target.value)"
              class="w-full h-8 px-2 border rounded text-sm"
            />
          </div>
          <div>
            <label class="text-xs">Alt Text</label>
            <input
              type="text"
              :value="element.properties.alt"
              @input="updateProperty('alt', $event.target.value)"
              class="w-full h-8 px-2 border rounded text-sm"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  selectedElement: String,
  elements: Array,
})

const emit = defineEmits(['update:elements'])

const element = computed(() => 
  props.elements.find(el => el.id === props.selectedElement)
)

const updateProperty = (property, value) => {
  if (!element.value) return

  const updatedElements = props.elements.map(el =>
    el.id === props.selectedElement
      ? { ...el, properties: { ...el.properties, [property]: value } }
      : el
  )
  emit('update:elements', updatedElements)
}

const updatePosition = (property, value) => {
  if (!element.value) return

  const updatedElements = props.elements.map(el =>
    el.id === props.selectedElement ? { ...el, [property]: value } : el
  )
  emit('update:elements', updatedElements)
}
</script>