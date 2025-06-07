import { ref } from 'vue'

export function useHistory(elements) {
    const history = ref([])
    const currentIndex = ref(-1)
    const maxHistory = 50

    const addState = (state) => {
        // Remove any future states if we're not at the end
        if (currentIndex.value < history.value.length - 1) {
            history.value = history.value.slice(0, currentIndex.value + 1)
        }

        // Add new state
        history.value.push(JSON.parse(JSON.stringify(state)))
        currentIndex.value = history.value.length - 1

        // Limit history size
        if (history.value.length > maxHistory) {
            history.value.shift()
            currentIndex.value--
        }
    }

    const undo = () => {
        if (currentIndex.value > 0) {
            currentIndex.value--
            return JSON.parse(JSON.stringify(history.value[currentIndex.value]))
        }
        return null
    }

    const redo = () => {
        if (currentIndex.value < history.value.length - 1) {
            currentIndex.value++
            return JSON.parse(JSON.stringify(history.value[currentIndex.value]))
        }
        return null
    }

    const clear = () => {
        history.value = []
        currentIndex.value = -1
    }

    const canUndo = () => currentIndex.value > 0
    const canRedo = () => currentIndex.value < history.value.length - 1

    return {
        addState,
        undo,
        redo,
        clear,
        canUndo: canUndo(),
        canRedo: canRedo()
    }
}
