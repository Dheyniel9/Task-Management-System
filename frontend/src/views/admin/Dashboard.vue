<!--
  Dashboard.vue
  This is the admin dashboard page. It shows system stats, charts, and recent tasks for admins.
  - The top section shows total users, tasks, pending tasks, and admin users.
  - The next section has charts for task status and priority distribution.
  - The bottom section lists recent tasks with details.
-->
<template>
  <div class="space-y-6">
    <!-- Header: shows the page title and a short description -->
    <div>
      <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
      <p class="mt-2 text-gray-600">System overview and statistics</p>
    </div>

    <!-- System Statistics: shows key numbers in cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Total Users -->
      <div class="card">
        <div class="flex items-center">
          <div class="flex-shrink-0 p-3 bg-primary-100 rounded-lg">
            <!-- User icon -->
            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-600">Total Users</p>
            <p class="text-2xl font-bold text-gray-900">{{ statistics.total_users }}</p>
          </div>
        </div>
      </div>

      <!-- Total Tasks -->
      <div class="card">
        <div class="flex items-center">
          <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
            <!-- Task icon -->
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-600">Total Tasks</p>
            <p class="text-2xl font-bold text-gray-900">{{ statistics.total_tasks }}</p>
          </div>
        </div>
      </div>

      <!-- Pending Tasks -->
      <div class="card">
        <div class="flex items-center">
          <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-lg">
            <!-- Clock icon -->
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-600">Pending Tasks</p>
            <p class="text-2xl font-bold text-gray-900">{{ statistics.tasks_by_status?.pending || 0 }}</p>
          </div>
        </div>
      </div>

      <!-- Admin Users -->
      <div class="card">
        <div class="flex items-center">
          <div class="flex-shrink-0 p-3 bg-purple-100 rounded-lg">
            <!-- Shield icon -->
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-600">Admin Users</p>
            <p class="text-2xl font-bold text-gray-900">{{ statistics.total_admins }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Distribution Charts: status and priority -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Status Distribution Chart -->
      <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tasks by Status</h2>
        <div class="space-y-2">
          <!-- Pending tasks bar -->
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Pending</span>
            <span class="text-sm font-medium">{{ statistics.tasks_by_status?.pending || 0 }}</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-yellow-600 h-2 rounded-full transition-all duration-300"
              :style="`width: ${getPendingPercentage()}%`"
            ></div>
          </div>
          <!-- Completed tasks bar -->
          <div class="flex items-center justify-between mt-4">
            <span class="text-sm text-gray-600">Completed</span>
            <span class="text-sm font-medium">{{ statistics.tasks_by_status?.completed || 0 }}</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-green-600 h-2 rounded-full transition-all duration-300"
              :style="`width: ${getCompletedPercentage()}%`"
            ></div>
          </div>
        </div>
      </div>

      <!-- Priority Distribution Chart -->
      <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tasks by Priority</h2>
        <div class="space-y-4">
          <div v-for="priority in ['high', 'medium', 'low']" :key="priority">
            <div class="flex items-center justify-between">
              <span class="text-sm capitalize" :class="getPriorityTextClass(priority)">
                {{ priority }} Priority
              </span>
              <span class="text-sm font-medium">
                {{ statistics.tasks_by_priority?.[priority] || 0 }}
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
              <div
                class="h-2 rounded-full transition-all duration-300"
                :class="getPriorityBarClass(priority)"
                :style="`width: ${getPriorityPercentage(priority)}%`"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Tasks Table -->
    <div class="card">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Recent Tasks</h2>
        <router-link to="/admin/users" class="text-sm text-primary-600 hover:text-primary-700">
          View All Users →
        </router-link>
      </div>
      <!-- Table of recent tasks -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Task
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                User
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Priority
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="task in statistics.recent_tasks" :key="task.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ task.title }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ task.user?.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="task.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                >
                  {{ task.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getPriorityClass(task.priority)"
                >
                  {{ task.priority }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(task.created_at) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
// Import Vue features and the API service
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'

// Holds all the statistics for the dashboard
const statistics = ref({
  total_users: 0,
  total_admins: 0,
  total_tasks: 0,
  tasks_by_status: { pending: 0, completed: 0 },
  tasks_by_priority: { low: 0, medium: 0, high: 0 },
  recent_tasks: [],
})

// When the page loads, fetch the statistics from the server
onMounted(async () => {
  await fetchStatistics()
})

// Fetches statistics from the backend API
async function fetchStatistics() {
  try {
    const response = await api.get('/admin/statistics')
    statistics.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch statistics:', error)
  }
}

// Calculate the percentage of pending tasks
function getPendingPercentage() {
  const total = statistics.value.total_tasks
  if (!total) return 0
  return Math.round((statistics.value.tasks_by_status?.pending / total) * 100)
}

// Calculate the percentage of completed tasks
function getCompletedPercentage() {
  const total = statistics.value.total_tasks
  if (!total) return 0
  return Math.round((statistics.value.tasks_by_status?.completed / total) * 100)
}

// Calculate the percentage for each priority
function getPriorityPercentage(priority) {
  const total = statistics.value.total_tasks
  if (!total) return 0
  return Math.round((statistics.value.tasks_by_priority?.[priority] / total) * 100)
}

// Get the color class for the priority badge
function getPriorityClass(priority) {
  const classes = {
    low: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-red-100 text-red-800',
  }
  return classes[priority] || 'bg-gray-100 text-gray-800'
}

// Get the text color for the priority label
function getPriorityTextClass(priority) {
  const classes = {
    low: 'text-green-600',
    medium: 'text-yellow-600',
    high: 'text-red-600',
  }
  return classes[priority] || 'text-gray-600'
}

// Get the bar color for the priority chart
function getPriorityBarClass(priority) {
  const classes = {
    low: 'bg-green-600',
    medium: 'bg-yellow-600',
    high: 'bg-red-600',
  }
  return classes[priority] || 'bg-gray-600'
}

// Format a date string to a readable date
function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString()
}
</script>
