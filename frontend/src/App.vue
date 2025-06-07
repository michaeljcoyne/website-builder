<template>
    <DragDropProvider>
        <div class="h-screen flex flex-col bg-gray-50">
            <!-- Enhanced Toolbar with Responsive Controls -->
            <Toolbar
                @save="saveProject"
                @preview="previewProject"
                @export="exportProject"
                :is-saving="isSaving"
                :can-undo="canUndo"
                :can-redo="canRedo"
                :zoom="zoom"
                :canvas-size="canvasSize"
                :show-grid="showGrid"
                :show-guides="showResponsiveGuides"
                @undo="undo"
                @redo="redo"
                @zoom="handleZoom"
                @toggle-grid="showGrid = !showGrid"
                @toggle-guides="showResponsiveGuides = !showResponsiveGuides"
                @open-settings="showSettingsModal = true"
            />

            <div class="flex-1 flex overflow-hidden">
                <ComponentSidebar />

                <div class="flex-1 flex">
                    <!-- Use ResponsiveCanvas instead of Canvas -->
                    <ResponsiveCanvas
                        :elements="elements"
                        :selected-element="selectedElement"
                        :show-grid="showGrid"
                        :show-responsive-guides="showResponsiveGuides"
                        :zoom="zoom"
                        @update:elements="setElements"
                        @update:selectedElement="setSelectedElement"
                    />

                    <div class="w-80 border-l bg-white flex flex-col">
                        <LayersPanel
                            :elements="elements"
                            :selectedElement="selectedElement"
                            @update:selectedElement="setSelectedElement"
                            @update:elements="setElements"
                        />

                        <!-- Use ResponsivePropertiesPanel instead of PropertiesPanel -->
                        <ResponsivePropertiesPanel
                            :selected-element="selectedElement"
                            :elements="elements"
                            @update:elements="setElements"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Library Modal -->
        <TemplateLibrary
            :show="showTemplateModal"
            @close="showTemplateModal = false"
            @select-template="loadTemplate"
        />

        <!-- Settings Modal (optional - you can create this later) -->
        <div v-if="showSettingsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Canvas Width</label>
                        <input
                            type="number"
                            v-model="canvasSize.width"
                            class="w-full px-3 py-2 border rounded-md"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Canvas Height</label>
                        <input
                            type="number"
                            v-model="canvasSize.height"
                            class="w-full px-3 py-2 border rounded-md"
                        />
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button
                        @click="showSettingsModal = false"
                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-md"
                    >
                        Cancel
                    </button>
                    <button
                        @click="showSettingsModal = false"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Floating button to open templates -->
        <button
            @click="showTemplateModal = true"
            class="fixed bottom-6 left-6 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-colors"
            title="Open Templates"
        >
            <FileText class="w-5 h-5" />
        </button>
    </DragDropProvider>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjects } from '@/composables/useProjects'
import { useResponsive } from '@/composables/useResponsive'
import { FileText } from 'lucide-vue-next'

// Components
import DragDropProvider from './components/DragDropProvider.vue'
import Toolbar from './components/Toolbar.vue'
import ComponentSidebar from './components/ComponentSidebar.vue'
import ResponsiveCanvas from './components/ResponsiveCanvas.vue'
import LayersPanel from './components/LayersPanel.vue'
import ResponsivePropertiesPanel from './components/ResponsivePropertiesPanel.vue'
import TemplateLibrary from './components/templates/TemplateLibrary.vue'

// Composables
const route = useRoute()
const router = useRouter()
const { saveProject: apiSaveProject, fetchProject, exportProject: apiExportProject } = useProjects()
const { activeBreakpoint } = useResponsive()

// State
const selectedElement = ref(null)
const elements = ref([])
const projectId = ref(route.params.id || null)
const isSaving = ref(false)
const projectName = ref('My Website')
const showTemplateModal = ref(false)
const showSettingsModal = ref(false)

// UI State for responsive design
const showGrid = ref(true)
const showResponsiveGuides = ref(true)
const zoom = ref(1)
const canvasSize = ref({ width: 1200, height: 800 })

// History state for undo/redo
const history = ref([])
const historyIndex = ref(-1)
const canUndo = ref(false)
const canRedo = ref(false)

// Auto-save timer
let autoSaveTimeout = null

// Methods
const setSelectedElement = (id) => {
    selectedElement.value = id
}

const setElements = (newElements) => {
    console.log('📝 App.vue setElements called with:', newElements)

    // Add to history for undo/redo
    if (historyIndex.value < history.value.length - 1) {
        history.value = history.value.slice(0, historyIndex.value + 1)
    }

    history.value.push(JSON.parse(JSON.stringify(elements.value)))
    historyIndex.value = history.value.length - 1

    // Update undo/redo state
    canUndo.value = historyIndex.value > 0
    canRedo.value = historyIndex.value < history.value.length - 1

    elements.value = newElements
    scheduleAutoSave()
}

const scheduleAutoSave = () => {
    if (autoSaveTimeout) {
        clearTimeout(autoSaveTimeout)
    }
    autoSaveTimeout = setTimeout(() => {
        if (projectId.value) {
            saveProject()
        }
    }, 2000)
}

const saveProject = async () => {
    if (isSaving.value) return

    isSaving.value = true
    try {
        const projectData = {
            id: projectId.value,
            name: projectName.value,
            elements: elements.value,
            settings: {
                canvas: canvasSize.value,
                activeBreakpoint: activeBreakpoint.value,
                showGrid: showGrid.value,
                showResponsiveGuides: showResponsiveGuides.value
            }
        }

        const savedProject = await apiSaveProject(projectData)

        if (!projectId.value) {
            projectId.value = savedProject.id
            router.push({ name: 'editor', params: { id: savedProject.id } })
        }

        console.log('Project saved successfully')
    } catch (error) {
        console.error('Error saving project:', error)
    } finally {
        isSaving.value = false
    }
}

const loadProject = async () => {
    if (!projectId.value) return

    try {
        const project = await fetchProject(projectId.value)
        elements.value = project.elements || []
        projectName.value = project.name || 'My Website'

        // Load settings if they exist
        if (project.settings) {
            canvasSize.value = project.settings.canvas || { width: 1200, height: 800 }
            showGrid.value = project.settings.showGrid !== undefined ? project.settings.showGrid : true
            showResponsiveGuides.value = project.settings.showResponsiveGuides !== undefined ? project.settings.showResponsiveGuides : true
        }

        // Initialize history
        history.value = [JSON.parse(JSON.stringify(elements.value))]
        historyIndex.value = 0
        canUndo.value = false
        canRedo.value = false

    } catch (error) {
        console.error('Error loading project:', error)
    }
}

const loadTemplate = (template) => {
    elements.value = template.elements || []
    projectName.value = template.name || 'My Website'
    selectedElement.value = null

    // Reset history
    history.value = [JSON.parse(JSON.stringify(elements.value))]
    historyIndex.value = 0
    canUndo.value = false
    canRedo.value = false

    showTemplateModal.value = false
}

const previewProject = () => {
    if (projectId.value) {
        window.open(`http://localhost:8000/preview/${projectId.value}`, '_blank')
    }
}

const exportProject = async () => {
    if (!projectId.value) return

    try {
        const exported = await apiExportProject(projectId.value, 'html')

        // Download the file
        const blob = new Blob([exported.content], { type: 'text/html' })
        const url = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `${exported.filename}.html`
        a.click()
        window.URL.revokeObjectURL(url)
    } catch (error) {
        console.error('Error exporting project:', error)
    }
}

// History operations
const undo = () => {
    if (historyIndex.value > 0) {
        historyIndex.value--
        elements.value = JSON.parse(JSON.stringify(history.value[historyIndex.value]))
        canUndo.value = historyIndex.value > 0
        canRedo.value = true
        selectedElement.value = null
    }
}

const redo = () => {
    if (historyIndex.value < history.value.length - 1) {
        historyIndex.value++
        elements.value = JSON.parse(JSON.stringify(history.value[historyIndex.value]))
        canRedo.value = historyIndex.value < history.value.length - 1
        canUndo.value = true
        selectedElement.value = null
    }
}

// Zoom operations
const handleZoom = (action) => {
    switch (action) {
        case 'in':
            zoom.value = Math.min(2, zoom.value + 0.1)
            break
        case 'out':
            zoom.value = Math.max(0.25, zoom.value - 0.1)
            break
        case 'reset':
            zoom.value = 1
            break
    }
}

// Lifecycle
onMounted(() => {
    loadProject()
})

// Watch for route changes
watch(() => route.params.id, (newId) => {
    projectId.value = newId
    loadProject()
})
</script>
