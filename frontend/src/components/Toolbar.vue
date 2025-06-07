<!-- Enhanced Toolbar.vue with Responsive Controls -->
<template>
    <div class="h-14 border-b bg-white flex items-center px-4 gap-2">
        <!-- Project Actions -->
        <div class="flex items-center gap-2">
            <button
                @click="$emit('save')"
                :disabled="isSaving"
                class="flex items-center gap-2 px-3 py-1.5 text-sm hover:bg-gray-100 rounded disabled:opacity-50"
            >
                <Save class="h-4 w-4" />
                {{ isSaving ? 'Saving...' : 'Save' }}
            </button>
            <button
                @click="$emit('preview')"
                class="flex items-center gap-2 px-3 py-1.5 text-sm hover:bg-gray-100 rounded"
            >
                <Eye class="h-4 w-4" />
                Preview
            </button>
            <button
                @click="$emit('export')"
                class="flex items-center gap-2 px-3 py-1.5 text-sm hover:bg-gray-100 rounded"
            >
                <Download class="h-4 w-4" />
                Export
            </button>
        </div>

        <div class="h-6 w-px bg-gray-300 mx-2"></div>

        <!-- Responsive Breakpoint Controls -->
        <div class="flex items-center gap-1">
            <span class="text-xs font-medium text-gray-700 mr-2">Breakpoint:</span>
            <button
                v-for="breakpoint in breakpointsWithIcons"
                :key="breakpoint.id"
                @click="setBreakpoint(breakpoint.id)"
                :class="[
                    'flex items-center gap-1 px-3 py-1.5 text-sm rounded transition-colors',
                    activeBreakpoint === breakpoint.id
                        ? 'bg-blue-100 text-blue-700 border border-blue-300'
                        : 'hover:bg-gray-100 text-gray-600'
                ]"
                :title="`${breakpoint.name} (${breakpoint.width}px)`"
            >
                <component :is="breakpoint.icon" class="h-4 w-4" />
                <span class="hidden sm:inline">{{ breakpoint.name }}</span>
            </button>
        </div>

        <div class="h-6 w-px bg-gray-300 mx-2"></div>

        <!-- Canvas Size Display -->
        <div class="text-xs text-gray-500">
            {{ getCurrentBreakpoint()?.width }}px
            <span v-if="canvasSize"> • {{ canvasSize.width }}×{{ canvasSize.height }}</span>
        </div>

        <div class="h-6 w-px bg-gray-300 mx-2"></div>

        <!-- View Controls -->
        <div class="flex items-center gap-1">
            <button
                @click="$emit('toggle-grid')"
                :class="[
                    'flex items-center gap-1 px-2 py-1.5 text-sm rounded transition-colors',
                    showGrid ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'
                ]"
                title="Toggle Grid"
            >
                <Grid3X3 class="h-4 w-4" />
                <span class="hidden md:inline">Grid</span>
            </button>
            <button
                @click="$emit('toggle-guides')"
                :class="[
                    'flex items-center gap-1 px-2 py-1.5 text-sm rounded transition-colors',
                    showGuides ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'
                ]"
                title="Toggle Responsive Guides"
            >
                <AlignLeft class="h-4 w-4" />
                <span class="hidden md:inline">Guides</span>
            </button>
        </div>

        <div class="h-6 w-px bg-gray-300 mx-2"></div>

        <!-- History Controls -->
        <div class="flex items-center gap-1">
            <button
                @click="$emit('undo')"
                :disabled="!canUndo"
                class="p-1.5 hover:bg-gray-100 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                title="Undo"
            >
                <Undo class="h-4 w-4" />
            </button>
            <button
                @click="$emit('redo')"
                :disabled="!canRedo"
                class="p-1.5 hover:bg-gray-100 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                title="Redo"
            >
                <Redo class="h-4 w-4" />
            </button>
        </div>

        <div class="ml-auto flex items-center gap-2">
            <!-- Zoom Controls -->
            <div class="flex items-center gap-1">
                <button
                    @click="$emit('zoom', 'out')"
                    class="p-1.5 hover:bg-gray-100 rounded"
                    title="Zoom Out"
                >
                    <ZoomOut class="h-4 w-4" />
                </button>
                <span class="text-xs text-gray-600 min-w-12 text-center">{{ Math.round(zoom * 100) }}%</span>
                <button
                    @click="$emit('zoom', 'in')"
                    class="p-1.5 hover:bg-gray-100 rounded"
                    title="Zoom In"
                >
                    <ZoomIn class="h-4 w-4" />
                </button>
            </div>

            <div class="h-6 w-px bg-gray-300 mx-2"></div>

            <!-- Settings -->
            <button
                @click="$emit('open-settings')"
                class="p-1.5 hover:bg-gray-100 rounded"
                title="Settings"
            >
                <Settings class="h-4 w-4" />
            </button>
        </div>
    </div>
</template>

<script setup>
import {
    Save, Eye, Download, Undo, Redo, Monitor, Tablet, Smartphone, Settings,
    ZoomIn, ZoomOut, Grid3X3, AlignLeft
} from 'lucide-vue-next'
import { useResponsive } from '@/composables/useResponsive'

const props = defineProps({
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
    canvasSize: {
        type: Object,
        default: null
    },
    showGrid: {
        type: Boolean,
        default: true
    },
    showGuides: {
        type: Boolean,
        default: true
    }
})

defineEmits([
    'save', 'preview', 'export', 'undo', 'redo', 'zoom',
    'toggle-grid', 'toggle-guides', 'open-settings'
])

// Use the responsive composable directly
const { activeBreakpoint, breakpoints, getCurrentBreakpoint, setBreakpoint } = useResponsive()

// Add icons to breakpoints for display
const breakpointsWithIcons = breakpoints.map(bp => ({
    ...bp,
    icon: bp.id === 'mobile' ? Smartphone : bp.id === 'tablet' ? Tablet : Monitor
}))
</script>
