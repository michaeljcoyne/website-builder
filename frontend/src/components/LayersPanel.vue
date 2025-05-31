<template>
    <div class="border-b">
        <div class="p-3 border-b">
            <h3 class="font-semibold text-sm">Layers</h3>
            <div class="text-xs text-gray-500 mt-1">{{ elements.length }} element{{ elements.length !== 1 ? 's' : '' }}</div>
        </div>

        <div class="h-48 overflow-y-auto">
            <div class="p-2">
                <div v-if="elements.length === 0" class="text-center text-gray-400 text-sm py-4">
                    No elements added yet
                </div>
                <div v-else class="space-y-1">
                    <div
                        v-for="(element, index) in sortedElements"
                        :key="element.id"
                        :class="[
              'flex items-center gap-2 p-2 rounded text-sm cursor-pointer hover:bg-gray-100 transition-colors',
              {
                'bg-blue-100 border border-blue-200': selectedElement === element.id,
                'opacity-50': element.properties?.hidden
              }
            ]"
                        @click="selectElement(element.id)"
                        draggable="true"
                        @dragstart="startDrag(element, index, $event)"
                        @dragover="handleDragOver($event)"
                        @drop="handleDrop(index, $event)"
                    >
                        <!-- Drag Handle -->
                        <div class="flex-shrink-0 cursor-move text-gray-400 hover:text-gray-600">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"></path>
                            </svg>
                        </div>

                        <!-- Element Type Icon -->
                        <div class="flex-shrink-0">
                            <component :is="getElementIcon(element.type)" class="h-3 w-3 text-gray-500" />
                        </div>

                        <!-- Element Name -->
                        <div class="flex-1 truncate">
                            <span class="font-medium">{{ element.name }}</span>
                            <span class="text-xs text-gray-500 ml-1">({{ element.type }})</span>
                        </div>

                        <!-- Layer Controls -->
                        <div class="flex items-center gap-1">
                            <!-- Visibility Toggle -->
                            <button
                                @click.stop="toggleVisibility(element.id)"
                                :class="[
                  'p-1 hover:bg-gray-200 rounded transition-colors',
                  element.properties?.hidden ? 'text-red-500' : 'text-gray-600'
                ]"
                                :title="element.properties?.hidden ? 'Show element' : 'Hide element'"
                            >
                                <Eye v-if="!element.properties?.hidden" class="h-3 w-3" />
                                <EyeOff v-else class="h-3 w-3" />
                            </button>

                            <!-- Lock Toggle -->
                            <button
                                @click.stop="toggleLock(element.id)"
                                :class="[
                  'p-1 hover:bg-gray-200 rounded transition-colors',
                  element.properties?.locked ? 'text-red-500' : 'text-gray-600'
                ]"
                                :title="element.properties?.locked ? 'Unlock element' : 'Lock element'"
                            >
                                <Lock v-if="element.properties?.locked" class="h-3 w-3" />
                                <Unlock v-else class="h-3 w-3" />
                            </button>

                            <!-- Z-Index Display -->
                            <span class="text-xs text-gray-400 w-6 text-center">{{ element.zIndex || 1 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layer Actions -->
        <div class="p-2 border-t bg-gray-50">
            <div class="flex gap-1">
                <button
                    @click="moveSelectedLayer('up')"
                    :disabled="!selectedElement"
                    class="flex-1 px-2 py-1 text-xs border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Move layer up"
                >
                    ↑ Up
                </button>
                <button
                    @click="moveSelectedLayer('down')"
                    :disabled="!selectedElement"
                    class="flex-1 px-2 py-1 text-xs border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Move layer down"
                >
                    ↓ Down
                </button>
                <button
                    @click="duplicateSelected"
                    :disabled="!selectedElement"
                    class="flex-1 px-2 py-1 text-xs border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Duplicate layer"
                >
                    Copy
                </button>
                <button
                    @click="deleteSelected"
                    :disabled="!selectedElement"
                    class="flex-1 px-2 py-1 text-xs border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed text-red-600"
                    title="Delete layer"
                >
                    Del
                </button>
            </div>
        </div>

        <!-- Selection Controls -->
        <div class="p-2 border-t bg-gray-50" v-if="elements.length > 0">
            <div class="flex gap-1">
                <button
                    @click="selectAll"
                    class="flex-1 px-2 py-1 text-xs border rounded hover:bg-gray-100"
                    title="Select all visible elements"
                >
                    Select All
                </button>
                <button
                    @click="hideAll"
                    class="flex-1 px-2 py-1 text-xs border rounded hover:bg-gray-100"
                    title="Hide all elements"
                >
                    Hide All
                </button>
                <button
                    @click="showAll"
                    class="flex-1 px-2 py-1 text-xs border rounded hover:bg-gray-100"
                    title="Show all elements"
                >
                    Show All
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import {
    Eye, EyeOff, Lock, Unlock, Type, Square, ImageIcon,
    Layout, Grid3X3, Navigation, Video, Mail, Users,
    ShoppingCart, BarChart, List, Play, Camera, Star
} from 'lucide-vue-next'

const props = defineProps({
    elements: Array,
    selectedElement: String,
})

const emit = defineEmits(['update:selectedElement', 'update:elements'])

const draggedIndex = ref(null)

// Sort elements by z-index (highest first in the list)
const sortedElements = computed(() => {
    return [...props.elements].sort((a, b) => (b.zIndex || 1) - (a.zIndex || 1))
})

// Get appropriate icon for element type
const getElementIcon = (type) => {
    const iconMap = {
        'text': Type,
        'heading': Type,
        'button': Square,
        'image': ImageIcon,
        'container': Layout,
        'section': Layout,
        'card': Square,
        'hero': Layout,
        'grid': Grid3X3,
        'navbar': Navigation,
        'footer': Layout,
        'video': Video,
        'youtube': Play,
        'gallery': Camera,
        'input': Square,
        'textarea': Square,
        'select': List,
        'form': Mail,
        'newsletter': Mail,
        'testimonial': Users,
        'team-member': Users,
        'product-card': ShoppingCart,
        'price-table': BarChart,
        'social-links': Users,
        'counter': BarChart,
        'feature': Star,
        'accordion': List,
        'tabs': Layout,
    }
    return iconMap[type] || Square
}

const selectElement = (elementId) => {
    const element = props.elements.find(el => el.id === elementId)
    // Don't select hidden or locked elements
    if (element && !element.properties?.hidden && !element.properties?.locked) {
        emit('update:selectedElement', elementId)
    }
}

const toggleVisibility = (elementId) => {
    console.log('🔍 Visibility toggle for:', elementId)

    const updatedElements = props.elements.map(el => {
        if (el.id === elementId) {
            const newHidden = !el.properties?.hidden
            return {
                ...el,
                properties: {
                    ...el.properties,
                    hidden: newHidden
                }
            }
        }
        return el
    })

    // If hiding the selected element, deselect it
    if (props.selectedElement === elementId) {
        const element = props.elements.find(el => el.id === elementId)
        if (!element.properties?.hidden) { // Will become hidden
            emit('update:selectedElement', null)
        }
    }

    emit('update:elements', updatedElements)
}

const toggleLock = (elementId) => {
    console.log('🔒 Lock toggle for:', elementId)

    const updatedElements = props.elements.map(el => {
        if (el.id === elementId) {
            const newLocked = !el.properties?.locked
            return {
                ...el,
                properties: {
                    ...el.properties,
                    locked: newLocked
                }
            }
        }
        return el
    })

    // If locking the selected element, deselect it
    if (props.selectedElement === elementId) {
        const element = props.elements.find(el => el.id === elementId)
        if (!element.properties?.locked) { // Will become locked
            emit('update:selectedElement', null)
        }
    }

    emit('update:elements', updatedElements)
}

const moveSelectedLayer = (direction) => {
    if (!props.selectedElement) return

    const element = props.elements.find(el => el.id === props.selectedElement)
    if (!element) return

    const currentZ = element.zIndex || 1
    const newZ = direction === 'up' ? currentZ + 1 : Math.max(1, currentZ - 1)

    const updatedElements = props.elements.map(el =>
        el.id === props.selectedElement ? { ...el, zIndex: newZ } : el
    )
    emit('update:elements', updatedElements)
}

const duplicateSelected = () => {
    if (!props.selectedElement) return

    const element = props.elements.find(el => el.id === props.selectedElement)
    if (!element) return

    const newElement = {
        ...element,
        id: `${element.type}-${Date.now()}`,
        name: `${element.name} Copy`,
        x: element.x + 20,
        y: element.y + 20,
        zIndex: (element.zIndex || 1) + 1,
        properties: {
            ...element.properties,
            hidden: false,
            locked: false
        }
    }

    emit('update:elements', [...props.elements, newElement])
    emit('update:selectedElement', newElement.id)
}

const deleteSelected = () => {
    if (!props.selectedElement) return

    const updatedElements = props.elements.filter(el => el.id !== props.selectedElement)
    emit('update:elements', updatedElements)
    emit('update:selectedElement', null)
}

// Bulk actions
const selectAll = () => {
    // Select the first visible, unlocked element
    const visibleElement = props.elements.find(el => !el.properties?.hidden && !el.properties?.locked)
    if (visibleElement) {
        emit('update:selectedElement', visibleElement.id)
    }
}

const hideAll = () => {
    const updatedElements = props.elements.map(el => ({
        ...el,
        properties: { ...el.properties, hidden: true }
    }))
    emit('update:elements', updatedElements)
    emit('update:selectedElement', null)
}

const showAll = () => {
    const updatedElements = props.elements.map(el => ({
        ...el,
        properties: { ...el.properties, hidden: false }
    }))
    emit('update:elements', updatedElements)
}

// Drag and drop for reordering
const startDrag = (element, index, event) => {
    draggedIndex.value = index
    event.dataTransfer.effectAllowed = 'move'
    event.dataTransfer.setData('text/html', element.id)
}

const handleDragOver = (event) => {
    event.preventDefault()
    event.dataTransfer.dropEffect = 'move'
}

const handleDrop = (dropIndex, event) => {
    event.preventDefault()

    if (draggedIndex.value === null || draggedIndex.value === dropIndex) {
        draggedIndex.value = null
        return
    }

    const reorderedElements = [...props.elements]
    const draggedElement = sortedElements.value[draggedIndex.value]
    const targetElement = sortedElements.value[dropIndex]

    // Update z-indexes to reflect new order
    const updatedElements = reorderedElements.map(el => {
        if (el.id === draggedElement.id) {
            return { ...el, zIndex: targetElement.zIndex }
        } else if (el.id === targetElement.id) {
            return { ...el, zIndex: draggedElement.zIndex }
        }
        return el
    })

    emit('update:elements', updatedElements)
    draggedIndex.value = null
}
</script>
