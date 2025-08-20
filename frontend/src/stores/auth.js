// src/stores/auth.js
// This file manages user authentication (login, register, logout) and stores user info for the app.
// It uses Pinia for state management and talks to the backend API for auth actions.

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
  // State: holds user info, token, loading status, and error messages
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const loading = ref(false)
  const error = ref(null)

  // Getters: quick ways to check if logged in or if user is admin
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.is_admin || false)

  // Register a new user
  async function register(userData) {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/auth/register', userData)
      const { user: newUser, token: newToken } = response.data
      // Save token and user info
      token.value = newToken
      user.value = newUser
      localStorage.setItem('token', newToken)
      // Set token for future API calls
      api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
      // Go to tasks page after signup
      await router.push('/tasks')
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  // Log in an existing user
  async function login(credentials) {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/auth/login', credentials)
      const { user: newUser, token: newToken } = response.data
      // Save token and user info
      token.value = newToken
      user.value = newUser
      localStorage.setItem('token', newToken)
      // Set token for future API calls
      api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
      // Go to admin or tasks page based on user role
      if (newUser.is_admin) {
        await router.push('/admin')
      } else {
        await router.push('/tasks')
      }
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Invalid credentials'
      throw err
    } finally {
      loading.value = false
    }
  }

  // Log out the user
  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      // Always clear user info and token
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      delete api.defaults.headers.common['Authorization']
      // Go to login page
      await router.push('/login')
    }
  }

  // Get the current user's info from the server
  async function fetchUser() {
    if (!token.value) return
    try {
      const response = await api.get('/auth/user')
      user.value = response.data.user
    } catch (err) {
      // If token is bad, log out
      if (err.response?.status === 401) {
        await logout()
      }
    }
  }

  // If there's a token, set it for API and get user info on app start
  if (token.value) {
    api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    fetchUser()
  }

  return {
    // State
    user,
    token,
    loading,
    error,
    // Getters
    isAuthenticated,
    isAdmin,
    // Actions
    register,
    login,
    logout,
    fetchUser,
  }
})
