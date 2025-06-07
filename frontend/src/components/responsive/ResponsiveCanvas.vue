<!-- ResponsiveCanvas.vue - Canvas wrapper with responsive controls -->
<template>
    <div class="flex-1 flex flex-col">
        <!-- Responsive Controls Bar -->
        <div class="h-12 border-b bg-gray-50 flex items-center px-4 gap-4">
            <!-- Breakpoint Selector -->
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Device:</span>
                <div class="flex rounded-lg border border-gray-200 bg-white p-1">
                    <button
                        v-for="breakpoint in breakpoints"
                        :key="breakpoint.id"
                        @click="activeBreakpoint = breakpoint.id"
                        :class="[
              'flex items-center gap-2 px-3 py-1.5 text-sm rounded-md transition-all',
              activeBreakpoint === breakpoint.id
                ? 'bg-blue-100 text-blue-700 shadow-sm'
                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
            ]"
                        :title="`${breakpoint.name} (${breakpoint.width}px)`"
                    >
                        <component :is="breakpoint.icon" class="h-4 w-4" />
                        <span class="hidden sm:inline">{{ breakpoint.name }}</span>
                        <span class="text-xs opacity-75">{{ breakpoint.width }}px</span>
                    </button>
                </div>
            </div>

            <!-- Canvas Controls -->
            <div class="flex items-center gap-2 ml-auto">
                <!-- Grid Toggle -->
                <button
                    @click="showGrid = !showGrid"
                    :class="[
            'flex items-center gap-2 px-3 py-1.5 text-sm rounded-md transition-colors',
            showGrid ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'
          ]"
                >
                    <Grid3X3 class="h-4 w-4" />
                    Grid
                </button>

                <!-- Responsive Guides Toggle -->
                <button
                    @click="showResponsiveGuides = !showResponsiveGuides"
                    :class="[
            'flex items-center gap-2 px-3 py-1.5 text-sm rounded-md transition-colors',
            showResponsiveGuides ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'
          ]"
                >
                    <Rulers class="h-4 w-4" />
                    Guides
                </button>

                <!-- Zoom Controls -->
                <div class="flex items-center gap-1 ml-4">
                    <button
                        @click="adjustZoom(-0.1)"
                        :disabled="zoom <= 0.2"
                        class="p-1.5 text-gray-600 hover:bg-gray-100 rounded disabled:opacity-50"
                    >
                        <ZoomOut class="h-4 w-4" />
                    </button>
                    <span class="text-sm text-gray-600 min-w-16 text-center">{{ Math.round(zoom * 100) }}%</span>
                    <button
                        @click="adjustZoom(0.1)"
                        :disabled="zoom >= 2"
                        class="p-1.5 text-gray-600 hover:bg-gray-100 rounded disabled:opacity-50"
                    >
                        <ZoomIn class="h-4 w-4" />
                    </button>
                    <button
                        @click="zoom = 1"
                        class="px-2 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded ml-1"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Canvas Area -->
        <div class="flex-1 relative bg-gray-100 overflow-hidden">
            <Canvas
                :elements="elements"
                :selectedElement="selectedElement"
                :activeBreakpoint="activeBreakpoint"
                :showGrid="showGrid"
                :showResponsiveGuides="showResponsiveGuides"
                :zoom="zoom"
                @update:elements="$emit('update:elements', $event)"
                @update:selectedElement="$emit('update:selectedElement', $event)"
            />

            <!-- Responsive Info Overlay -->
            <div class="absolute top-4 left-4 bg-white rounded-lg shadow-sm border px-3 py-2">
                <div class="flex items-center gap-2 text-sm">
                    <component :is="getCurrentBreakpoint().icon" class="h-4 w-4 text-blue-600" />
                    <span class="font-medium">{{ getCurrentBreakpoint().name }}</span>
                    <span class="text-gray-500">{{ getCurrentBreakpoint().width }}px</span>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ visibleElementsCount }} element{{ visibleElementsCount !== 1 ? 's' : '' }} visible
                </div>
            </div>

            <!-- Responsive Preview -->
            <div v-if="showResponsivePreview" class="absolute bottom-4 right-4 bg-white rounded-lg shadow-lg border p-4 max-w-sm">
                <h4 class="font-medium text-sm mb-3">Responsive Preview</h4>
                <div class="grid grid-cols-3 gap-2">
                    <div
                        v-for="bp in breakpoints"
                        :key="bp.id"
                        :class="[
              'relative border-2 rounded cursor-pointer transition-all',
              activeBreakpoint === bp.id ? 'border-blue-500' : 'border-gray-200 hover:border-gray-300'
            ]"
                        @click="activeBreakpoint = bp.id"
                        :title="`Switch to ${bp.name}`"
                    >
                        <div class="aspect-[4/3] bg-gray-50 rounded-sm overflow-hidden">
                            <!-- Mini preview of canvas at this breakpoint -->
                            <div class="w-full h-full bg-gradient-to-br from-blue-50 to-purple-50 flex items-center justify-center">
                                <component :is="bp.icon" class="h-4 w-4 text-gray-400" />
                            </div>
                        </div>
                        <div class="absolute -bottom-6 left-0 text-xs text-gray-600">
                            {{ bp.name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakpoint Visibility Summary -->
        <div v-if="selectedElement" class="h-10 border-t bg-gray-50 flex items-center px-4">
            <span class="text-xs text-gray-600 mr-3">Visible on:</span>
            <div class="flex gap-2">
                <div
                    v-for="bp in breakpoints"
                    :key="bp.id"
                    :class="[
            'flex items-center gap-1 px-2 py-1 rounded text-xs',
            isElementVisibleOnBreakpoint(bp.id)
              ? 'bg-green-100 text-green-700'
              : 'bg-red-100 text-red-700'
          ]"
                >
                    <component :is="bp.icon" class="h-3 w-3" />
                    {{ bp.name }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Monitor, Tablet, Smartphone, Grid3X3, Rulers, ZoomIn, ZoomOut } from 'lucide-vue-next'
import Canvas from './Canvas.vue'

const props = defineProps({
    elements: Array,
    selectedElement: String
})

const emit = defineEmits(['update:elements', 'update:selectedElement'])

// Responsive state
const activeBreakpoint = ref('desktop')
const showGrid = ref(true)
const showResponsiveGuides = ref(true)
const showResponsivePreview = ref(false)
const zoom = ref(1)

// Breakpoints configuration
const breakpoints = [
    { id: 'mobile', name: 'Mobile', width: 375, icon: Smartphone },
    { id: 'tablet', name: 'Tablet', width: 768, icon: Tablet },
    { id: 'desktop', name: 'Desktop', width: 1200, icon: Monitor }
]

// Computed properties
const getCurrentBreakpoint = () => {
    return breakpoints.find(bp => bp.id === activeBreakpoint.value) || breakpoints[2]
}

const visibleElementsCount = computed(() => {
    return props.elements.filter(element => {
        if (element.properties?.hidden) return false

        const responsiveProps = element.responsiveProperties || {}
        const breakpointProps = responsiveProps[activeBreakpoint.value] || {}

        return breakpointProps.visible !== false
    }).length
})

// Methods
const adjustZoom = (delta) => {
    zoom.value = Math.max(0.2, Math.min(2, zoom.value + delta))
}

const isElementVisibleOnBreakpoint = (breakpointId) => {
    if (!props.selectedElement) return false

    const element = props.elements.find(el => el.id === props.selectedElement)
    if (!element) return false

    const responsiveProps = element.responsiveProperties || {}
    const breakpointProps = responsiveProps[breakpointId] || {}

    return breakpointProps.visible !== false
}

// Keyboard shortcuts
const handleKeydown = (e) => {
    if (e.key === '1') activeBreakpoint.value = 'mobile'
    if (e.key === '2') activeBreakpoint.value = 'tablet'
    if (e.key === '3') activeBreakpoint.value = 'desktop'
    if (e.key === 'g' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault()
        showGrid.value = !showGrid.value
    }
}

// Lifecycle
import { onMounted, onUnmounted } from 'vue'

onMounted(() => {
    document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown)
})
</script>
