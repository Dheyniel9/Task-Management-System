<!--
  This layout is used for the main app pages after login.
  It shows a navigation bar with links and user info, and displays the current page content.
-->
<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Left side: App name and navigation links -->
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-primary-600">
                Task Manager
              </h1>
            </div>
            <!-- Nav links -->
            <div class="hidden sm:ml-8 sm:flex sm:space-x-4">
              <router-link
                to="/tasks"
                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md hover:text-primary-600"
                :class="route.name === 'Tasks' ? 'text-primary-600 bg-primary-50' : 'text-gray-600'"
              >
                My Tasks
              </router-link>
              <router-link
                v-if="authStore.isAdmin"
                to="/admin"
                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md hover:text-primary-600"
                :class="route.name?.startsWith('Admin') ? 'text-primary-600 bg-primary-50' : 'text-gray-600'"
              >
                Admin Dashboard
              </router-link>
            </div>
          </div>
          <!-- Right side: User info and logout button -->
          <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-600">
              {{ authStore.user?.name }}
              <span v-if="authStore.isAdmin" class="ml-1 px-2 py-1 text-xs bg-primary-100 text-primary-700 rounded">
                Admin
              </span>
            </div>
            <button
              @click="handleLogout"
              class="text-sm text-gray-600 hover:text-gray-900"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>
    <!-- Main content -->
    <main class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
// This script sets up the logic for the layout, like getting user info and handling logout
import { useAuthStore } from '@/stores/auth'
import { useRoute } from 'vue-router'

const authStore = useAuthStore()
const route = useRoute()

async function handleLogout() {
  await authStore.logout()
}
</script>
