import { ref } from 'vue'
import api from '@/plugins/axios'

export function useProjects() {
    const projects = ref([])
    const currentProject = ref(null)
    const loading = ref(false)
    const error = ref(null)

    const fetchProjects = async () => {
        loading.value = true
        try {
            const response = await api.get('/projects')
            projects.value = response.data
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const fetchProject = async (id) => {
        loading.value = true
        try {
            const response = await api.get(`/projects/${id}`)
            currentProject.value = response.data
            return response.data
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const saveProject = async (projectData) => {
        loading.value = true
        try {
            let response
            if (projectData.id) {
                response = await api.put(`/projects/${projectData.id}`, projectData)
            } else {
                response = await api.post('/projects', projectData)
            }
            return response.data
        } catch (err) {
            error.value = err.message
            throw err
        } finally {
            loading.value = false
        }
    }

    const exportProject = async (projectId, format = 'html') => {
        try {
            const response = await api.post(`/projects/${projectId}/export`, { format })
            return response.data
        } catch (err) {
            error.value = err.message
            throw err
        }
    }

    return {
        projects,
        currentProject,
        loading,
        error,
        fetchProjects,
        fetchProject,
        saveProject,
        exportProject
    }
}
