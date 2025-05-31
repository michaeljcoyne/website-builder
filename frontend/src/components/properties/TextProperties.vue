<!-- TextProperties.vue Component -->
<template>
    <div class="space-y-3">
        <label class="text-xs font-medium text-gray-700">Text Content</label>

        <div v-if="element.type === 'text' || element.type === 'heading'">
            <label class="text-xs">Content</label>
            <textarea
                :value="element.properties.content"
                @input="$emit('update', 'content', $event.target.value)"
                class="w-full h-20 px-2 py-1 border rounded text-sm resize-none"
            ></textarea>
        </div>

        <div>
            <label class="text-xs">Font Size</label>
            <div class="flex items-center gap-2">
                <input
                    type="range"
                    :value="element.properties.fontSize"
                    @input="$emit('update', 'fontSize', parseInt($event.target.value))"
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
                @input="$emit('update', 'color', $event.target.value)"
                class="w-full h-8 border rounded"
            />
        </div>

        <div>
            <label class="text-xs">Text Align</label>
            <select
                :value="element.properties.textAlign || 'left'"
                @change="$emit('update', 'textAlign', $event.target.value)"
                class="w-full h-8 px-2 border rounded text-sm"
            >
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
                <option value="justify">Justify</option>
            </select>
        </div>

        <div>
            <label class="text-xs">Font Weight</label>
            <select
                :value="element.properties.fontWeight || 'normal'"
                @change="$emit('update', 'fontWeight', $event.target.value)"
                class="w-full h-8 px-2 border rounded text-sm"
            >
                <option value="normal">Normal</option>
                <option value="bold">Bold</option>
                <option value="lighter">Light</option>
            </select>
        </div>

        <div v-if="element.type === 'heading'">
            <label class="text-xs">Heading Level</label>
            <select
                :value="element.properties.level"
                @change="$emit('update', 'level', parseInt($event.target.value))"
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
</template>

<script setup>
defineProps(['element'])
defineEmits(['update'])
</script>


