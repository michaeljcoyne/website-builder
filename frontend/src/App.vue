<template>
    <DragDropProvider>
        <div class="h-screen flex flex-col bg-gray-50">
            <Toolbar
                @save="saveProject"
                @preview="previewProject"
                @export="exportProject"
                :is-saving="isSaving"
            />
            <div class="flex-1 flex overflow-hidden">
                <ComponentSidebar />
                <div class="flex-1 flex">
                    <Canvas
                        :elements="elements"
                        :selectedElement="selectedElement"
                        @update:elements="setElements"
                        @update:selectedElement="setSelectedElement"
                    />
                    <div class="w-80 border-l bg-white flex flex-col">
                        <LayersPanel
                            :elements="elements"
                            :selectedElement="selectedElement"
                            @update:selectedElement="setSelectedElement"
                        />
                        <PropertiesPanel
                            :selectedElement="selectedElement"
                            :elements="elements"
                            @update:elements="setElements"
                        />
                    </div>
                </div>
            </div>
        </div>
    </DragDropProvider>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjects } from '@/composables/useProjects'

// Components
import DragDropProvider from './components/DragDropProvider.vue'
import Toolbar from './components/Toolbar.vue'
import ComponentSidebar from './components/ComponentSidebar.vue'
import Canvas from './components/Canvas.vue'
import LayersPanel from './components/LayersPanel.vue'
import PropertiesPanel from './components/PropertiesPanel.vue'

// Composables
const route = useRoute()
const router = useRouter()
const { saveProject: apiSaveProject, fetchProject, exportProject: apiExportProject } = useProjects()

// State
const selectedElement = ref(null)
const elements = ref([])
const projectId = ref(route.params.id || null)
const isSaving = ref(false)
const projectName = ref('My Website')

// Auto-save timer
let autoSaveTimeout = null

// Methods
const setSelectedElement = (id) => {
    selectedElement.value = id
}

const setElements = (newElements) => {
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
                canvas: { width: 1200, height: 800 }
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
    } catch (error) {
        console.error('Error loading project:', error)
    }
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
