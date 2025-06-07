<!-- ResponsivePropertiesPanel.vue - Properties panel with responsive controls -->
<template>
    <div class="flex flex-col h-full bg-white border-l">
        <!-- Header -->
        <div class="p-4 border-b">
            <h3 class="font-semibold text-lg">Properties</h3>
            <p v-if="!selectedElement" class="text-sm text-gray-500 mt-1">
                Select an element to edit its properties
            </p>
            <p v-else class="text-sm text-gray-500 mt-1">
                {{ selectedElementData?.name || 'Element' }}
            </p>
        </div>

        <!-- Content -->
        <div v-if="selectedElement" class="flex-1 overflow-y-auto">
            <!-- Responsive Breakpoint Selector -->
            <div class="p-4 border-b bg-gray-50">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Edit properties for:
                </label>
                <div class="grid grid-cols-3 gap-1">
                    <button
                        v-for="breakpoint in breakpoints"
                        :key="breakpoint.id"
                        @click="activeBreakpoint = breakpoint.id"
                        :class="[
              'flex flex-col items-center gap-1 p-2 rounded-lg border transition-all text-xs',
              activeBreakpoint === breakpoint.id
                ? 'border-blue-500 bg-blue-50 text-blue-700'
                : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'
            ]"
                    >
                        <component :is="breakpoint.icon" class="h-4 w-4" />
                        <span>{{ breakpoint.name }}</span>
                        <span class="text-xs opacity-75">{{ breakpoint.width }}px</span>
                    </button>
                </div>

                <!-- Inheritance Info -->
                <div v-if="hasInheritedProperties" class="mt-3 p-2 bg-blue-50 rounded text-xs text-blue-700">
                    <Info class="h-3 w-3 inline mr-1" />
                    Some properties inherited from larger breakpoints
                </div>
            </div>

            <!-- Visibility Controls -->
            <div class="p-4 border-b">
                <h4 class="font-medium text-sm mb-3">Visibility</h4>

                <!-- Current Breakpoint Visibility -->
                <div class="mb-3">
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            :checked="isVisibleOnCurrentBreakpoint"
                            @change="toggleCurrentBreakpointVisibility($event.target.checked)"
                            class="rounded"
                        />
                        <span class="text-sm">Visible on {{ getCurrentBreakpoint().name }}</span>
                    </label>
                </div>

                <!-- All Breakpoints Overview -->
                <div class="space-y-2">
                    <label class="text-xs font-medium text-gray-700">Visibility across devices:</label>
                    <div class="grid grid-cols-1 gap-1">
                        <label
                            v-for="bp in breakpoints"
                            :key="bp.id"
                            class="flex items-center justify-between p-2 rounded border"
                            :class="isVisibleOnBreakpoint(bp.id) ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'"
                        >
                            <div class="flex items-center gap-2">
                                <component :is="bp.icon" class="h-4 w-4" />
                                <span class="text-sm">{{ bp.name }}</span>
                            </div>
                            <input
                                type="checkbox"
                                :checked="isVisibleOnBreakpoint(bp.id)"
                                @change="toggleBreakpointVisibility(bp.id, $event.target.checked)"
                                class="rounded"
                            />
                        </label>
                    </div>
                </div>
            </div>

            <!-- Position & Size -->
            <div class="p-4 border-b">
                <h4 class="font-medium text-sm mb-3 flex items-center gap-2">
                    <Move class="h-4 w-4" />
                    Position & Size
                    <span v-if="hasResponsiveOverrides.position" class="text-xs bg-blue-100 text-blue-700 px-1 rounded">
            {{ getCurrentBreakpoint().name }}
          </span>
                </h4>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">X Position</label>
                        <input
                            type="number"
                            :value="getResponsiveProperty('x')"
                            @input="updateResponsiveProperty('x', parseInt($event.target.value))"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Y Position</label>
                        <input
                            type="number"
                            :value="getResponsiveProperty('y')"
                            @input="updateResponsiveProperty('y', parseInt($event.target.value))"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Width</label>
                        <input
                            type="number"
                            :value="getResponsiveProperty('width')"
                            @input="updateResponsiveProperty('width', parseInt($event.target.value))"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Height</label>
                        <input
                            type="number"
                            :value="getResponsiveProperty('height')"
                            @input="updateResponsiveProperty('height', parseInt($event.target.value))"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>
                </div>

                <!-- Reset Overrides Button -->
                <button
                    v-if="hasResponsiveOverrides.position"
                    @click="clearResponsiveOverrides(['x', 'y', 'width', 'height'])"
                    class="mt-2 text-xs text-blue-600 hover:text-blue-800"
                >
                    Reset to inherit from larger breakpoints
                </button>
            </div>

            <!-- Element-Specific Properties -->
            <div class="p-4 border-b">
                <h4 class="font-medium text-sm mb-3">Element Properties</h4>

                <!-- Text Element Properties -->
                <div v-if="selectedElementData?.type === 'text'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Content</label>
                        <input
                            type="text"
                            :value="selectedElementData.properties.content"
                            @input="updateProperty('content', $event.target.value)"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Font Size</label>
                        <input
                            type="number"
                            :value="selectedElementData.properties.fontSize"
                            @input="updateProperty('fontSize', parseInt($event.target.value))"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Color</label>
                        <input
                            type="color"
                            :value="selectedElementData.properties.color"
                            @input="updateProperty('color', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                </div>

                <!-- Button Element Properties -->
                <div v-else-if="selectedElementData?.type === 'button'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Button Text</label>
                        <input
                            type="text"
                            :value="selectedElementData.properties.text"
                            @input="updateProperty('text', $event.target.value)"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Background Color</label>
                        <input
                            type="color"
                            :value="selectedElementData.properties.backgroundColor"
                            @input="updateProperty('backgroundColor', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Text Color</label>
                        <input
                            type="color"
                            :value="selectedElementData.properties.textColor"
                            @input="updateProperty('textColor', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                </div>

                <!-- Image Element Properties -->
                <div v-else-if="selectedElementData?.type === 'image'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Image URL</label>
                        <input
                            type="url"
                            :value="selectedElementData.properties.src"
                            @input="updateProperty('src', $event.target.value)"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Alt Text</label>
                        <input
                            type="text"
                            :value="selectedElementData.properties.alt"
                            @input="updateProperty('alt', $event.target.value)"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Object Fit</label>
                        <select
                            :value="selectedElementData.properties.objectFit"
                            @input="updateProperty('objectFit', $event.target.value)"
                            class="w-full px-2 py-1 border rounded text-sm"
                        >
                            <option value="cover">Cover</option>
                            <option value="contain">Contain</option>
                            <option value="fill">Fill</option>
                            <option value="scale-down">Scale Down</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Advanced -->
            <div class="p-4">
                <h4 class="font-medium text-sm mb-3">Advanced</h4>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Z-Index</label>
                        <input
                            type="number"
                            :value="selectedElementData?.zIndex || 1"
                            @input="updateProperty('zIndex', parseInt($event.target.value), true)"
                            class="w-full px-2 py-1 border rounded text-sm"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            :checked="selectedElementData?.properties?.locked"
                            @change="updateProperty('locked', $event.target.checked)"
                            class="rounded"
                        />
                        <label class="text-sm">Lock element</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            :checked="selectedElementData?.properties?.hidden"
                            @change="updateProperty('hidden', $event.target.checked)"
                            class="rounded"
                        />
                        <label class="text-sm">Hide element</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Selection State -->
        <div v-else class="flex-1 flex items-center justify-center text-center p-8">
            <div>
                <MousePointer class="h-12 w-12 mx-auto text-gray-300 mb-4" />
                <h4 class="font-medium text-gray-600 mb-2">No Element Selected</h4>
                <p class="text-sm text-gray-500">
                    Click on an element in the canvas to edit its properties
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Monitor, Tablet, Smartphone, Move, Info, MousePointer } from 'lucide-vue-next'

const props = defineProps({
    selectedElement: String,
    elements: Array,
    activeBreakpoint: {
        type: String,
        default: 'desktop'
    }
})

const emit = defineEmits(['update:elements'])

// Local active breakpoint for properties panel
const activeBreakpoint = ref(props.activeBreakpoint)

// Breakpoints configuration
const breakpoints = [
    { id: 'mobile', name: 'Mobile', width: 375, icon: Smartphone },
    { id: 'tablet', name: 'Tablet', width: 768, icon: Tablet },
    { id: 'desktop', name: 'Desktop', width: 1200, icon: Monitor }
]

// Computed properties
const selectedElementData = computed(() =>
    props.elements.find(el => el.id === props.selectedElement)
)

const getCurrentBreakpoint = () => {
    return breakpoints.find(bp => bp.id === activeBreakpoint.value) || breakpoints[2]
}

const isVisibleOnCurrentBreakpoint = computed(() => {
    return isVisibleOnBreakpoint(activeBreakpoint.value)
})

const hasInheritedProperties = computed(() => {
    if (!selectedElementData.value) return false

    const responsiveProps = selectedElementData.value.responsiveProperties || {}
    const currentProps = responsiveProps[activeBreakpoint.value] || {}

    // Check if any position/size properties are missing (inherited)
    return !currentProps.x && !currentProps.y && !currentProps.width && !currentProps.height
})

const hasResponsiveOverrides = computed(() => {
    if (!selectedElementData.value) return { position: false }

    const responsiveProps = selectedElementData.value.responsiveProperties || {}
    const currentProps = responsiveProps[activeBreakpoint.value] || {}

    return {
        position: !!(currentProps.x || currentProps.y || currentProps.width || currentProps.height)
    }
})

// Methods
const isVisibleOnBreakpoint = (breakpointId) => {
    if (!selectedElementData.value) return true

    const responsiveProps = selectedElementData.value.responsiveProperties || {}
    const breakpointProps = responsiveProps[breakpointId] || {}

    return breakpointProps.visible !== false
}

const toggleCurrentBreakpointVisibility = (visible) => {
    toggleBreakpointVisibility(activeBreakpoint.value, visible)
}

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

const getResponsiveProperty = (property) => {
    if (!selectedElementData.value) return 0

    const responsiveProps = selectedElementData.value.responsiveProperties || {}
    const currentProps = responsiveProps[activeBreakpoint.value] || {}

    // Return responsive override if exists, otherwise base property
    return currentProps[property] !== undefined
        ? currentProps[property]
        : selectedElementData.value[property]
}

const updateResponsiveProperty = (property, value) => {
    if (!props.selectedElement) return

    const updatedElements = props.elements.map(el => {
        if (el.id === props.selectedElement) {
            const responsiveProperties = el.responsiveProperties || {}
            const breakpointProps = responsiveProperties[activeBreakpoint.value] || {}

            return {
                ...el,
                responsiveProperties: {
                    ...responsiveProperties,
                    [activeBreakpoint.value]: {
                        ...breakpointProps,
                        [property]: value
                    }
                }
            }
        }
        return el
    })

    emit('update:elements', updatedElements)
}

const updateProperty = (property, value, isTopLevel = false) => {
    if (!props.selectedElement) return

    const updatedElements = props.elements.map(el => {
        if (el.id === props.selectedElement) {
            if (isTopLevel) {
                return { ...el, [property]: value }
            } else {
                return {
                    ...el,
                    properties: {
                        ...el.properties,
                        [property]: value
                    }
                }
            }
        }
        return el
    })

    emit('update:elements', updatedElements)
}

const clearResponsiveOverrides = (properties) => {
    if (!props.selectedElement) return

    const updatedElements = props.elements.map(el => {
        if (el.id === props.selectedElement) {
            const responsiveProperties = { ...el.responsiveProperties } || {}
            const breakpointProps = { ...responsiveProperties[activeBreakpoint.value] } || {}

            // Remove the specified properties from this breakpoint
            properties.forEach(prop => {
                delete breakpointProps[prop]
            })

            // If no properties left, remove the entire breakpoint object
            if (Object.keys(breakpointProps).length === 0) {
                delete responsiveProperties[activeBreakpoint.value]
            } else {
                responsiveProperties[activeBreakpoint.value] = breakpointProps
            }

            return {
                ...el,
                responsiveProperties
            }
        }
        return el
    })

    emit('update:elements', updatedElements)
}
</script>
