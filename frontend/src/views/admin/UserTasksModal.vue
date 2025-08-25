<!-- src/components/admin/UserTasksModal.vue -->
<template>
  <!-- Modal Backdrop -->
  <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click="handleBackdropClick">
    <!-- Modal Content -->
    <div 
      ref="modalContent"
      class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden"
      @click.stop
    >
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div v-if="user.avatar" class="w-16 h-16 rounded-full overflow-hidden border-4 border-white/20">
              <img :src="user.avatar" :alt="user.name" class="w-full h-full object-cover">
            </div>
            <div v-else class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center border-4 border-white/20">
              <span class="text-white font-bold text-xl">{{ getInitials(user.name) }}</span>
            </div>
            <div>
              <h2 class="text-2xl font-bold">{{ user.name }}'s Tasks</h2>
              <p class="text-purple-100">{{ user.email }}</p>
            </div>
          </div>
          <button
            @click="$emit('close')"
            class="p-2 hover:bg-white/10 rounded-full transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- User Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6">
          <div class="bg-white/10 rounded-xl p-4">
            <div class="text-2xl font-bold">{{ userStats.total || 0 }}</div>
            <div class="text-sm text-purple-100">Total Tasks</div>
          </div>
          <div class="bg-white/10 rounded-xl p-4">
            <div class="text-2xl font-bold text-emerald-300">{{ userStats.completed || 0 }}</div>
            <div class="text-sm text-purple-100">Completed</div>
          </div>
          <div class="bg-white/10 rounded-xl p-4">
            <div class="text-2xl font-bold text-amber-300">{{ userStats.pending || 0 }}</div>
            <div class="text-sm text-purple-100">Pending</div>
          </div>
          <div class="bg-white/10 rounded-xl p-4">
            <div class="text-2xl font-bold text-green-300">{{ completionRate }}%</div>
            <div class="text-sm text-purple-100">Completion Rate</div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="p-6 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
          <div class="flex-1 max-w-md">
            <div class="relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search tasks..."
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              >
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <select 
              v-model="statusFilter"
              class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            >
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="completed">Completed</option>
            </select>
            <select 
              v-model="priorityFilter"
              class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            >
              <option value="">All Priority</option>
              <option value="high">High Priority</option>
              <option value="medium">Medium Priority</option>
              <option value="low">Low Priority</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Tasks List -->
      <div class="flex-1 overflow-hidden">
        <div v-if="loading" class="p-12 text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-purple-600 border-t-transparent"></div>
          <p class="mt-4 text-lg text-gray-600">Loading tasks...</p>
        </div>

        <div v-else-if="filteredTasks.length === 0" class="p-12 text-center">
          <div class="mx-auto w-24 h-24 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">No tasks found</h3>
          <p class="text-gray-600">This user doesn't have any tasks matching your criteria.</p>
        </div>

        <div v-else class="max-h-[60vh] overflow-y-auto">
          <div class="divide-y divide-gray-200">
            <div
              v-for="task in paginatedTasks"
              :key="task.id"
              class="p-6 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                  <!-- Task Header -->
                  <div class="flex items-center space-x-3 mb-3">
                    <div 
                      class="flex-shrink-0 w-3 h-3 rounded-full"
                      :class="{
                        'bg-emerald-500': task.status === 'completed',
                        'bg-amber-500': task.status === 'pending'
                      }"
                    ></div>
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                      {{ task.title }}
                    </h3>
                    
                    <!-- Priority Badge -->
                    <span 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="{
                        'bg-red-100 text-red-800': task.priority === 'high',
                        'bg-amber-100 text-amber-800': task.priority === 'medium',
                        'bg-green-100 text-green-800': task.priority === 'low'
                      }"
                    >
                      {{ task.priority }} priority
                    </span>

                    <!-- Status Badge -->
                    <span 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="{
                        'bg-emerald-100 text-emerald-800': task.status === 'completed',
                        'bg-amber-100 text-amber-800': task.status === 'pending'
                      }"
                    >
                      {{ task.status }}
                    </span>
                  </div>

                  <!-- Task Description -->
                  <p v-if="task.description" class="text-gray-600 mb-3 line-clamp-2">
                    {{ task.description }}
                  </p>

                  <!-- Task Metadata -->
                  <div class="flex items-center space-x-6 text-sm text-gray-500">
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      <span>Created {{ formatDate(task.created_at) }}</span>
                    </div>
                    <div v-if="task.updated_at !== task.created_at" class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                      </svg>
                      <span>Updated {{ formatDate(task.updated_at) }}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                      </svg>
                      <span>Order: {{ task.order }}</span>
                    </div>
                  </div>
                </div>

                <!-- Task Actions -->
                <div class="flex-shrink-0 ml-4">
                  <div class="flex items-center space-x-2">
                    <!-- View Details Button -->
                    <button
                      @click="viewTaskDetails(task)"
                      class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                      title="View details"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer with Pagination -->
      <div v-if="totalTaskPages > 1" class="p-6 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-600">
            Showing {{ (currentTaskPage - 1) * tasksPerPage + 1 }} to {{ Math.min(currentTaskPage * tasksPerPage, filteredTasks.length) }} of {{ filteredTasks.length }} tasks
          </div>
          <div class="flex space-x-2">
            <button
              @click="currentTaskPage--"
              :disabled="currentTaskPage === 1"
              class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <div class="flex space-x-1">
              <button
                v-for="page in visibleTaskPages"
                :key="page"
                @click="currentTaskPage = page"
                :class="[
                  'px-3 py-2 text-sm border rounded-lg',
                  page === currentTaskPage
                    ? 'bg-purple-600 text-white border-purple-600'
                    : 'border-gray-300 hover:bg-white'
                ]"
              >
                {{ page }}
              </button>
            </div>
            <button
              @click="currentTaskPage++"
              :disabled="currentTaskPage === totalTaskPages"
              class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Detail Modal -->
    <TaskDetailModal
      v-if="selectedTask"
      :task="selectedTask"
      @close="selectedTask = null"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import TaskDetailModal from '@/components/admin/TaskDetailModal.vue'
import { adminApi } from '@/services/adminApi'

// Props and Emits
const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])

// Reactive data
const loading = ref(false)
const tasks = ref([])
const userStats = ref({})
const selectedTask = ref(null)
const searchQuery = ref('')
const statusFilter = ref('')
const priorityFilter = ref('')

// Pagination
const currentTaskPage = ref(1)
const tasksPerPage = ref(10)

// Computed properties
const filteredTasks = computed(() => {
  let filtered = [...tasks.value]

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(task =>
      task.title.toLowerCase().includes(query) ||
      (task.description && task.description.toLowerCase().includes(query))
    )
  }

  // Status filter
  if (statusFilter.value) {
    filtered = filtered.filter(task => task.status === statusFilter.value)
  }

  // Priority filter
  if (priorityFilter.value) {
    filtered = filtered.filter(task => task.priority === priorityFilter.value)
  }

  return filtered
})

const totalTaskPages = computed(() => Math.ceil(filteredTasks.value.length / tasksPerPage.value))

const paginatedTasks = computed(() => {
  const start = (currentTaskPage.value - 1) * tasksPerPage.value
  const end = start + tasksPerPage.value
  return filteredTasks.value.slice(start, end)
})

const visibleTaskPages = computed(() => {
  const pages = []
  const total = totalTaskPages.value
  const current = currentTaskPage.value
  
  if (total <= 5) {
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 3) {
      for (let i = 1; i <= 4; i++) pages.push(i)
      pages.push('...', total)
    } else if (current >= total - 2) {
      pages.push(1, '...')
      for (let i = total - 3; i <= total; i++) pages.push(i)
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total)
    }
  }
  
  return pages.filter(p => p !== '...' || true)
})

const completionRate = computed(() => {
  const total = userStats.value.total || 0
  const completed = userStats.value.completed || 0
  return total > 0 ? Math.round((completed / total) * 100) : 0
})

// Methods
const fetchUserTasks = async () => {
  try {
    loading.value = true
    const response = await adminApi.getAllTasks({ user_id: props.user.id })
    tasks.value = response.data.data
  } catch (error) {
    console.error('Error fetching user tasks:', error)
  } finally {
    loading.value = false
  }
}

const fetchUserStatistics = async () => {
  try {
    const response = await adminApi.getUserStatistics(props.user.id)
    userStats.value = response.data.data.statistics
  } catch (error) {
    console.error('Error fetching user statistics:', error)
  }
}

const viewTaskDetails = (task) => {
  selectedTask.value = task
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    emit('close')
  }
}

// Watch for filter changes to reset pagination
watch([searchQuery, statusFilter, priorityFilter], () => {
  currentTaskPage.value = 1
})

// Lifecycle
onMounted(() => {
  fetchUserTasks()
  fetchUserStatistics()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>