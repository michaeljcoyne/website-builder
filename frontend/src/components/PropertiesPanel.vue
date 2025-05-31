<template>
    <div class="flex-1">
        <div class="p-3 border-b">
            <h3 class="font-semibold text-sm">Properties</h3>
            <p v-if="element" class="text-xs text-gray-500">{{ element.name }} ({{ element.type }})</p>
        </div>

        <div v-if="!element" class="p-4 text-center text-gray-400 text-sm">
            Select an element to edit its properties
        </div>

        <div v-else class="flex-1 overflow-y-auto">
            <div class="p-4 space-y-4">

                <!-- Position & Size -->
                <div class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Position & Size</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs">X</label>
                            <input
                                type="number"
                                :value="element.x"
                                @input="updatePosition('x', parseInt($event.target.value) || 0)"
                                class="w-full h-8 px-2 border rounded text-sm"
                            />
                        </div>
                        <div>
                            <label class="text-xs">Y</label>
                            <input
                                type="number"
                                :value="element.y"
                                @input="updatePosition('y', parseInt($event.target.value) || 0)"
                                class="w-full h-8 px-2 border rounded text-sm"
                            />
                        </div>
                        <div>
                            <label class="text-xs">Width</label>
                            <input
                                type="number"
                                :value="element.width"
                                @input="updatePosition('width', parseInt($event.target.value) || 0)"
                                class="w-full h-8 px-2 border rounded text-sm"
                            />
                        </div>
                        <div>
                            <label class="text-xs">Height</label>
                            <input
                                type="number"
                                :value="element.height"
                                @input="updatePosition('height', parseInt($event.target.value) || 0)"
                                class="w-full h-8 px-2 border rounded text-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- Text/Heading Properties -->
                <div v-if="element.type === 'text' || element.type === 'heading'" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Text Settings</label>
                    <div>
                        <label class="text-xs">Content</label>
                        <textarea
                            :value="element.properties.content"
                            @input="updateProperty('content', $event.target.value)"
                            class="w-full h-20 px-2 py-1 border rounded text-sm resize-none"
                        ></textarea>
                    </div>
                    <div>
                        <label class="text-xs">Font Size</label>
                        <div class="flex items-center gap-2">
                            <input
                                type="range"
                                :value="element.properties.fontSize"
                                @input="updateProperty('fontSize', parseInt($event.target.value))"
                                min="8"
                                max="72"
                                class="flex-1"
                            />
                            <span class="text-xs w-8">{{ element.properties.fontSize }}px</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs">Color</label>
                        <input
                            type="color"
                            :value="element.properties.color"
                            @input="updateProperty('color', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Text Align</label>
                        <select
                            :value="element.properties.textAlign || 'left'"
                            @change="updateProperty('textAlign', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        >
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                            <option value="justify">Justify</option>
                        </select>
                    </div>
                    <div v-if="element.type === 'heading'">
                        <label class="text-xs">Heading Level</label>
                        <select
                            :value="element.properties.level"
                            @change="updateProperty('level', parseInt($event.target.value))"
                            class="w-full h-8 px-2 border rounded text-sm"
                        >
                            <option value="1">H1</option>
                            <option value="2">H2</option>
                            <option value="3">H3</option>
                            <option value="4">H4</option>
                            <option value="5">H5</option>
                            <option value="6">H6</option>
                        </select>
                    </div>
                </div>

                <!-- Button Properties -->
                <div v-if="element.type === 'button'" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Button Settings</label>
                    <div>
                        <label class="text-xs">Text</label>
                        <input
                            type="text"
                            :value="element.properties.text"
                            @input="updateProperty('text', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Background Color</label>
                        <input
                            type="color"
                            :value="element.properties.backgroundColor"
                            @input="updateProperty('backgroundColor', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Text Color</label>
                        <input
                            type="color"
                            :value="element.properties.textColor"
                            @input="updateProperty('textColor', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Border Radius</label>
                        <div class="flex items-center gap-2">
                            <input
                                type="range"
                                :value="parseInt(element.properties.borderRadius) || 4"
                                @input="updateProperty('borderRadius', $event.target.value + 'px')"
                                min="0"
                                max="50"
                                class="flex-1"
                            />
                            <span class="text-xs w-8">{{ parseInt(element.properties.borderRadius) || 4 }}px</span>
                        </div>
                    </div>
                </div>

                <!-- Image Properties -->
                <div v-if="element.type === 'image'" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Image Settings</label>
                    <div>
                        <label class="text-xs">Image URL</label>
                        <input
                            type="text"
                            :value="element.properties.src"
                            @input="updateProperty('src', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Alt Text</label>
                        <input
                            type="text"
                            :value="element.properties.alt"
                            @input="updateProperty('alt', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Object Fit</label>
                        <select
                            :value="element.properties.objectFit || 'cover'"
                            @change="updateProperty('objectFit', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        >
                            <option value="cover">Cover</option>
                            <option value="contain">Contain</option>
                            <option value="fill">Fill</option>
                            <option value="none">None</option>
                        </select>
                    </div>
                </div>

                <!-- Container/Section/Card Properties -->
                <div v-if="['container', 'section', 'card'].includes(element.type)" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Layout Settings</label>
                    <div>
                        <label class="text-xs">Background Color</label>
                        <input
                            type="color"
                            :value="element.properties.backgroundColor || '#ffffff'"
                            @input="updateProperty('backgroundColor', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Padding</label>
                        <input
                            type="text"
                            :value="element.properties.padding"
                            @input="updateProperty('padding', $event.target.value)"
                            placeholder="e.g., 20px"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Border Radius</label>
                        <div class="flex items-center gap-2">
                            <input
                                type="range"
                                :value="parseInt(element.properties.borderRadius) || 0"
                                @input="updateProperty('borderRadius', $event.target.value + 'px')"
                                min="0"
                                max="50"
                                class="flex-1"
                            />
                            <span class="text-xs w-8">{{ parseInt(element.properties.borderRadius) || 0 }}px</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Section Properties -->
                <div v-if="element.type === 'hero'" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Hero Settings</label>
                    <div>
                        <label class="text-xs">Title</label>
                        <input
                            type="text"
                            :value="element.properties.title"
                            @input="updateProperty('title', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Subtitle</label>
                        <textarea
                            :value="element.properties.subtitle"
                            @input="updateProperty('subtitle', $event.target.value)"
                            class="w-full h-16 px-2 py-1 border rounded text-sm resize-none"
                        ></textarea>
                    </div>
                    <div>
                        <label class="text-xs">Button Text</label>
                        <input
                            type="text"
                            :value="element.properties.buttonText"
                            @input="updateProperty('buttonText', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Background Color</label>
                        <input
                            type="color"
                            :value="element.properties.backgroundColor"
                            @input="updateProperty('backgroundColor', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                </div>

                <!-- Form Element Properties -->
                <div v-if="['input', 'textarea', 'select'].includes(element.type)" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Form Settings</label>
                    <div>
                        <label class="text-xs">Label</label>
                        <input
                            type="text"
                            :value="element.properties.label"
                            @input="updateProperty('label', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Placeholder</label>
                        <input
                            type="text"
                            :value="element.properties.placeholder"
                            @input="updateProperty('placeholder', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div v-if="element.type === 'input'">
                        <label class="text-xs">Input Type</label>
                        <select
                            :value="element.properties.type || 'text'"
                            @change="updateProperty('type', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        >
                            <option value="text">Text</option>
                            <option value="email">Email</option>
                            <option value="password">Password</option>
                            <option value="number">Number</option>
                            <option value="tel">Phone</option>
                        </select>
                    </div>
                </div>

                <!-- Newsletter Properties -->
                <div v-if="element.type === 'newsletter'" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Newsletter Settings</label>
                    <div>
                        <label class="text-xs">Title</label>
                        <input
                            type="text"
                            :value="element.properties.title"
                            @input="updateProperty('title', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Placeholder</label>
                        <input
                            type="text"
                            :value="element.properties.placeholder"
                            @input="updateProperty('placeholder', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Button Text</label>
                        <input
                            type="text"
                            :value="element.properties.buttonText"
                            @input="updateProperty('buttonText', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Button Color</label>
                        <input
                            type="color"
                            :value="element.properties.buttonColor"
                            @input="updateProperty('buttonColor', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                </div>

                <!-- Navigation Properties -->
                <div v-if="element.type === 'navbar'" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Navigation Settings</label>
                    <div>
                        <label class="text-xs">Brand Name</label>
                        <input
                            type="text"
                            :value="element.properties.brand"
                            @input="updateProperty('brand', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Links (comma separated)</label>
                        <input
                            type="text"
                            :value="(element.properties.links || []).join(', ')"
                            @input="updateProperty('links', $event.target.value.split(',').map(l => l.trim()))"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Background Color</label>
                        <input
                            type="color"
                            :value="element.properties.backgroundColor"
                            @input="updateProperty('backgroundColor', $event.target.value)"
                            class="w-full h-8 border rounded"
                        />
                    </div>
                </div>

                <!-- Testimonial Properties -->
                <div v-if="element.type === 'testimonial'" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Testimonial Settings</label>
                    <div>
                        <label class="text-xs">Quote</label>
                        <textarea
                            :value="element.properties.quote"
                            @input="updateProperty('quote', $event.target.value)"
                            class="w-full h-16 px-2 py-1 border rounded text-sm resize-none"
                        ></textarea>
                    </div>
                    <div>
                        <label class="text-xs">Author</label>
                        <input
                            type="text"
                            :value="element.properties.author"
                            @input="updateProperty('author', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Position</label>
                        <input
                            type="text"
                            :value="element.properties.position"
                            @input="updateProperty('position', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Avatar URL</label>
                        <input
                            type="text"
                            :value="element.properties.avatar"
                            @input="updateProperty('avatar', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                </div>

                <!-- Product Card Properties -->
                <div v-if="element.type === 'product-card'" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Product Settings</label>
                    <div>
                        <label class="text-xs">Product Name</label>
                        <input
                            type="text"
                            :value="element.properties.name"
                            @input="updateProperty('name', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Price</label>
                        <input
                            type="text"
                            :value="element.properties.price"
                            @input="updateProperty('price', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Image URL</label>
                        <input
                            type="text"
                            :value="element.properties.image"
                            @input="updateProperty('image', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Rating (1-5)</label>
                        <input
                            type="number"
                            :value="element.properties.rating"
                            @input="updateProperty('rating', parseInt($event.target.value))"
                            min="1"
                            max="5"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                </div>

                <!-- Counter Properties -->
                <div v-if="element.type === 'counter'" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">Counter Settings</label>
                    <div>
                        <label class="text-xs">Number</label>
                        <input
                            type="number"
                            :value="element.properties.number"
                            @input="updateProperty('number', parseInt($event.target.value))"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Label</label>
                        <input
                            type="text"
                            :value="element.properties.label"
                            @input="updateProperty('label', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Prefix</label>
                        <input
                            type="text"
                            :value="element.properties.prefix"
                            @input="updateProperty('prefix', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs">Suffix</label>
                        <input
                            type="text"
                            :value="element.properties.suffix"
                            @input="updateProperty('suffix', $event.target.value)"
                            class="w-full h-8 px-2 border rounded text-sm"
                        />
                    </div>
                </div>

                <!-- Generic Properties for other elements -->
                <div v-if="!['text', 'heading', 'button', 'image', 'container', 'section', 'card', 'hero', 'input', 'textarea', 'select', 'newsletter', 'navbar', 'testimonial', 'product-card', 'counter'].includes(element.type)" class="space-y-3">
                    <label class="text-xs font-medium text-gray-700">{{ element.type }} Settings</label>
                    <div class="text-xs text-gray-500">
                        Properties for {{ element.type }} coming soon...
                    </div>
                </div>

                <!-- Element State Controls -->
                <div class="space-y-3 border-t pt-4">
                    <label class="text-xs font-medium text-gray-700">Element State</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                :checked="!element.properties?.hidden"
                                @change="updateProperty('hidden', !$event.target.checked)"
                                class="rounded"
                            />
                            <Eye class="w-4 h-4" />
                            Visible
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                :checked="!element.properties?.locked"
                                @change="updateProperty('locked', !$event.target.checked)"
                                class="rounded"
                            />
                            <Unlock class="w-4 h-4" />
                            Unlocked
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2 border-t pt-4">
                    <button
                        @click="duplicateElement"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                    >
                        Duplicate Element
                    </button>
                    <button
                        @click="deleteElement"
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm"
                    >
                        Delete Element
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Eye, Unlock } from 'lucide-vue-next'

const props = defineProps({
    selectedElement: String,
    elements: Array,
})

const emit = defineEmits(['update:elements'])

const element = computed(() =>
    props.elements.find(el => el.id === props.selectedElement)
)

const updateProperty = (property, value) => {
    if (!element.value) return

    const updatedElements = props.elements.map(el =>
        el.id === props.selectedElement
            ? { ...el, properties: { ...el.properties, [property]: value } }
            : el
    )
    emit('update:elements', updatedElements)
}

const updatePosition = (property, value) => {
    if (!element.value) return

    const updatedElements = props.elements.map(el =>
        el.id === props.selectedElement ? { ...el, [property]: value } : el
    )
    emit('update:elements', updatedElements)
}

const duplicateElement = () => {
    if (!element.value) return

    const newElement = {
        ...element.value,
        id: `${element.value.type}-${Date.now()}`,
        x: element.value.x + 20,
        y: element.value.y + 20,
    }

    emit('update:elements', [...props.elements, newElement])
}

const deleteElement = () => {
    const updatedElements = props.elements.filter(el => el.id !== props.selectedElement)
    emit('update:elements', updatedElements)
}
</script>
