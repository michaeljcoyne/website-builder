<!-- Enhanced Canvas.vue with Grid System, Snapping, and Responsive Design -->
<template>
    <div class="flex-1 relative bg-gray-100">
        <!-- Canvas Container with Responsive Frame -->
        <div class="w-full h-full flex items-center justify-center p-4 overflow-auto">
            <div
                class="bg-white shadow-lg transition-all duration-300 relative"
                :style="canvasStyle"
            >
                <!-- Grid Overlay -->
                <div
                    v-if="showGrid"
                    class="absolute inset-0 pointer-events-none z-10"
                    :style="gridStyle"
                ></div>

                <!-- Canvas Content -->
                <div
                    ref="canvasRef"
                    class="relative overflow-hidden"
                    :style="canvasContentStyle"
                    :class="{ 'bg-blue-50': dragOver, 'cursor-copy': isDragging }"
                    @dragover="handleDragOver"
                    @dragleave="handleDragLeave"
                    @drop="handleDrop"
                    @click="$emit('update:selectedElement', null)"
                    @contextmenu.prevent="handleRightClick"
                >
                    <!-- Breakpoint Indicator -->
                    <div class="absolute top-2 left-2 z-50 bg-white rounded shadow-sm border px-2 py-1">
                        <div class="flex items-center gap-2 text-xs">
                            <component :is="getCurrentBreakpoint()?.icon" class="h-3 w-3 text-blue-600" />
                            <span class="font-medium">{{ getCurrentBreakpoint()?.name }}</span>
                            <span class="text-gray-500">{{ getCurrentBreakpoint()?.width }}px</span>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="visibleElements.length === 0 && !isDragging" class="absolute inset-0 flex items-center justify-center text-gray-400">
                        <div class="text-center">
                            <Grid3X3 class="w-16 h-16 mx-auto mb-4 opacity-50" />
                            <div class="text-lg mb-2">Start building your website</div>
                            <div class="text-sm">Drag components from the sidebar to get started</div>
                        </div>
                    </div>

                    <!-- Drop Indicator -->
                    <div v-if="isDragging" class="absolute inset-0 flex items-center justify-center text-blue-500 bg-blue-50/50 border-2 border-dashed border-blue-300 z-20">
                        <div class="text-center">
                            <div class="text-lg mb-2">Drop component here</div>
                            <div class="text-sm">Release to add {{ draggedItem?.name }} to your page</div>
                        </div>
                    </div>

                    <!-- Elements with Responsive Properties -->
                    <div
                        v-for="element in visibleElements"
                        :key="element.id"
                        :class="[
              'absolute border-2 transition-all duration-200',
              selectedElement === element.id ? 'border-blue-500 shadow-lg' : 'border-transparent hover:border-gray-300',
              element.properties?.locked ? 'cursor-not-allowed' : 'cursor-pointer'
            ]"
                        :style="getElementStyle(element)"
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
                                    <Layout class="w-8 h-8 mx-auto mb-2" />
                                    <div class="text-sm">Container</div>
                                </div>
                            </div>

                            <!-- Default Element -->
                            <div v-else class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                <div class="text-center text-gray-600">
                                    <Square class="w-6 h-6 mx-auto mb-1" />
                                    <div class="text-xs">{{ element.name }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Selection Controls -->
                        <div v-if="selectedElement === element.id" class="absolute inset-0 pointer-events-none">
                            <!-- Resize Handles -->
                            <div class="absolute -top-1 -left-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-nw-resize pointer-events-auto"
                                 @mousedown.stop="startResize(element, 'nw', $event)"></div>
                            <div class="absolute -top-1 right-1/2 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-n-resize pointer-events-auto"
                                 @mousedown.stop="startResize(element, 'n', $event)"></div>
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-ne-resize pointer-events-auto"
                                 @mousedown.stop="startResize(element, 'ne', $event)"></div>
                            <div class="absolute top-1/2 -right-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-e-resize pointer-events-auto"
                                 @mousedown.stop="startResize(element, 'e', $event)"></div>
                            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-se-resize pointer-events-auto"
                                 @mousedown.stop="startResize(element, 'se', $event)"></div>
                            <div class="absolute -bottom-1 left-1/2 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-s-resize pointer-events-auto"
                                 @mousedown.stop="startResize(element, 's', $event)"></div>
                            <div class="absolute -bottom-1 -left-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-sw-resize pointer-events-auto"
                                 @mousedown.stop="startResize(element, 'sw', $event)"></div>
                            <div class="absolute top-1/2 -left-1 w-3 h-3 bg-blue-500 border border-white rounded-full cursor-w-resize pointer-events-auto"
                                 @mousedown.stop="startResize(element, 'w', $event)"></div>
                        </div>

                        <!-- Action Buttons for Selected Element -->
                        <div v-if="selectedElement === element.id" class="absolute -top-10 left-0 flex gap-1 z-50">
                            <button
                                @click.stop="duplicateElement(element.id)"
                                class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600 transition-colors"
                                title="Duplicate"
                            >
                                <Copy class="h-3 w-3" />
                            </button>
                            <button
                                @click.stop="handleDelete(element.id)"
                                class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600 transition-colors"
                                title="Delete"
                            >
                                <Trash2 class="h-3 w-3" />
                            </button>
                        </div>
                    </div>

                    <!-- Responsive Guidelines -->
                    <div v-if="showResponsiveGuides" class="absolute inset-0 pointer-events-none z-40">
                        <div
                            v-for="guide in responsiveGuides"
                            :key="guide.width"
                            class="absolute h-full border-l border-dashed opacity-30"
                            :class="guide.color"
                            :style="{ left: guide.position + 'px' }"
                        >
                            <div class="absolute -top-6 left-1 text-xs bg-white px-1 rounded" :class="guide.textColor">
                                {{ guide.label }}
                            </div>
                        </div>
                    </div>

                    <!-- Alignment Guidelines -->
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
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onUnmounted } from 'vue'
import { Monitor, Tablet, Smartphone, Trash2, Copy, Grid3X3, Layout, Square } from 'lucide-vue-next'

const props = defineProps({
    elements: Array,
    selectedElement: String,
    activeBreakpoint: {
        type: String,
        default: 'desktop'
    },
    showGrid: {
        type: Boolean,
        default: true
    },
    zoom: {
        type: Number,
        default: 1
    }
})

const emit = defineEmits(['update:elements', 'update:selectedElement'])

// Inject drag and drop context
const dragDropContext = inject('dragDrop', {
    draggedItem: ref(null),
    isDragging: ref(false)
})

const { draggedItem, isDragging } = dragDropContext

// Breakpoints configuration
const breakpoints = [
    { id: 'mobile', name: 'Mobile', width: 375, icon: Smartphone },
    { id: 'tablet', name: 'Tablet', width: 768, icon: Tablet },
    { id: 'desktop', name: 'Desktop', width: 1200, icon: Monitor }
]

// Refs
const canvasRef = ref(null)
const dragOver = ref(false)
const showGrid = ref(true)
const showResponsiveGuides = ref(true)
const gridSize = ref(20)
const guidelines = ref([])
const contextMenu = ref({ show: false, x: 0, y: 0 })
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

// Get current breakpoint data
const getCurrentBreakpoint = () => {
    return breakpoints.find(bp => bp.id === props.activeBreakpoint)
}

// Canvas styling based on active breakpoint
const canvasStyle = computed(() => ({
    width: getCurrentBreakpoint()?.width + 'px',
    minHeight: '800px',
    transform: `scale(${props.zoom})`,
    transformOrigin: 'top center'
}))

const canvasContentStyle = computed(() => ({
    width: '100%',
    height: '100%',
    minHeight: '800px'
}))

// Grid style
const gridStyle = computed(() => ({
    backgroundImage: `
    linear-gradient(to right, #e5e7eb 1px, transparent 1px),
    linear-gradient(to bottom, #e5e7eb 1px, transparent 1px)
  `,
    backgroundSize: `${gridSize.value}px ${gridSize.value}px`,
    opacity: 0.3
}))

// Responsive guides
const responsiveGuides = computed(() => {
    const currentWidth = getCurrentBreakpoint()?.width || 1200
    const guides = []

    breakpoints.forEach(bp => {
        if (bp.id !== props.activeBreakpoint && bp.width < currentWidth) {
            guides.push({
                width: bp.width,
                position: bp.width,
                label: bp.name,
                color: bp.id === 'mobile' ? 'border-green-400' : 'border-orange-400',
                textColor: bp.id === 'mobile' ? 'text-green-600' : 'text-orange-600'
            })
        }
    })

    return guides
})

// Filter visible elements for current breakpoint
const visibleElements = computed(() => {
    return props.elements.filter(element => {
        // Skip hidden elements
        if (element.properties?.hidden) return false

        // Check responsive visibility
        const responsiveProps = element.responsiveProperties || {}
        const breakpointProps = responsiveProps[props.activeBreakpoint] || {}

        // If no responsive settings, show by default
        if (!responsiveProps[props.activeBreakpoint]) return true

        // Check visibility setting for this breakpoint
        return breakpointProps.visible !== false
    })
})

// Get selected element data
const selectedElementData = computed(() =>
    props.elements.find(el => el.id === props.selectedElement)
)

// Get element style with responsive properties
const getElementStyle = (element) => {
    const baseStyle = {
        left: element.x + 'px',
        top: element.y + 'px',
        width: element.width + 'px',
        height: element.height + 'px',
        zIndex: props.selectedElement === element.id ? 1000 : element.zIndex || 1,
        opacity: element.properties?.locked ? 0.7 : 1
    }

    // Apply responsive overrides
    const responsiveProps = element.responsiveProperties || {}
    const breakpointProps = responsiveProps[props.activeBreakpoint] || {}

    // Override base properties with breakpoint-specific ones
    if (breakpointProps.x !== undefined) baseStyle.left = breakpointProps.x + 'px'
    if (breakpointProps.y !== undefined) baseStyle.top = breakpointProps.y + 'px'
    if (breakpointProps.width !== undefined) baseStyle.width = breakpointProps.width + 'px'
    if (breakpointProps.height !== undefined) baseStyle.height = breakpointProps.height + 'px'

    return baseStyle
}

// Snap to grid function
const snapToGrid = (value) => {
    return Math.round(value / gridSize.value) * gridSize.value
}

// Check if element is visible on specific breakpoint
const isVisibleOnBreakpoint = (element, breakpointId) => {
    if (!element) return true
    const responsiveProps = element.responsiveProperties || {}
    const breakpointProps = responsiveProps[breakpointId] || {}
    return breakpointProps.visible !== false
}

// Toggle element visibility for specific breakpoint
const toggleBreakpointVisibility = (breakpointId, visible) => {
    if (!props.selectedElement) return

    const updatedElements = props.elements.map(el => {
        if (el.id === props.selectedElement) {
            const responsiveProperties = el.responsiveProperties || {}
            const breakpointProps = responsiveProperties[breakpointId] || {}

            return {
                ...el,
                responsiveProperties: {
                    ...responsiveProperties,
                    [breakpointId]: {
                        ...breakpointProps,
                        visible
                    }
                }
            }
        }
        return el
    })

    emit('update:elements', updatedElements)
}

// Default properties for different element types
const getDefaultProperties = (type) => {
    switch (type) {
        case "text":
            return { content: "Sample text", fontSize: 16, color: "#000000", fontWeight: "normal", textAlign: "left" }
        case "heading":
            return { content: "Heading", fontSize: 24, color: "#000000", level: 1, textAlign: "left" }
        case "button":
            return { text: "Button", backgroundColor: "#3b82f6", textColor: "#ffffff", fontSize: "16", borderRadius: "4px" }
        case "image":
            return { src: "/placeholder.svg?height=200&width=200", alt: "Image", objectFit: "cover", borderRadius: "0px" }
        case "container":
            return { backgroundColor: "transparent", padding: "20px" }
        default:
            return {}
    }
}

// Event handlers
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

const handleElementClick = (element) => {
    if (element.properties?.locked || element.properties?.hidden) return
    emit('update:selectedElement', element.id)
}

const handleElementMouseDown = (element, event) => {
    if (element.properties?.locked || element.properties?.hidden) return
    startDrag(element, event)
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

    emit('update:selectedElement', element.id)
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
            ? { ...el, x: Math.max(0, newX), y: Math.max(0, newY) }
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
    const { handle, element, startWidth, startHeight, startLeft, startTop } = resizing.value

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
            ? { ...el, width: newWidth, height: newHeight, x: newX, y: newY }
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
        if (Math.abs(el.x - x) < 5) guides.push({ type: 'vertical', position: el.x })
        if (Math.abs(el.x + el.width - x) < 5) guides.push({ type: 'vertical', position: el.x + el.width })

        // Horizontal alignment
        if (Math.abs(el.y - y) < 5) guides.push({ type: 'horizontal', position: el.y })
        if (Math.abs(el.y + el.height - y) < 5) guides.push({ type: 'horizontal', position: el.y + el.height })
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

const toggleResponsiveGuides = () => {
    showResponsiveGuides.value = !showResponsiveGuides.value
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
