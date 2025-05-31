<template>
  <div class="w-64 border-r bg-white flex flex-col">
    <div class="p-4 border-b">
      <h2 class="font-semibold text-sm">Components</h2>
    </div>

    <div class="flex-1 overflow-y-auto">
      <div class="p-2">
        <div v-for="category in componentCategories" :key="category.name" class="mb-4">
          <h3 class="text-xs font-medium text-gray-500 mb-2 px-2">{{ category.name }}</h3>
          <div class="space-y-1">
            <div
              v-for="component in category.components"
              :key="component.id"
              draggable="true"
              @dragstart="handleDragStart(component)"
              @dragend="handleDragEnd"
              class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-100 cursor-grab active:cursor-grabbing text-sm"
            >
              <component :is="component.icon" class="h-4 w-4 text-gray-600" />
              {{ component.name }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject } from 'vue'
import { Type, ImageIcon, Square, Layout, Grid3X3, Navigation, Video } from 'lucide-vue-next'

const { setDraggedItem, setIsDragging } = inject('dragDrop')

const componentCategories = [
  {
    name: "Basic",
    components: [
      { id: "text", name: "Text", icon: Type, type: "text" },
      { id: "heading", name: "Heading", icon: Type, type: "heading" },
      { id: "button", name: "Button", icon: Square, type: "button" },
      { id: "image", name: "Image", icon: ImageIcon, type: "image" },
      { id: "divider", name: "Divider", icon: Layout, type: "divider" },
    ],
  },
  {
    name: "Layout",
    components: [
      { id: "container", name: "Container", icon: Layout, type: "container" },
      { id: "grid", name: "Grid", icon: Grid3X3, type: "grid" },
      { id: "columns", name: "Columns", icon: Layout, type: "columns" },
    ],
  },
  {
    name: "Navigation",
    components: [
      { id: "navbar", name: "Navbar", icon: Navigation, type: "navbar" },
      { id: "breadcrumb", name: "Breadcrumb", icon: Navigation, type: "breadcrumb" },
    ],
  },
  {
    name: "Media",
    components: [
      { id: "video", name: "Video", icon: Video, type: "video" },
      { id: "gallery", name: "Gallery", icon: ImageIcon, type: "gallery" },
    ],
  },
]

const handleDragStart = (component) => {
  setDraggedItem(component)
  setIsDragging(true)
}

const handleDragEnd = () => {
  setDraggedItem(null)
  setIsDragging(false)
}
</script>