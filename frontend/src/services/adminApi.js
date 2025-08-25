// src/services/adminApi.js - Create this new file
// This uses your existing API setup and extends it with admin functionality

import api from '@/services/api' // Import your existing API service

export const adminApi = {
  // Get all users with their task counts
  async getUsers(params = {}) {
    try {
      const response = await api.get('/admin/users', { params })
      return response
    } catch (error) {
      console.error('Error fetching users:', error)
      throw error
    }
  },

  // Get all tasks with filtering options
  async getAllTasks(filters = {}) {
    try {
      const response = await api.get('/admin/tasks', { params: filters })
      return response
    } catch (error) {
      console.error('Error fetching tasks:', error)
      throw error
    }
  },

  // Get statistics for a specific user
  async getUserStatistics(userId) {
    try {
      const response = await api.get(`/admin/users/${userId}/statistics`)
      return response
    } catch (error) {
      console.error('Error fetching user statistics:', error)
      throw error
    }
  },

  // Get system-wide statistics
  async getSystemStatistics() {
    try {
      const response = await api.get('/admin/statistics')
      return response
    } catch (error) {
      console.error('Error fetching system statistics:', error)
      throw error
    }
  },

  // Get specific task details
  async getTaskDetails(taskId) {
    try {
      const response = await api.get(`/admin/tasks/${taskId}`)
      return response
    } catch (error) {
      console.error('Error fetching task details:', error)
      throw error
    }
  },

  // Optional: Update task status (if admin needs this capability)
  async updateTaskStatus(taskId, status) {
    try {
      const response = await api.patch(`/admin/tasks/${taskId}/status`, { status })
      return response
    } catch (error) {
      console.error('Error updating task status:', error)
      throw error
    }
  },

  // Optional: Delete a task (if admin needs this capability)
  async deleteTask(taskId) {
    try {
      const response = await api.delete(`/admin/tasks/${taskId}`)
      return response
    } catch (error) {
      console.error('Error deleting task:', error)
      throw error
    }
  },

  // Get user details
  async getUserDetails(userId) {
    try {
      const response = await api.get(`/admin/users/${userId}`)
      return response
    } catch (error) {
      console.error('Error fetching user details:', error)
      throw error
    }
  },

  // Optional: Update user details (if admin needs this capability)
  async updateUser(userId, userData) {
    try {
      const response = await api.put(`/admin/users/${userId}`, userData)
      return response
    } catch (error) {
      console.error('Error updating user:', error)
      throw error
    }
  },

  // Optional: Delete user (if admin needs this capability)
  async deleteUser(userId) {
    try {
      const response = await api.delete(`/admin/users/${userId}`)
      return response
    } catch (error) {
      console.error('Error deleting user:', error)
      throw error
    }
  },

  async createUser(userData) {
    try {
      const response = await api.post('/admin/users', userData)
      return response
    } catch (error) {
      console.error('Error creating user:', error)
      throw error
    }
  },

  // Update an existing user
  async updateUser(userId, userData) {
    try {
      const response = await api.put(`/admin/users/${userId}`, userData)
      return response
    } catch (error) {
      console.error('Error updating user:', error)
      throw error
    }
  },

  // Delete a user
  async deleteUser(userId) {
    try {
      const response = await api.delete(`/admin/users/${userId}`)
      return response
    } catch (error) {
      console.error('Error deleting user:', error)
      throw error
    }
  },

  // Get detailed user information
  async getUserDetails(userId) {
    try {
      const response = await api.get(`/admin/users/${userId}`)
      return response
    } catch (error) {
      console.error('Error fetching user details:', error)
      throw error
    }
  },

  // Update task status (for drag-and-drop functionality)
  async updateTaskStatus(taskId, status) {
    try {
      const response = await api.patch(`/admin/tasks/${taskId}/status`, { status })
      return response
    } catch (error) {
      console.error('Error updating task status:', error)
      throw error
    }
  },

  // Bulk operations
  async bulkDeleteUsers(userIds) {
    try {
      const response = await api.post('/admin/users/bulk-delete', { user_ids: userIds })
      return response
    } catch (error) {
      console.error('Error bulk deleting users:', error)
      throw error
    }
  },

  // Export users data
  async exportUsers(format = 'csv') {
    try {
      const response = await api.get(`/admin/users/export?format=${format}`, {
        responseType: 'blob'
      })
      return response
    } catch (error) {
      console.error('Error exporting users:', error)
      throw error
    }
  }
}
