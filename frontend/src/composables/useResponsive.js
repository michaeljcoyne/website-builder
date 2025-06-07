import { ref, computed } from 'vue'

export function useResponsive() {
    const activeBreakpoint = ref('desktop')

    const breakpoints = [
        { id: 'mobile', name: 'Mobile', width: 375 },
        { id: 'tablet', name: 'Tablet', width: 768 },
        { id: 'desktop', name: 'Desktop', width: 1200 }
    ]

    const getCurrentBreakpoint = () => {
        return breakpoints.find(bp => bp.id === activeBreakpoint.value)
    }

    const setBreakpoint = (breakpointId) => {
        activeBreakpoint.value = breakpointId
    }

    return {
        activeBreakpoint,
        breakpoints,
        getCurrentBreakpoint,
        setBreakpoint
    }
}
