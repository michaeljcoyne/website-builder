<!-- ImageProperties.vue Component -->
<template>
    <div class="space-y-3">
        <label class="text-xs font-medium text-gray-700">Image Settings</label>

        <div>
            <label class="text-xs">Image URL</label>
            <input
                type="text"
                :value="element.properties.src"
                @input="$emit('update', 'src', $event.target.value)"
                class="w-full h-8 px-2 border rounded text-sm"
            />
        </div>

        <div>
            <label class="text-xs">Alt Text</label>
            <input
                type="text"
                :value="element.properties.alt"
                @input="$emit('update', 'alt', $event.target.value)"
                class="w-full h-8 px-2 border rounded text-sm"
            />
        </div>

        <div>
            <label class="text-xs">Object Fit</label>
            <select
                :value="element.properties.objectFit || 'cover'"
                @change="$emit('update', 'objectFit', $event.target.value)"
                class="w-full h-8 px-2 border rounded text-sm"
            >
                <option value="cover">Cover</option>
                <option value="contain">Contain</option>
                <option value="fill">Fill</option>
                <option value="none">None</option>
            </select>
        </div>

        <div>
            <label class="text-xs">Border Radius</label>
            <div class="flex items-center gap-2">
                <input
                    type="range"
                    :value="parseInt(element.properties.borderRadius) || 0"
                    @input="$emit('update', 'borderRadius', $event.target.value + 'px')"
                    min="0"
                    max="50"
                    class="flex-1"
                />
                <span class="text-xs w-8">{{ parseInt(element.properties.borderRadius) || 0 }}px</span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps(['element'])
defineEmits(['update'])
</script>
