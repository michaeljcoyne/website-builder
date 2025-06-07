<!-- ResponsiveToolbar.vue - Enhanced toolbar with responsive design controls -->
<template>
    <div class="h-16 border-b bg-white flex items-center px-4 gap-4 shadow-sm">
        <!-- Project Info & Actions -->
        <div class="flex items-center gap-3">
            <!-- Project Name -->
            <div class="flex items-center gap-2">
                <FileText class="h-5 w-5 text-gray-600" />
                <span class="font-medium text-gray-900">{{ projectName }}</span>
            </div>

            <!-- Project Actions -->
            <div class="flex items-center gap-1">
                <button
                    @click="$emit('save')"
                    :disabled="isSaving"
                    :class="[
            'flex items-center gap-2 px-3 py-1.5 text-sm rounded-md transition-all',
            isSaving
              ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
              : 'bg-blue-600 text-white hover:bg-blue-700'
          ]"
                >
                    <Save class="h-4 w-4" />
                    {{ isSaving ? 'Saving...' : 'Save' }}
                </button>

                <button
                    @click="$emit('preview')"
                    class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors"
                >
                    <Eye class="h-4 w-4" />
                    Preview
                </button>

                <button
                    @click="$emit('export')"
                    class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors"
                >
                    <Download class="h-4 w-4" />
                    Export
                </button>
            </div>
        </div>

        <!-- Separator -->
        <div class="h-8 w-px bg-gray-200"></div>

        <!-- Responsive Controls -->
        <div class="flex items-center gap-4">
            <!-- Device Preview Selector -->
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Device:</span>
                <div class="flex rounded-lg border border-gray-200 bg-gray-50 p-1">
                    <button
                        v-for="breakpoint in breakpoints"
                        :key="breakpoint.id"
                        @click="setActiveBreakpoint(breakpoint.id)"
                        :class="[
              'flex items-center gap-2 px-3 py-1.5 text-sm rounded-md transition-all',
              activeBreakpoint === breakpoint.id
                ? 'bg-white text-blue-700 shadow-sm border border-blue-200'
                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
            ]"
                        :title="`${breakpoint.name} (${breakpoint.width}px)`"
                    >
                        <component :is="breakpoint.icon" class="h-4 w-4" />
                        <span class="hidden sm:inline">{{ breakpoint.name }}</span>
                        <span class="text-xs opacity-75 hidden md:inline">{{ breakpoint.width }}px</span>
                    </button>
                </div>
            </div>

            <!-- Current Canvas Info -->
            <div class="hidden lg:flex items-center gap-2 text-sm text-gray-600">
                <Monitor class="h-4 w-4" />
                <span>{{ getCurrentBreakpoint().width }}×{{ canvasHeight }}px</span>
                <span class="text-gray-400">•</span>
                <span>{{ visibleElementsCount }} elements</span>
            </div>
        </div>

        <!-- Separator -->
        <div class="h-8 w-px bg-gray-200"></div>

        <!-- View Controls -->
        <div class="flex items-center gap-2">
            <!-- Grid Toggle -->
            <button
                @click="$emit('toggle-grid')"
                :class="[
          'flex items-center gap-2 px-2 py-1.5 text-sm rounded-md transition-colors',
          showGrid ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'
        ]"
                title="Toggle Grid"
            >
                <Grid3X3 class="h-4 w-4" />
                <span class="hidden sm:inline">Grid</span>
            </button>

            <!-- Responsive Guides Toggle -->
            <button
                @click="$emit('toggle-guides')"
                :class="[
          'flex items-center gap-2 px-2 py-1.5 text-sm rounded-md transition-colors',
          showGuides ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'
        ]"
                title="Toggle Responsive Guides"
            >
                <Rulers class="h-4 w-4" />
                <span class="hidden sm:inline">Guides</span>
            </button>
        </div>

        <!-- Separator -->
        <div class="h-8 w-px bg-gray-200"></div>

        <!-- History Controls -->
        <div class="flex items-center gap-1">
            <button
                @click="$emit('undo')"
                :disabled="!canUndo"
                class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-md disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                title="Undo (Ctrl+Z)"
            >
                <Undo class="h-4 w-4" />
            </button>
            <button
                @click="$emit('redo')"
                :disabled="!canRedo"
                class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-md disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                title="Redo (Ctrl+Y)"
            >
                <Redo class="h-4 w-4" />
            </button>
        </div>

        <!-- Right Side Controls -->
        <div class="ml-auto flex items-center gap-4">
            <!-- Zoom Controls -->
            <div class="flex items-center gap-2">
                <button
                    @click="$emit('zoom', 'out')"
                    :disabled="zoom <= 0.25"
                    class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-md disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    title="Zoom Out"
                >
                    <ZoomOut class="h-4 w-4" />
                </button>

                <div class="flex items-center gap-1">
                    <span class="text-sm text-gray-600 min-w-12 text-center">{{ Math.round(zoom * 100) }}%</span>
                    <button
                        @click="$emit('zoom', 'reset')"
                        class="text-xs text-gray-500 hover:text-gray-700 px-1"
                        title="Reset Zoom"
                    >
                        Reset
                    </button>
                </div>

                <button
                    @click="$emit('zoom', 'in')"
                    :disabled="zoom >= 2"
                    class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-md disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    title="Zoom In"
                >
                    <ZoomIn class="h-4 w-4" />
                </button>
            </div>

            <!-- Separator -->
            <div class="h-8 w-px bg-gray-200"></div>

            <!-- Responsive Design Mode Toggle -->
            <button
                @click="toggleResponsiveMode"
                :class="[
          'flex items-center gap-2 px-3 py-1.5 text-sm rounded-md transition-all',
          responsiveMode
            ? 'bg-purple-100 text-purple-700 border border-purple-200'
            : 'text-gray-600 hover:bg-gray-100'
        ]"
                title="Toggle Responsive Design Mode"
            >
                <Smartphone class="h-4 w-4" />
                <span class="hidden lg:inline">Responsive</span>
            </button>

            <!-- Settings -->
            <button
                @click="$emit('open-settings')"
                class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-md transition-colors"
                title="Settings"
            >
                <Settings class="h-4 w-4" />
            </button>
        </div>

        <!-- Responsive Design Mode Indicator -->
        <div v-if="responsiveMode" class="absolute top-full left-0 right-0 bg-purple-50 border-b border-purple-200 px-4 py-2">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <Smartphone class="h-4 w-4 text-purple-600" />
                    <span class="text-purple-700 font-medium">Responsive Design Mode</span>
                    <span class="text-purple-600">- Design for multiple screen sizes</span>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Quick Actions in Responsive Mode -->
                    <button
                        @click="$emit('auto-layout')"
                        class="text-purple-600 hover:text-purple-800 text-sm"
                    >
                        Auto Layout
                    </button>
                    <button
                        @click="$emit('test-breakpoints')"
                        class="text-purple-600 hover:text-purple-800 text-sm"
                    >
                        Test All Breakpoints
                    </button>
                    <button
                        @click="toggleResponsiveMode"
                        class="text-purple-600 hover:text-purple-800"
                        title="Exit Responsive Mode"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
    FileText, Save, Eye, Download, Undo, Redo, Monitor, Tablet, Smartphone,
    Grid3X3, Rulers, ZoomIn, ZoomOut, Settings, X
} from 'lucide-vue-next'

const props = defineProps({
    projectName: {
        type: String,
        default: 'Untitled Project'
    },
    isSaving: {
        type: Boolean,
        default: false
    },
    activeBreakpoint: {
        type: String,
        default: 'desktop'
    },
    canUndo: {
        type: Boolean,
        default: false
    },
    canRedo: {
        type: Boolean,
        default: false
    },
    zoom: {
        type: Number,
        default: 1
    },
    showGrid: {
        type: Boolean,
        default: true
    },
    showGuides: {
        type: Boolean,
        default: true
    },
    canvasHeight: {
        type: Number,
        default: 800
    },
    visibleElementsCount: {
        type: Number,
        default: 0
    }
})

const emit = defineEmits([
    'save', 'preview', 'export', 'undo', 'redo', 'zoom',
    'update:activeBreakpoint', 'toggle-grid', 'toggle-guides',
    'open-settings', 'auto-layout', 'test-breakpoints'
])

// Local state
const responsiveMode = ref(false)

// Breakpoints configuration
const breakpoints = [
    { id: 'mobile', name: 'Mobile', width: 375, icon: Smartphone },
    { id: 'tablet', name: 'Tablet', width: 768, icon: Tablet },
    { id: 'desktop', name: 'Desktop', width: 1200, icon: Monitor }
]

// Computed properties
const getCurrentBreakpoint = () => {
    return breakpoints.find(bp => bp.id === props.activeBreakpoint) || breakpoints[2]
}

// Methods
const setActiveBreakpoint = (breakpointId) => {
    emit('update:activeBreakpoint', breakpointId)
}

const toggleResponsiveMode = () => {
    responsiveMode.value = !responsiveMode.value

    // If entering responsive mode, switch to mobile first
    if (responsiveMode.value && props.activeBreakpoint === 'desktop') {
        setActiveBreakpoint('mobile')
    }
}

// Keyboard shortcuts
const handleKeydown = (e) => {
    // Breakpoint shortcuts
    if (e.key === '1' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault()
        setActiveBreakpoint('mobile')
    }
    if (e.key === '2' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault()
        setActiveBreakpoint('tablet')
    }
    if (e.key === '3' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault()
        setActiveBreakpoint('desktop')
    }

    // Other shortcuts
    if (e.key === 'r' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault()
        toggleResponsiveMode()
    }
    if (e.key === 'g' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault()
        emit('toggle-grid')
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
