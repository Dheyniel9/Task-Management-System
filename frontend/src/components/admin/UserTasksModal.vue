<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div 
      ref="modalRef"
      :style="{ transform: `translate(${position.x}px, ${position.y}px)` }"
      class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden cursor-move"
      @mousedown="startDrag"
    >
      <!-- Modal Header -->
      <div 
        ref="headerRef"
        class="bg-indigo-600 text-white p-6 cursor-move select-none"
        @mousedown="startDrag"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div v-if="user.avatar" class="w-12 h-12 rounded-full overflow-hidden border-2 border-white">
              <img :src="user.avatar" :alt="user.name" class="w-full h-full object-cover">
            </div>
            <div v-else class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center border-2 border-white">
              <span class="text-white font-bold text-lg">{{ getInitials(user.name) }}</span>
            </div>
            <div>
              <h2 class="text-2xl font-bold">{{ user.name }}'s Tasks</h2>
              <p class="text-indigo-100">{{ user.email }}</p>
            </div>
          </div>
          <button
            @click="$emit('close')"
            class="text-white hover:text-red-300 transition-colors p-2 rounded-full hover:bg-red-500 hover:bg-opacity-20"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- User Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
          <div class="bg-white rounded-lg p-4 text-center">
            <p class="text-gray-600 text-sm font-medium">Total Tasks</p>
            <p class="text-2xl font-bold text-gray-800">{{ user.tasks_count || 0 }}</p>
          </div>
          <div class="bg-green-500 rounded-lg p-4 text-center">
            <p class="text-white text-sm font-medium">Completed</p>
            <p class="text-2xl font-bold text-white">{{ user.completed_tasks_count || 0 }}</p>
          </div>
          <div class="bg-yellow-500 rounded-lg p-4 text-center">
            <p class="text-white text-sm font-medium">Pending</p>
            <p class="text-2xl font-bold text-white">{{ user.pending_tasks_count || 0 }}</p>
          </div>
          <div class="bg-blue-500 rounded-lg p-4 text-center">
            <p class="text-white text-sm font-medium">Completion Rate</p>
            <p class="text-2xl font-bold text-white">{{ getCompletionRate(user) }}%</p>
          </div>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-6 max-h-[60vh] overflow-y-auto cursor-default" @mousedown.stop>
        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-600 border-t-transparent"></div>
          <span class="ml-3 text-gray-600 font-medium">Loading tasks...</span>
        </div>

        <!-- No Tasks State -->
        <div v-else-if="userTasks.length === 0" class="text-center py-12">
          <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">No tasks found</h3>
          <p class="text-gray-600">This user hasn't created any tasks yet.</p>
        </div>

        <!-- Tasks List -->
        <div v-else class="space-y-4">
          <!-- Filter Buttons -->
          <div class="flex flex-wrap gap-3 mb-6">
            <button
              @click="statusFilter = 'all'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2',
                statusFilter === 'all'
                  ? 'bg-indigo-600 text-white border-indigo-600'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
              ]"
            >
              All Tasks ({{ userTasks.length }})
            </button>
            <button
              @click="statusFilter = 'pending'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2',
                statusFilter === 'pending'
                  ? 'bg-yellow-500 text-white border-yellow-500'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
              ]"
            >
              Pending ({{ filteredTasks.filter(t => t.status === 'pending').length }})
            </button>
            <button
              @click="statusFilter = 'in_progress'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2',
                statusFilter === 'in_progress'
                  ? 'bg-blue-500 text-white border-blue-500'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
              ]"
            >
              In Progress ({{ filteredTasks.filter(t => t.status === 'in_progress').length }})
            </button>
            <button
              @click="statusFilter = 'completed'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2',
                statusFilter === 'completed'
                  ? 'bg-green-500 text-white border-green-500'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
              ]"
            >
              Completed ({{ filteredTasks.filter(t => t.status === 'completed').length }})
            </button>
          </div>

          <!-- Task Items -->
          <div class="space-y-3">
            <div
              v-for="task in filteredTasks"
              :key="task.id"
              class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 hover:shadow-md transition-all duration-200"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-3">
                    <span
                      :class="[
                        'px-3 py-1 rounded-full text-xs font-semibold border',
                        getStatusClass(task.status)
                      ]"
                    >
                      {{ formatStatus(task.status) }}
                    </span>
                    <span class="text-sm text-gray-500 font-medium">
                      Created {{ formatDate(task.created_at) }}
                    </span>
                  </div>
                  
                  <h4 class="font-bold text-gray-900 mb-2 text-lg">{{ task.title }}</h4>
                  
                  <p v-if="task.description" class="text-gray-600 text-sm mb-3 line-clamp-2">
                    {{ task.description }}
                  </p>

                  <div v-if="task.due_date" class="flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span :class="isOverdue(task.due_date) ? 'text-red-600 font-semibold' : 'text-gray-600 font-medium'">
                      Due {{ formatDate(task.due_date) }}
                      <span v-if="isOverdue(task.due_date)" class="ml-1 px-2 py-1 bg-red-100 text-red-800 rounded text-xs">(Overdue)</span>
                    </span>
                  </div>
                </div>

                <div class="flex items-center space-x-2 ml-4">
                  <button
                    @click="viewTaskDetails(task)"
                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200 rounded-lg border border-transparent hover:border-indigo-200"
                    title="View Details"
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

      <!-- Modal Footer -->
      <div class="bg-gray-100 px-6 py-4 border-t border-gray-200" @mousedown.stop>
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-600 font-medium">
            Showing {{ filteredTasks.length }} of {{ userTasks.length }} tasks
          </div>
          <div class="flex space-x-3">
            <button
              @click="resetPosition"
              class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium"
              title="Reset Position"
            >
              Reset Position
            </button>
            <button
              @click="$emit('close')"
              class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { adminApi } from '@/services/adminApi'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])

// Reactive data
const loading = ref(false)
const userTasks = ref([])
const statusFilter = ref('all')

// Dragging functionality
const modalRef = ref(null)
const headerRef = ref(null)
const isDragging = ref(false)
const position = ref({ x: 0, y: 0 })
const startPos = ref({ x: 0, y: 0 })
const initialMousePos = ref({ x: 0, y: 0 })

// Computed properties
const filteredTasks = computed(() => {
  if (statusFilter.value === 'all') {
    return userTasks.value
  }
  return userTasks.value.filter(task => task.status === statusFilter.value)
})

// Dragging methods
const startDrag = (e) => {
  isDragging.value = true
  initialMousePos.value = { x: e.clientX, y: e.clientY }
  startPos.value = { ...position.value }
  document.addEventListener('mousemove', drag)
  document.addEventListener('mouseup', stopDrag)
  e.preventDefault()
}

const drag = (e) => {
  if (!isDragging.value) return
  
  const deltaX = e.clientX - initialMousePos.value.x
  const deltaY = e.clientY - initialMousePos.value.y
  
  position.value = {
    x: startPos.value.x + deltaX,
    y: startPos.value.y + deltaY
  }
}

const stopDrag = () => {
  isDragging.value = false
  document.removeEventListener('mousemove', drag)
  document.removeEventListener('mouseup', stopDrag)
}

const resetPosition = () => {
  position.value = { x: 0, y: 0 }
}

// Methods
const fetchUserTasks = async () => {
  try {
    loading.value = true
    const response = await adminApi.getAllTasks({ user_id: props.user.id })
    userTasks.value = response.data.data
  } catch (error) {
    console.error('Error fetching user tasks:', error)
    userTasks.value = []
  } finally {
    loading.value = false
  }
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

const formatStatus = (status) => {
  return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800 border-green-300'
    case 'in_progress':
      return 'bg-blue-100 text-blue-800 border-blue-300'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800 border-yellow-300'
    default:
      return 'bg-gray-100 text-gray-800 border-gray-300'
  }
}

const isOverdue = (dueDate) => {
  if (!dueDate) return false
  return new Date(dueDate) < new Date()
}

const viewTaskDetails = (task) => {
  console.log('View task details:', task)
}

// Lifecycle
onMounted(() => {
  fetchUserTasks()
})

onUnmounted(() => {
  document.removeEventListener('mousemove', drag)
  document.removeEventListener('mouseup', stopDrag)
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.cursor-move {
  cursor: move;
}

.cursor-default {
  cursor: default;
}

.select-none {
  user-select: none;
}
</style>