<template>
    <div class="h-14 border-b bg-white flex items-center px-4 gap-2">
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
        <!-- Rest of your toolbar content -->
    </div>
</template>

<script setup>
import { Save, Eye, Download, Undo, Redo, Monitor, Tablet, Smartphone, Settings } from 'lucide-vue-next'

defineProps({
    isSaving: {
        type: Boolean,
        default: false
    }
})

defineEmits(['save', 'preview', 'export'])
</script>
