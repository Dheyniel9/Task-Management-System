<template>
  <div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 p-8">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-900 to-indigo-600 bg-clip-text text-transparent">
            Admin Dashboard
          </h1>
          <p class="text-gray-600 mt-2">Manage users and monitor system activity</p>
        </div>
        <div class="flex space-x-4">
          <button
            @click="refreshData"
            :disabled="loading"
            class="btn btn-secondary flex items-center gap-2"
          >
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refresh
          </button>
        </div>
      </div>

      <!-- System Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-6 border border-blue-200/50">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-blue-600 mb-1">Total Users</p>
              <p class="text-3xl font-bold text-blue-900">{{ systemStats.total_users || 0 }}</p>
            </div>
            <div class="p-3 bg-blue-200/50 rounded-xl">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Tasks -->
        <div class="bg-gradient-to-br from-emerald-50 to-green-100 rounded-2xl p-6 border border-emerald-200/50">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-emerald-600 mb-1">Total Tasks</p>
              <p class="text-3xl font-bold text-emerald-900">{{ systemStats.total_tasks || 0 }}</p>
            </div>
            <div class="p-3 bg-emerald-200/50 rounded-xl">
              <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Completed Tasks -->
        <div class="bg-gradient-to-br from-amber-50 to-yellow-100 rounded-2xl p-6 border border-amber-200/50">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-amber-600 mb-1">Completed</p>
              <p class="text-3xl font-bold text-amber-900">{{ systemStats.tasks_by_status?.completed || 0 }}</p>
            </div>
            <div class="p-3 bg-amber-200/50 rounded-xl">
              <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Pending Tasks -->
        <div class="bg-gradient-to-br from-red-50 to-pink-100 rounded-2xl p-6 border border-red-200/50">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-red-600 mb-1">Pending</p>
              <p class="text-3xl font-bold text-red-900">{{ systemStats.tasks_by_status?.pending || 0 }}</p>
            </div>
            <div class="p-3 bg-red-200/50 rounded-xl">
              <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 p-6">
      <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="flex-1 max-w-md">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="searchQuery"
              @input="debouncedSearch"
              type="text"
              placeholder="Search users..."
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            >
          </div>
        </div>
        <div class="flex space-x-2">
          <select 
            v-model="sortBy"
            @change="sortUsers"
            class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
          >
            <option value="name">Sort by Name</option>
            <option value="total_tasks">Sort by Total Tasks</option>
            <option value="completed_tasks">Sort by Completed</option>
            <option value="created_at">Sort by Join Date</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Users Grid -->
    <div v-if="loading && users.length === 0" class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 p-12 text-center">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-purple-600 border-t-transparent"></div>
      <p class="mt-4 text-lg text-gray-600">Loading users...</p>
    </div>

    <div v-else-if="filteredUsers.length === 0" class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 p-12 text-center">
      <div class="mx-auto w-24 h-24 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
        </svg>
      </div>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">No users found</h3>
      <p class="text-gray-600">Try adjusting your search criteria</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="user in paginatedUsers"
        :key="user.id"
        @click="selectUser(user)"
        class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 p-6 cursor-pointer hover:shadow-xl hover:scale-105 transition-all duration-300 group"
      >
        <!-- User Avatar and Info -->
        <div class="flex items-center space-x-4 mb-4">
          <div class="relative">
            <div v-if="user.avatar" class="w-12 h-12 rounded-full overflow-hidden">
              <img :src="user.avatar" :alt="user.name" class="w-full h-full object-cover">
            </div>
            <div v-else class="w-12 h-12 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-full flex items-center justify-center">
              <span class="text-white font-bold text-lg">{{ getInitials(user.name) }}</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 border-2 border-white rounded-full"></div>
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-gray-900 truncate group-hover:text-purple-700 transition-colors">
              {{ user.name }}
            </h3>
            <p class="text-sm text-gray-600 truncate">{{ user.email }}</p>
          </div>
        </div>

        <!-- Task Statistics -->
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Total Tasks</span>
            <span class="font-semibold text-gray-900">{{ user.tasks_count || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Completed</span>
            <span class="font-semibold text-emerald-600">{{ user.completed_tasks_count || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Pending</span>
            <span class="font-semibold text-amber-600">{{ user.pending_tasks_count || 0 }}</span>
          </div>
          
          <!-- Progress Bar -->
          <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
              <span>Progress</span>
              <span>{{ getCompletionRate(user) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
              <div 
                class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-500 ease-out"
                :style="{ width: getCompletionRate(user) + '%' }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Join Date -->
        <div class="mt-4 pt-4 border-t border-gray-200/50">
          <div class="flex items-center text-xs text-gray-500">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Joined {{ formatDate(user.created_at) }}
          </div>
        </div>

        <!-- Click indicator -->
        <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
          <div class="flex items-center justify-center text-purple-600 text-sm font-medium">
            <span>Click to view tasks</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 p-6">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600">
          Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredUsers.length) }} of {{ filteredUsers.length }} users
        </div>
        <div class="flex space-x-2">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <div class="flex space-x-1">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="currentPage = page"
              :class="[
                'px-3 py-2 text-sm border rounded-lg',
                page === currentPage
                  ? 'bg-purple-600 text-white border-purple-600'
                  : 'border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
          </div>
          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- User Tasks Modal -->
    <UserTasksModal
      v-if="selectedUser"
      :user="selectedUser"
      @close="selectedUser = null"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import UserTasksModal from '@/components/admin/UserTasksModal.vue'
import { adminApi } from '@/services/adminApi'

const router = useRouter()

// Reactive data
const loading = ref(false)
const users = ref([])
const systemStats = ref({})
const selectedUser = ref(null)
const searchQuery = ref('')
const sortBy = ref('name')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(12)

// Computed properties
const filteredUsers = computed(() => {
  let filtered = [...users.value]

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(user =>
      user.name.toLowerCase().includes(query) ||
      user.email.toLowerCase().includes(query)
    )
  }

  // Sorting
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'name':
        return a.name.localeCompare(b.name)
      case 'total_tasks':
        return (b.tasks_count || 0) - (a.tasks_count || 0)
      case 'completed_tasks':
        return (b.completed_tasks_count || 0) - (a.completed_tasks_count || 0)
      case 'created_at':
        return new Date(b.created_at) - new Date(a.created_at)
      default:
        return 0
    }
  })

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / itemsPerPage.value))

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredUsers.value.slice(start, end)
})

const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const current = currentPage.value
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) pages.push(i)
      pages.push('...', total)
    } else if (current >= total - 3) {
      pages.push(1, '...')
      for (let i = total - 4; i <= total; i++) pages.push(i)
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total)
    }
  }
  
  return pages.filter(p => p !== '...' || true)
})

// Methods
const fetchUsers = async () => {
  try {
    loading.value = true
    const response = await adminApi.getUsers()
    console.log('getUsers response:', response)
    users.value = response.data.data
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}

const fetchSystemStats = async () => {
  try {
    const response = await adminApi.getSystemStatistics()
    console.log('getSystemStatistics response:', response)
    systemStats.value = response.data.data
  } catch (error) {
    console.error('Error fetching system stats:', error)
  }
}

const refreshData = async () => {
  await Promise.all([fetchUsers(), fetchSystemStats()])
}

const selectUser = (user) => {
  selectedUser.value = user
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const getCompletionRate = (user) => {
  const total = user.tasks_count || 0
  const completed = user.completed_tasks_count || 0
  return total > 0 ? Math.round((completed / total) * 100) : 0
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const sortUsers = () => {
  currentPage.value = 1 // Reset to first page when sorting
}

// Debounced search
let searchTimeout
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1 // Reset to first page when searching
  }, 300)
}

// Watch for search query changes
watch(searchQuery, () => {
  currentPage.value = 1
})

// Lifecycle
onMounted(() => {
  refreshData()
})
</script>

<style scoped>
.btn {
  @apply px-6 py-3 rounded-xl font-semibold transition-all duration-200;
}

.btn-primary {
  @apply bg-gradient-to-r from-purple-600 to-indigo-600 text-white hover:from-purple-700 hover:to-indigo-700;
}

.btn-secondary {
  @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50;
}
</style>
