<!-- Enhanced Canvas.vue with Grid System and Snapping -->
<template>
    <div class="flex-1 relative bg-white">
        <!-- Grid Overlay -->
        <div
            v-if="showGrid"
            class="absolute inset-0 pointer-events-none z-10"
            :style="gridStyle"
        ></div>

        <!-- Canvas -->
        <div
            ref="canvasRef"
            :class="[
        'w-full h-full relative overflow-hidden',
        { 'bg-blue-50': dragOver, 'cursor-copy': isDragging }
      ]"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @drop="handleDrop"
            @click="$emit('update:selectedElement', null)"
            @contextmenu.prevent="handleRightClick"
        >
            <!-- Empty State -->
            <div v-if="elements.length === 0 && !isDragging"
                 class="absolute inset-0 flex items-center justify-center text-gray-400">
                <div class="text-center">
                    <Grid3X3 class="w-16 h-16 mx-auto mb-4 opacity-50"/>
                    <div class="text-lg mb-2">Start building your website</div>
                    <div class="text-sm">Drag components from the sidebar to get started</div>
                </div>
            </div>

            <!-- Drop Indicator -->
            <div v-if="isDragging"
                 class="absolute inset-0 flex items-center justify-center text-blue-500 bg-blue-50/50 border-2 border-dashed border-blue-300">
                <div class="text-center">
                    <div class="text-lg mb-2">Drop component here</div>
                    <div class="text-sm">Release to add {{ draggedItem?.name }} to your page</div>
                </div>
            </div>

            <!-- Debug Info -->
            <!--<div class="absolute top-4 right-4 bg-white p-2 rounded shadow text-xs max-w-xs">
                <div class="font-semibold mb-1">Canvas Debug:</div>
                <div>Total elements: {{ elements.length }}</div>
                <div>Visible elements: {{ visibleElements.length }}</div>
                <div v-if="selectedElement">Selected: {{ selectedElement }}</div>
                <div class="mt-2 space-y-1">
                    <div v-for="element in elements" :key="element.id" class="text-xs">
                        {{ element.name }}:
                        <span :class="element.properties?.hidden ? 'text-red-500' : 'text-green-500'">
        {{ element.properties?.hidden ? 'Hidden' : 'Visible' }}
      </span>
                        <span :class="element.properties?.locked ? 'text-red-500' : 'text-blue-500'">
        {{ element.properties?.locked ? ' Locked' : ' Unlocked' }}
      </span>
                    </div>
                </div>
            </div>-->

            <!-- Elements -->
            <div
                v-for="element in elements"
                :key="element.id"
                v-show="!element.properties?.hidden"
                :class="[
    'absolute border-2 transition-all duration-200',
    selectedElement === element.id ? 'border-blue-500 shadow-lg' : 'border-transparent hover:border-gray-300',
    element.properties?.locked ? 'cursor-not-allowed' : 'cursor-pointer'
  ]"
                :style="{
    left: element.x + 'px',
    top: element.y + 'px',
    width: element.width + 'px',
    height: element.height + 'px',
    zIndex: selectedElement === element.id ? 1000 : element.zIndex || 1,
    opacity: element.properties?.locked ? 0.7 : 1,
    backgroundColor: element.properties?.hidden ? 'rgba(255,0,0,0.1)' : 'transparent'
  }"
                @click.stop="handleElementClick(element)"
                @mousedown="handleElementMouseDown(element, $event)"
            >
                <!-- Element Content -->
                <div class="w-full h-full relative">
                    <!-- Text Element -->
                    <div
                        v-if="element.type === 'text'"
                        class="p-2 h-full flex items-center overflow-hidden"
                        :style="{
              fontSize: element.properties.fontSize + 'px',
              color: element.properties.color,
              fontWeight: element.properties.fontWeight || 'normal',
              textAlign: element.properties.textAlign || 'left'
            }"
                    >
                        {{ element.properties.content }}
                    </div>

                    <!-- Heading Element -->
                    <component
                        v-else-if="element.type === 'heading'"
                        :is="`h${element.properties.level}`"
                        class="p-2 h-full flex items-center font-bold overflow-hidden"
                        :style="{
              fontSize: element.properties.fontSize + 'px',
              color: element.properties.color,
              textAlign: element.properties.textAlign || 'left'
            }"
                    >
                        {{ element.properties.content }}
                    </component>

                    <!-- Button Element -->
                    <button
                        v-else-if="element.type === 'button'"
                        class="w-full h-full rounded px-4 py-2 transition-all duration-200 hover:opacity-90"
                        :style="{
              backgroundColor: element.properties.backgroundColor,
              color: element.properties.textColor,
              fontSize: element.properties.fontSize || '16px',
              border: element.properties.border || 'none',
              borderRadius: element.properties.borderRadius || '4px'
            }"
                        @click.stop
                    >
                        {{ element.properties.text }}
                    </button>

                    <!-- Image Element -->
                    <img
                        v-else-if="element.type === 'image'"
                        :src="element.properties.src || '/placeholder.svg'"
                        :alt="element.properties.alt"
                        class="w-full h-full object-cover rounded"
                        :style="{
              objectFit: element.properties.objectFit || 'cover',
              borderRadius: element.properties.borderRadius || '0px'
            }"
                    />

                    <!-- Container Element -->
                    <div
                        v-else-if="element.type === 'container'"
                        class="w-full h-full border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-500"
                        :style="{
              backgroundColor: element.properties.backgroundColor || 'transparent',
              padding: element.properties.padding || '20px'
            }"
                    >
                        <div class="text-center">
                            <Layout class="w-8 h-8 mx-auto mb-2"/>
                            <div class="text-sm">Container</div>
                        </div>
                    </div>

                    <!-- Default Element -->
                    <div v-else class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                        <div class="text-center text-gray-600">
                            <Square class="w-6 h-6 mx-auto mb-1"/>
                            <div class="text-xs">{{ element.name }}</div>
                        </div>
                    </div>
                </div>

                <!-- Selection Controls -->
                <div v-if="selectedElement === element.id" class="absolute inset-0 pointer-events-none">
                    <!-- Resize Handles -->
                    <div
                        class="absolute -top-1 -left-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-nw-resize pointer-events-auto"
                        @mousedown.stop="startResize(element, 'nw', $event)"></div>
                    <div
                        class="absolute -top-1 right-1/2 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-n-resize pointer-events-auto"
                        @mousedown.stop="startResize(element, 'n', $event)"></div>
                    <div
                        class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-ne-resize pointer-events-auto"
                        @mousedown.stop="startResize(element, 'ne', $event)"></div>
                    <div
                        class="absolute top-1/2 -right-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-e-resize pointer-events-auto"
                        @mousedown.stop="startResize(element, 'e', $event)"></div>
                    <div
                        class="absolute -bottom-1 -right-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-se-resize pointer-events-auto"
                        @mousedown.stop="startResize(element, 'se', $event)"></div>
                    <div
                        class="absolute -bottom-1 left-1/2 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-s-resize pointer-events-auto"
                        @mousedown.stop="startResize(element, 's', $event)"></div>
                    <div
                        class="absolute -bottom-1 -left-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-sw-resize pointer-events-auto"
                        @mousedown.stop="startResize(element, 'sw', $event)"></div>
                    <div
                        class="absolute top-1/2 -left-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-w-resize pointer-events-auto"
                        @mousedown.stop="startResize(element, 'w', $event)"></div>
                </div>

                <!-- Action Buttons for Selected Element -->
                <div v-if="selectedElement === element.id" class="absolute -top-10 left-0 flex gap-1 z-50">
                    <button
                        @click.stop="duplicateElement(element.id)"
                        class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600 transition-colors"
                        title="Duplicate"
                    >
                        <Copy class="h-3 w-3"/>
                    </button>
                    <button
                        @click.stop="handleDelete(element.id)"
                        class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600 transition-colors"
                        title="Delete"
                    >
                        <Trash2 class="h-3 w-3"/>
                    </button>
                </div>
            </div>

            <!-- Guidelines -->
            <div v-for="guideline in guidelines" :key="`${guideline.type}-${guideline.position}`"
                 :class="[
             'absolute bg-blue-400 pointer-events-none z-50',
             guideline.type === 'vertical' ? 'w-0.5 h-full' : 'h-0.5 w-full'
           ]"
                 :style="{
             left: guideline.type === 'vertical' ? guideline.position + 'px' : '0',
             top: guideline.type === 'horizontal' ? guideline.position + 'px' : '0'
           }"></div>
        </div>

        <!-- Context Menu -->
        <div
            v-if="contextMenu.show"
            class="absolute bg-white border shadow-lg rounded py-1 z-[9999]"
            :style="{ left: contextMenu.x + 'px', top: contextMenu.y + 'px' }"
        >
            <button @click="pasteElement" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                Paste
            </button>
            <button @click="toggleGrid" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                {{ showGrid ? 'Hide' : 'Show' }} Grid
            </button>
        </div>
    </div>
</template>

<script setup>
import {ref, inject, computed, onMounted, onUnmounted} from 'vue'
import {Trash2, Copy, Grid3X3, Layout, Square} from 'lucide-vue-next'

const props = defineProps({
    elements: Array,
    selectedElement: String,
})

const emit = defineEmits(['update:elements', 'update:selectedElement'])

const {draggedItem, isDragging} = inject('dragDrop')
const canvasRef = ref(null)
const dragOver = ref(false)
const showGrid = ref(true)
const gridSize = ref(20)
const guidelines = ref([])
const contextMenu = ref({show: false, x: 0, y: 0})
const copiedElement = ref(null)

// Dragging state
const dragging = ref({
    active: false,
    element: null,
    startX: 0,
    startY: 0,
    elementStartX: 0,
    elementStartY: 0
})

// Resizing state
const resizing = ref({
    active: false,
    element: null,
    handle: null,
    startX: 0,
    startY: 0,
    startWidth: 0,
    startHeight: 0,
    startLeft: 0,
    startTop: 0
})

// Grid style
const gridStyle = computed(() => ({
    backgroundImage: `
    linear-gradient(to right, #e5e7eb 1px, transparent 1px),
    linear-gradient(to bottom, #e5e7eb 1px, transparent 1px)
  `,
    backgroundSize: `${gridSize.value}px ${gridSize.value}px`,
    opacity: 0.5
}))

// Snap to grid function
const snapToGrid = (value) => {
    return Math.round(value / gridSize.value) * gridSize.value
}

// Methods
const selectElement = (id) => {
    emit('update:selectedElement', id)
}

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
        const rect = canvasRef.value.getBoundingClientRect()
        const x = snapToGrid(e.clientX - rect.left)
        const y = snapToGrid(e.clientY - rect.top)

        const newElement = {
            id: `${draggedItem.value.type}-${Date.now()}`,
            type: draggedItem.value.type,
            name: draggedItem.value.name,
            x,
            y,
            width: snapToGrid(200),
            height: snapToGrid(100),
            zIndex: props.elements.length + 1,
            properties: getDefaultProperties(draggedItem.value.type),
        }
        emit('update:elements', [...props.elements, newElement])
        emit('update:selectedElement', newElement.id)
    }
}

const getDefaultProperties = (type) => {
    switch (type) {
        case "text":
            return {content: "Sample text", fontSize: 16, color: "#000000", fontWeight: "normal", textAlign: "left"}
        case "heading":
            return {content: "Heading", fontSize: 24, color: "#000000", level: 1, textAlign: "left"}
        case "button":
            return {
                text: "Button",
                backgroundColor: "#3b82f6",
                textColor: "#ffffff",
                fontSize: "16",
                borderRadius: "4px"
            }
        case "image":
            return {src: "/placeholder.svg?height=200&width=200", alt: "Image", objectFit: "cover", borderRadius: "0px"}
        case "container":
            return {backgroundColor: "transparent", padding: "20px"}
        default:
            return {}
    }
}

const startDrag = (element, e) => {
    if (element.properties?.locked || e.target.closest('.resize-handle')) return

    dragging.value = {
        active: true,
        element: element,
        startX: e.clientX,
        startY: e.clientY,
        elementStartX: element.x,
        elementStartY: element.y
    }

    selectElement(element.id)
    document.addEventListener('mousemove', handleDragMove)
    document.addEventListener('mouseup', handleDragEnd)
}

const handleDragMove = (e) => {
    if (!dragging.value.active) return

    const deltaX = e.clientX - dragging.value.startX
    const deltaY = e.clientY - dragging.value.startY

    const newX = snapToGrid(dragging.value.elementStartX + deltaX)
    const newY = snapToGrid(dragging.value.elementStartY + deltaY)

    // Update element position
    const updatedElements = props.elements.map(el =>
        el.id === dragging.value.element.id
            ? {...el, x: Math.max(0, newX), y: Math.max(0, newY)}
            : el
    )
    emit('update:elements', updatedElements)

    // Show guidelines
    showGuidelines(newX, newY)
}

const handleDragEnd = () => {
    dragging.value.active = false
    guidelines.value = []
    document.removeEventListener('mousemove', handleDragMove)
    document.removeEventListener('mouseup', handleDragEnd)
}

const startResize = (element, handle, e) => {
    e.stopPropagation()

    if (element.properties?.locked) return

    resizing.value = {
        active: true,
        element: element,
        handle: handle,
        startX: e.clientX,
        startY: e.clientY,
        startWidth: element.width,
        startHeight: element.height,
        startLeft: element.x,
        startTop: element.y
    }

    document.addEventListener('mousemove', handleResizeMove)
    document.addEventListener('mouseup', handleResizeEnd)
}

const handleResizeMove = (e) => {
    if (!resizing.value.active) return

    const deltaX = e.clientX - resizing.value.startX
    const deltaY = e.clientY - resizing.value.startY
    const {handle, element, startWidth, startHeight, startLeft, startTop} = resizing.value

    let newWidth = startWidth
    let newHeight = startHeight
    let newX = startLeft
    let newY = startTop

    // Handle different resize directions
    if (handle.includes('e')) newWidth = snapToGrid(Math.max(50, startWidth + deltaX))
    if (handle.includes('w')) {
        newWidth = snapToGrid(Math.max(50, startWidth - deltaX))
        newX = snapToGrid(startLeft + deltaX)
    }
    if (handle.includes('s')) newHeight = snapToGrid(Math.max(30, startHeight + deltaY))
    if (handle.includes('n')) {
        newHeight = snapToGrid(Math.max(30, startHeight - deltaY))
        newY = snapToGrid(startTop + deltaY)
    }

    // Update element
    const updatedElements = props.elements.map(el =>
        el.id === element.id
            ? {...el, width: newWidth, height: newHeight, x: newX, y: newY}
            : el
    )
    emit('update:elements', updatedElements)
}

const handleResizeEnd = () => {
    resizing.value.active = false
    document.removeEventListener('mousemove', handleResizeMove)
    document.removeEventListener('mouseup', handleResizeEnd)
}

const showGuidelines = (x, y) => {
    const guides = []

    // Check alignment with other elements
    props.elements.forEach(el => {
        if (el.id === dragging.value.element.id) return

        // Vertical alignment
        if (Math.abs(el.x - x) < 5) guides.push({type: 'vertical', position: el.x})
        if (Math.abs(el.x + el.width - x) < 5) guides.push({type: 'vertical', position: el.x + el.width})

        // Horizontal alignment
        if (Math.abs(el.y - y) < 5) guides.push({type: 'horizontal', position: el.y})
        if (Math.abs(el.y + el.height - y) < 5) guides.push({type: 'horizontal', position: el.y + el.height})
    })

    guidelines.value = guides
}

const handleDelete = (elementId) => {
    const updatedElements = props.elements.filter(el => el.id !== elementId)
    emit('update:elements', updatedElements)
    emit('update:selectedElement', null)
}

const duplicateElement = (elementId) => {
    const element = props.elements.find(el => el.id === elementId)
    if (!element) return

    const newElement = {
        ...element,
        id: `${element.type}-${Date.now()}`,
        x: element.x + 20,
        y: element.y + 20,
        zIndex: props.elements.length + 1
    }

    emit('update:elements', [...props.elements, newElement])
    emit('update:selectedElement', newElement.id)
}

const handleRightClick = (e) => {
    contextMenu.value = {
        show: true,
        x: e.clientX,
        y: e.clientY
    }
}

const toggleGrid = () => {
    showGrid.value = !showGrid.value
    contextMenu.value.show = false
}

const pasteElement = () => {
    if (copiedElement.value) {
        const newElement = {
            ...copiedElement.value,
            id: `${copiedElement.value.type}-${Date.now()}`,
            x: contextMenu.value.x,
            y: contextMenu.value.y
        }
        emit('update:elements', [...props.elements, newElement])
    }
    contextMenu.value.show = false
}

// Keyboard shortcuts
const handleKeydown = (e) => {
    if (e.key === 'Delete' && props.selectedElement) {
        handleDelete(props.selectedElement)
    }
    if (e.key === 'Escape') {
        emit('update:selectedElement', null)
    }
    if (e.ctrlKey || e.metaKey) {
        if (e.key === 'c' && props.selectedElement) {
            copiedElement.value = props.elements.find(el => el.id === props.selectedElement)
            e.preventDefault()
        }
        if (e.key === 'v' && copiedElement.value) {
            pasteElement()
            e.preventDefault()
        }
        if (e.key === 'd' && props.selectedElement) {
            duplicateElement(props.selectedElement)
            e.preventDefault()
        }
    }
}

// Hide context menu on click
const hideContextMenu = () => {
    contextMenu.value.show = false
}

const visibleElements = computed(() =>
    props.elements.filter(el => !el.properties?.hidden)
)

const handleElementClick = (element) => {
    console.log('🖱️ Element clicked:', element.name, 'Hidden:', element.properties?.hidden, 'Locked:', element.properties?.locked)

    if (element.properties?.locked) {
        console.log('❌ Cannot select locked element')
        return
    }

    if (element.properties?.hidden) {
        console.log('❌ Cannot select hidden element')
        return
    }

    console.log('✅ Selecting element:', element.id)
    selectElement(element.id)
}

const handleElementMouseDown = (element, event) => {
    console.log('🖱️ Element mousedown:', element.name, 'Hidden:', element.properties?.hidden, 'Locked:', element.properties?.locked)

    if (element.properties?.locked) {
        console.log('❌ Cannot drag locked element')
        return
    }

    if (element.properties?.hidden) {
        console.log('❌ Cannot drag hidden element')
        return
    }

    startDrag(element, event)
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown)
    document.addEventListener('click', hideContextMenu)
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown)
    document.removeEventListener('click', hideContextMenu)
    document.removeEventListener('mousemove', handleDragMove)
    document.removeEventListener('mouseup', handleDragEnd)
    document.removeEventListener('mousemove', handleResizeMove)
    document.removeEventListener('mouseup', handleResizeEnd)
})


</script>
