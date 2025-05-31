<!-- ButtonProperties.vue Component -->
<template>
    <div class="space-y-3">
        <label class="text-xs font-medium text-gray-700">Button Settings</label>

        <div>
            <label class="text-xs">Button Text</label>
            <input
                type="text"
                :value="element.properties.text"
                @input="$emit('update', 'text', $event.target.value)"
                class="w-full h-8 px-2 border rounded text-sm"
            />
        </div>

        <div>
            <label class="text-xs">Background Color</label>
            <input
                type="color"
                :value="element.properties.backgroundColor"
                @input="$emit('update', 'backgroundColor', $event.target.value)"
                class="w-full h-8 border rounded"
            />
        </div>

        <div>
            <label class="text-xs">Text Color</label>
            <input
                type="color"
                :value="element.properties.textColor"
                @input="$emit('update', 'textColor', $event.target.value)"
                class="w-full h-8 border rounded"
            />
        </div>

        <div>
            <label class="text-xs">Border Radius</label>
            <div class="flex items-center gap-2">
                <input
                    type="range"
                    :value="parseInt(element.properties.borderRadius) || 4"
                    @input="$emit('update', 'borderRadius', $event.target.value + 'px')"
                    min="0"
                    max="50"
                    class="flex-1"
                />
                <span class="text-xs w-8">{{ parseInt(element.properties.borderRadius) || 4 }}px</span>
            </div>
        </div>

        <div>
            <label class="text-xs">Font Size</label>
            <div class="flex items-center gap-2">
                <input
                    type="range"
                    :value="parseInt(element.properties.fontSize) || 16"
                    @input="$emit('update', 'fontSize', $event.target.value + 'px')"
                    min="10"
                    max="32"
                    class="flex-1"
                />
                <span class="text-xs w-8">{{ parseInt(element.properties.fontSize) || 16 }}px</span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps(['element'])
defineEmits(['update'])
</script>
