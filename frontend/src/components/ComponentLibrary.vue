<!-- COMPLETE ComponentLibrary.vue -->
<template>
    <div class="w-64 border-r bg-white flex flex-col">
        <!-- Header with Tabs -->
        <div class="p-4 border-b">
            <h2 class="font-semibold text-sm mb-3">Component Library</h2>
            <div class="flex rounded-lg bg-gray-100 p-1">
                <button
                    @click="activeTab = 'default'"
                    :class="[
            'flex-1 px-3 py-1 text-xs rounded-md transition-colors',
            activeTab === 'default'
              ? 'bg-white text-gray-900 shadow-sm'
              : 'text-gray-600 hover:text-gray-900'
          ]"
                >
                    Default
                </button>
                <button
                    @click="activeTab = 'custom'"
                    :class="[
            'flex-1 px-3 py-1 text-xs rounded-md transition-colors',
            activeTab === 'custom'
              ? 'bg-white text-gray-900 shadow-sm'
              : 'text-gray-600 hover:text-gray-900'
          ]"
                >
                    Custom
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="p-4 border-b">
            <input
                v-model="searchTerm"
                placeholder="Search components..."
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
        </div>

        <!-- Default Components Tab -->
        <div v-if="activeTab === 'default'" class="flex-1 overflow-y-auto">
            <div class="p-2">
                <div v-for="category in filteredDefaultCategories" :key="category.name" class="mb-4">
                    <button
                        @click="toggleCategory(category.name)"
                        class="w-full flex items-center justify-between text-xs font-medium text-gray-500 mb-2 px-2 py-1 hover:bg-gray-100 rounded"
                    >
                        <span>{{ category.name }}</span>
                        <ChevronDown
                            :class="['w-3 h-3 transition-transform', expandedCategories.includes(category.name) ? 'rotate-180' : '']"
                        />
                    </button>

                    <div v-show="expandedCategories.includes(category.name)" class="space-y-1">
                        <div
                            v-for="component in category.components"
                            :key="component.id"
                            draggable="true"
                            @dragstart="handleDragStart(component)"
                            @dragend="handleDragEnd"
                            class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-100 cursor-grab active:cursor-grabbing text-sm transition-colors group"
                        >
                            <component :is="component.icon" class="h-4 w-4 text-gray-600 flex-shrink-0" />
                            <span class="truncate flex-1">{{ component.name }}</span>

                            <!-- Save as Custom Component -->
                            <button
                                @click.stop="saveAsCustomComponent(component)"
                                class="opacity-0 group-hover:opacity-100 p-1 hover:bg-gray-200 rounded transition-opacity"
                                title="Save as custom component"
                            >
                                <Bookmark class="w-3 h-3 text-gray-500" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Components Tab -->
        <div v-if="activeTab === 'custom'" class="flex-1 overflow-y-auto">
            <div class="p-2">
                <!-- No custom components message -->
                <div v-if="filteredCustomComponents.length === 0" class="text-center py-8">
                    <Package class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                    <p class="text-sm text-gray-500 mb-4">No custom components yet</p>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Select an element on the canvas and click "Save as Component" to create reusable components.
                    </p>
                </div>

                <!-- Custom Components List -->
                <div v-else class="space-y-2">
                    <div
                        v-for="component in filteredCustomComponents"
                        :key="component.id"
                        class="group"
                    >
                        <!-- Component Item -->
                        <div
                            draggable="true"
                            @dragstart="handleDragStart(component)"
                            @dragend="handleDragEnd"
                            class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-100 cursor-grab active:cursor-grabbing text-sm transition-colors"
                        >
                            <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                                <component :is="getComponentIcon(component.elementType)" class="w-4 h-4 text-gray-600" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="font-medium truncate">{{ component.name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ component.description }}</div>
                            </div>

                            <!-- Component Actions -->
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    @click.stop="editComponent(component)"
                                    class="p-1 hover:bg-gray-200 rounded"
                                    title="Edit component"
                                >
                                    <Edit3 class="w-3 h-3 text-gray-500" />
                                </button>
                                <button
                                    @click.stop="duplicateComponent(component)"
                                    class="p-1 hover:bg-gray-200 rounded"
                                    title="Duplicate component"
                                >
                                    <Copy class="w-3 h-3 text-gray-500" />
                                </button>
                                <button
                                    @click.stop="deleteComponent(component.id)"
                                    class="p-1 hover:bg-gray-200 rounded"
                                    title="Delete component"
                                >
                                    <Trash2 class="w-3 h-3 text-red-500" />
                                </button>
                            </div>
                        </div>

                        <!-- Component Preview -->
                        <div
                            v-if="component.preview"
                            class="ml-10 mb-2 opacity-0 group-hover:opacity-100 transition-opacity"
                        >
                            <div class="bg-gray-50 rounded p-2 text-xs">
                                <div class="text-gray-600 mb-1">Preview:</div>
                                <div class="bg-white rounded border p-2 max-h-20 overflow-hidden">
                                    {{ component.preview }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Footer -->
        <div class="border-t p-4 bg-gray-50">
            <div v-if="activeTab === 'custom'" class="space-y-2">
                <button
                    @click="saveSelectedAsComponent"
                    :disabled="!selectedElement"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                >
                    <Plus class="w-4 h-4" />
                    Save as Component
                </button>

                <div class="flex gap-2">
                    <button
                        @click="importComponents"
                        class="flex-1 px-3 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100 transition-colors"
                    >
                        Import
                    </button>
                    <button
                        @click="exportComponents"
                        :disabled="customComponents.length === 0"
                        class="flex-1 px-3 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        Export
                    </button>
                </div>
            </div>

            <div v-else class="text-xs text-gray-500 text-center">
                Drag components to the canvas to use them
            </div>
        </div>
    </div>

    <!-- Save Component Modal -->
    <div v-if="showSaveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold mb-4">Save as Component</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Component Name</label>
                    <input
                        v-model="componentForm.name"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter component name"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea
                        v-model="componentForm.description"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        rows="2"
                        placeholder="Describe your component"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select
                        v-model="componentForm.category"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="custom">Custom</option>
                        <option value="ui">UI Elements</option>
                        <option value="content">Content</option>
                        <option value="navigation">Navigation</option>
                        <option value="forms">Forms</option>
                    </select>
                </div>

                <!-- Component Preview -->
                <div v-if="selectedElementData" class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm font-medium text-gray-700 mb-2">Preview:</div>
                    <div class="bg-white rounded border p-2 text-xs">
                        <div><strong>Type:</strong> {{ selectedElementData.type }}</div>
                        <div><strong>Size:</strong> {{ selectedElementData.width }}×{{ selectedElementData.height }}</div>
                        <div v-if="selectedElementData.properties?.content">
                            <strong>Content:</strong> {{ selectedElementData.properties.content.slice(0, 50) }}...
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button
                    @click="showSaveModal = false"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="confirmSaveComponent"
                    :disabled="!componentForm.name"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    Save Component
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Component Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold mb-4">Edit Component</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Component Name</label>
                    <input
                        v-model="editForm.name"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea
                        v-model="editForm.description"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        rows="2"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select
                        v-model="editForm.category"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="custom">Custom</option>
                        <option value="ui">UI Elements</option>
                        <option value="content">Content</option>
                        <option value="navigation">Navigation</option>
                        <option value="forms">Forms</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button
                    @click="showEditModal = false"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="confirmEditComponent"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Update Component
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, inject, onMounted } from 'vue'
import {
    ChevronDown, Bookmark, Package, Plus, Edit3, Copy, Trash2,
    Type, Square, ImageIcon, Layout, Grid3X3, Navigation, Video,
    Mail, Users, ShoppingCart, BarChart, List, Play, Camera, Star
} from 'lucide-vue-next'

const props = defineProps({
    selectedElement: String,
    elements: Array
})

const emit = defineEmits(['save-component', 'load-component'])

const { setDraggedItem, setIsDragging } = inject('dragDrop')

// State
const activeTab = ref('default')
const searchTerm = ref('')
const expandedCategories = ref(['Basic', 'Layout'])
const showSaveModal = ref(false)
const showEditModal = ref(false)
const customComponents = ref([])
const editingComponent = ref(null)

// Forms
const componentForm = ref({
    name: '',
    description: '',
    category: 'custom'
})

const editForm = ref({
    name: '',
    description: '',
    category: 'custom'
})

// Default component categories
const defaultComponentCategories = [
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
            { id: "section", name: "Section", icon: Layout, type: "section" },
            { id: "card", name: "Card", icon: Square, type: "card" },
            { id: "hero", name: "Hero Section", icon: Layout, type: "hero" },
        ],
    },
    {
        name: "Forms",
        components: [
            { id: "input", name: "Text Input", icon: Square, type: "input" },
            { id: "textarea", name: "Textarea", icon: Square, type: "textarea" },
            { id: "select", name: "Select", icon: List, type: "select" },
            { id: "newsletter", name: "Newsletter", icon: Mail, type: "newsletter" },
        ],
    },
    {
        name: "Media",
        components: [
            { id: "video", name: "Video", icon: Video, type: "video" },
            { id: "gallery", name: "Gallery", icon: Camera, type: "gallery" },
            { id: "carousel", name: "Carousel", icon: ImageIcon, type: "carousel" },
        ],
    },
    {
        name: "E-commerce",
        components: [
            { id: "product-card", name: "Product Card", icon: ShoppingCart, type: "product-card" },
            { id: "price-table", name: "Price Table", icon: BarChart, type: "price-table" },
        ],
    }
]

// Computed
const selectedElementData = computed(() =>
    props.elements.find(el => el.id === props.selectedElement)
)

const filteredDefaultCategories = computed(() => {
    if (!searchTerm.value) return defaultComponentCategories

    return defaultComponentCategories.map(category => ({
        ...category,
        components: category.components.filter(component =>
            component.name.toLowerCase().includes(searchTerm.value.toLowerCase())
        )
    })).filter(category => category.components.length > 0)
})

const filteredCustomComponents = computed(() => {
    if (!searchTerm.value) return customComponents.value

    return customComponents.value.filter(component =>
        component.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        component.description.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
})

// Methods
const toggleCategory = (categoryName) => {
    const index = expandedCategories.value.indexOf(categoryName)
    if (index > -1) {
        expandedCategories.value.splice(index, 1)
    } else {
        expandedCategories.value.push(categoryName)
    }
}

const handleDragStart = (component) => {
    setDraggedItem(component)
    setIsDragging(true)
}

const handleDragEnd = () => {
    setDraggedItem(null)
    setIsDragging(false)
}

const getComponentIcon = (elementType) => {
    const iconMap = {
        'text': Type,
        'heading': Type,
        'button': Square,
        'image': ImageIcon,
        'container': Layout,
        'section': Layout,
        'card': Square,
        'hero': Layout,
        'input': Square,
        'textarea': Square,
        'select': List,
        'newsletter': Mail,
        'video': Video,
        'gallery': Camera,
        'product-card': ShoppingCart,
        'price-table': BarChart,
    }
    return iconMap[elementType] || Square
}

const saveAsCustomComponent = (component) => {
    componentForm.value = {
        name: component.name + ' Custom',
        description: `Custom version of ${component.name}`,
        category: 'custom'
    }
    showSaveModal.value = true
}

const saveSelectedAsComponent = () => {
    if (!selectedElementData.value) return

    componentForm.value = {
        name: `${selectedElementData.value.name} Component`,
        description: `Custom ${selectedElementData.value.type} component`,
        category: 'custom'
    }
    showSaveModal.value = true
}

const confirmSaveComponent = () => {
    if (!componentForm.value.name || !selectedElementData.value) return

    const newComponent = {
        id: `custom-${Date.now()}`,
        name: componentForm.value.name,
        description: componentForm.value.description,
        category: componentForm.value.category,
        elementType: selectedElementData.value.type,
        type: 'custom-component',
        isCustom: true,
        createdAt: new Date().toISOString(),
        elementData: {
            ...selectedElementData.value,
            id: `${selectedElementData.value.type}-${Date.now()}`,
            x: 0,
            y: 0
        },
        preview: generateComponentPreview(selectedElementData.value)
    }

    customComponents.value.push(newComponent)
    saveCustomComponents()

    showSaveModal.value = false
    componentForm.value = { name: '', description: '', category: 'custom' }

    // Emit to parent
    emit('save-component', newComponent)
}

const editComponent = (component) => {
    editingComponent.value = component
    editForm.value = {
        name: component.name,
        description: component.description,
        category: component.category
    }
    showEditModal.value = true
}

const confirmEditComponent = () => {
    if (!editingComponent.value) return

    const index = customComponents.value.findIndex(c => c.id === editingComponent.value.id)
    if (index > -1) {
        customComponents.value[index] = {
            ...customComponents.value[index],
            name: editForm.value.name,
            description: editForm.value.description,
            category: editForm.value.category,
            updatedAt: new Date().toISOString()
        }
        saveCustomComponents()
    }

    showEditModal.value = false
    editingComponent.value = null
}

const duplicateComponent = (component) => {
    const duplicate = {
        ...component,
        id: `custom-${Date.now()}`,
        name: `${component.name} Copy`,
        createdAt: new Date().toISOString()
    }

    customComponents.value.push(duplicate)
    saveCustomComponents()
}

const deleteComponent = (componentId) => {
    if (confirm('Are you sure you want to delete this component?')) {
        customComponents.value = customComponents.value.filter(c => c.id !== componentId)
        saveCustomComponents()
    }
}

const generateComponentPreview = (element) => {
    let preview = `${element.type}`
    if (element.properties?.content) {
        preview += `: ${element.properties.content.slice(0, 30)}`
    } else if (element.properties?.text) {
        preview += `: ${element.properties.text}`
    } else if (element.properties?.title) {
        preview += `: ${element.properties.title}`
    }
    return preview
}

const saveCustomComponents = () => {
    localStorage.setItem('customComponents', JSON.stringify(customComponents.value))
}

const loadCustomComponents = () => {
    const saved = localStorage.getItem('customComponents')
    if (saved) {
        customComponents.value = JSON.parse(saved)
    }
}

const exportComponents = () => {
    const exportData = {
        components: customComponents.value,
        exportedAt: new Date().toISOString(),
        version: '1.0'
    }

    const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'custom-components.json'
    a.click()
    URL.revokeObjectURL(url)
}

const importComponents = () => {
    const input = document.createElement('input')
    input.type = 'file'
    input.accept = '.json'
    input.onchange = (e) => {
        const file = e.target.files[0]
        if (!file) return

        const reader = new FileReader()
        reader.onload = (e) => {
            try {
                const data = JSON.parse(e.target.result)
                if (data.components && Array.isArray(data.components)) {
                    const importedComponents = data.components.map(comp => ({
                        ...comp,
                        id: `imported-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
                        importedAt: new Date().toISOString()
                    }))

                    customComponents.value.push(...importedComponents)
                    saveCustomComponents()

                    alert(`Successfully imported ${importedComponents.length} components!`)
                }
            } catch (error) {
                alert('Error importing components. Please check the file format.')
            }
        }
        reader.readAsText(file)
    }
    input.click()
}

// Initialize
onMounted(() => {
    loadCustomComponents()
})
</script>
