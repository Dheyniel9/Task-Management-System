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
                v-if="!authStore.isAdmin"
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
                :class="route.name?.startsWith('Admin') && !route.name?.includes('Site') ? 'text-primary-600 bg-primary-50' : 'text-gray-600'"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Task Dashboard
              </router-link>
              <router-link
                v-if="authStore.isAdmin"
                to="/admin/site"
                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md hover:text-primary-600"
                :class="route.name?.includes('Site') ? 'text-primary-600 bg-primary-50' : 'text-gray-600'"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                Site Administration
              </router-link>
            </div>
          </div>
          <!-- Right side: User info and logout button -->
          <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-600">
              {{ authStore.user?.name }}
              <!-- <span v-if="authStore.isAdmin" class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full font-medium">
                Admin
              </span> -->
            </div>
            <button
              @click="handleLogout"
              class="text-sm text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md hover:bg-gray-100 transition-colors"
            >
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
              Logout
            </button>
          </div>
        </div>
        
        <!-- Mobile menu button -->
        <div class="sm:hidden flex justify-between items-center py-2">
          <button
            @click="showMobileMenu = !showMobileMenu"
            class="text-gray-600 hover:text-gray-900 p-2"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
        </div>
        
        <!-- Mobile menu -->
        <div v-if="showMobileMenu" class="sm:hidden border-t border-gray-200 py-2">
          <router-link
            v-if="!authStore.isAdmin"
            to="/tasks"
            @click="showMobileMenu = false"
            class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 rounded-md"
            :class="route.name === 'Tasks' ? 'text-primary-600 bg-primary-50' : ''"
          >
            My Tasks
          </router-link>
          <router-link
            v-if="authStore.isAdmin"
            to="/admin"
            @click="showMobileMenu = false"
            class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 rounded-md"
            :class="route.name?.startsWith('Admin') && !route.name?.includes('Site') ? 'text-primary-600 bg-primary-50' : ''"
          >
            Task Dashboard
          </router-link>
          <router-link
            v-if="authStore.isAdmin"
            to="/admin/site"
            @click="showMobileMenu = false"
            class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 rounded-md"
            :class="route.name?.includes('Site') ? 'text-primary-600 bg-primary-50' : ''"
          >
            Site Administration
          </router-link>
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
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRoute } from 'vue-router'

const authStore = useAuthStore()
const route = useRoute()
const showMobileMenu = ref(false)

async function handleLogout() {
  await authStore.logout()
}
</script>