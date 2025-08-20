// src/stores/tasks.js
// This file manages all the tasks in the app: getting, creating, updating, deleting, and filtering tasks.
// It uses Pinia for state management and talks to the backend API for all task actions.

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useTaskStore = defineStore('tasks', () => {
  // State: holds the list of tasks, loading/error status, filters, and statistics
  const tasks = ref([])
  const loading = ref(false)
  const error = ref(null)
  const filters = ref({
    status: '',
    priority: '',
    search: '',
  })
  const statistics = ref({
    total: 0,
    completed: 0,
    pending: 0,
    by_priority: {
      low: 0,
      medium: 0,
      high: 0,
    },
  })

  // Getters: filter and sort tasks, and get pending/completed lists
  const filteredTasks = computed(() => {
    let result = [...tasks.value]
    if (filters.value.status) {
      result = result.filter(task => task.status === filters.value.status)
    }
    if (filters.value.priority) {
      result = result.filter(task => task.priority === filters.value.priority)
    }
    if (filters.value.search) {
      const searchLower = filters.value.search.toLowerCase()
      result = result.filter(task =>
        task.title.toLowerCase().includes(searchLower) ||
        task.description?.toLowerCase().includes(searchLower)
      )
    }
    return result.sort((a, b) => a.order - b.order)
  })

  const pendingTasks = computed(() =>
    tasks.value.filter(task => task.status === 'pending')
  )

  const completedTasks = computed(() =>
    tasks.value.filter(task => task.status === 'completed')
  )

  // Actions: fetch, create, update, delete, reorder tasks, and get stats
  async function fetchTasks() {
    loading.value = true
    error.value = null
    try {
      const params = {}
      if (filters.value.status) params.status = filters.value.status
      if (filters.value.priority) params.priority = filters.value.priority
      if (filters.value.search) params.search = filters.value.search
      const response = await api.get('/tasks', { params })
      tasks.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch tasks'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function createTask(taskData) {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/tasks', taskData)
      const newTask = response.data.data
      // Add new task to the list
      tasks.value.push(newTask)
      // Update stats after adding
      await fetchStatistics()
      return newTask
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create task'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateTask(taskId, updates) {
    loading.value = true
    error.value = null
    try {
      const response = await api.put(`/tasks/${taskId}`, updates)
      const updatedTask = response.data.data
      // Update the task in the list
      const index = tasks.value.findIndex(t => t.id === taskId)
      if (index !== -1) {
        tasks.value[index] = updatedTask
      }
      // Update stats if status changed
      if (updates.status) {
        await fetchStatistics()
      }
      return updatedTask
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update task'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function deleteTask(taskId) {
    loading.value = true
    error.value = null
    try {
      await api.delete(`/tasks/${taskId}`)
      // Remove the task from the list
      tasks.value = tasks.value.filter(t => t.id !== taskId)
      // Update stats after deleting
      await fetchStatistics()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete task'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function reorderTasks(taskOrders) {
    try {
      await api.post('/tasks/reorder', { tasks: taskOrders })
      // Update the order in the local list
      tasks.value.forEach(task => {
        if (taskOrders[task.id] !== undefined) {
          task.order = taskOrders[task.id]
        }
      })
      // Sort tasks by new order
      tasks.value.sort((a, b) => a.order - b.order)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to reorder tasks'
      throw err
    }
  }

  async function fetchStatistics() {
    try {
      const response = await api.get('/tasks/statistics')
      statistics.value = response.data.data
    } catch (err) {
      console.error('Failed to fetch statistics:', err)
    }
  }

  // Helpers to set and clear filters
  function setFilter(filterName, value) {
    filters.value[filterName] = value
  }

  function clearFilters() {
    filters.value = {
      status: '',
      priority: '',
      search: '',
    }
  }

  // WebSocket event handlers: update tasks in real-time
  function handleTaskCreated(task) {
    tasks.value.push(task)
    fetchStatistics()
  }

  function handleTaskUpdated(task) {
    const index = tasks.value.findIndex(t => t.id === task.id)
    if (index !== -1) {
      tasks.value[index] = task
    }
    fetchStatistics()
  }

  function handleTaskDeleted(taskId) {
    tasks.value = tasks.value.filter(t => t.id !== taskId)
    fetchStatistics()
  }

  function handleTasksReordered(taskOrders) {
    tasks.value.forEach(task => {
      if (taskOrders[task.id] !== undefined) {
        task.order = taskOrders[task.id]
      }
    })
    tasks.value.sort((a, b) => a.order - b.order)
  }

  return {
    // State
    tasks,
    loading,
    error,
    filters,
    statistics,
    // Getters
    filteredTasks,
    pendingTasks,
    completedTasks,
    // Actions
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
    reorderTasks,
    fetchStatistics,
    setFilter,
    clearFilters,
    // WebSocket handlers
    handleTaskCreated,
    handleTaskUpdated,
    handleTaskDeleted,
    handleTasksReordered,
  }
})
