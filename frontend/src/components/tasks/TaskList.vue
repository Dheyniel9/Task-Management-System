<!-- src/components/tasks/TaskList.vue -->
<!--
  Modern task list with native HTML5 drag and drop functionality.
  Features glass-morphism design, smooth animations, and intuitive interactions.
-->
<template>
  <div class="space-y-3 p-6">
    <!-- ✅ Remove debug info for clean production look -->

    <!-- ✅ Modern task cards with drag & drop -->
    <div
      v-for="(task, index) in props.tasks"
      :key="task.id"
      :draggable="true"
      @dragstart="handleDragStart($event, task, index)"
      @dragover="handleDragOver($event, index)"
      @drop="handleDrop($event, index)"
      @dragend="handleDragEnd"
      class="group relative bg-white/80 backdrop-blur-sm border border-gray-200/50 rounded-2xl p-6 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 cursor-pointer"
      :class="[
        task.status === 'completed' ? 'bg-gradient-to-r from-green-50/80 to-emerald-50/80 border-green-200/50' : 'hover:bg-gradient-to-r hover:from-blue-50/30 hover:to-indigo-50/30',
        isDragging && draggedTaskId === task.id ? 'opacity-50 scale-95 rotate-2' : '',
        dragOverIndex === index ? 'scale-105 shadow-2xl shadow-blue-200/50' : ''
      ]"
    >
      <!-- ✅ Drag handle with modern icon -->
      <div class="absolute left-2 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
        <div class="p-2 bg-gray-100/80 rounded-lg backdrop-blur-sm hover:bg-gray-200/80 transition-colors">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
          </svg>
        </div>
      </div>

      <div class="flex items-start space-x-4 ml-4">
        <!-- ✅ Modern checkbox with smooth animation -->
        <div class="relative mt-1">
          <input
            :id="`task-${task.id}`"
            type="checkbox"
            :checked="task.status === 'completed'"
            @change="$emit('toggle-status', task)"
            class="sr-only"
          />
          <label
            :for="`task-${task.id}`"
            class="flex items-center justify-center w-6 h-6 rounded-xl border-2 cursor-pointer transition-all duration-200"
            :class="task.status === 'completed'
              ? 'bg-gradient-to-r from-green-400 to-emerald-500 border-green-400 shadow-lg shadow-green-200'
              : 'border-gray-300 hover:border-blue-400 hover:shadow-md hover:shadow-blue-100'"
          >
            <svg
              v-if="task.status === 'completed'"
              class="w-4 h-4 text-white animate-bounce-in"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </label>
        </div>

        <!-- ✅ Task content with modern typography -->
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h3
                class="font-semibold text-lg leading-tight cursor-pointer transition-colors duration-200"
                :class="task.status === 'completed'
                  ? 'line-through text-gray-500'
                  : 'text-gray-900 hover:text-blue-600'"
                @click="$emit('edit', task)"
              >
                {{ task.title }}
              </h3>
              <p
                v-if="task.description"
                class="mt-2 text-gray-600 leading-relaxed"
                :class="task.status === 'completed' && 'line-through opacity-75'"
              >
                {{ task.description }}
              </p>
            </div>

            <!-- ✅ Action buttons with modern hover effects -->
            <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
              <button
                @click="$emit('edit', task)"
                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 hover:scale-110"
                title="Edit task"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button
                @click="$emit('delete', task.id)"
                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200 hover:scale-110"
                title="Delete task"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>

          <!-- ✅ Modern task meta with pills and icons -->
          <div class="mt-4 flex items-center space-x-3">
            <span
              class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium ring-1 ring-inset transition-all duration-200 hover:scale-105"
              :class="getPriorityClass(task.priority)"
            >
              <span class="mr-1.5">{{ getPriorityIcon(task.priority) }}</span>
              {{ task.priority.toUpperCase() }}
            </span>
            <span
              class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium ring-1 ring-inset transition-all duration-200"
              :class="task.status === 'completed'
                ? 'bg-green-50 text-green-700 ring-green-600/20'
                : 'bg-amber-50 text-amber-700 ring-amber-600/20'"
            >
              <span class="mr-1.5">{{ task.status === 'completed' ? '✅' : '⏳' }}</span>
              {{ task.status === 'completed' ? 'Completed' : 'Pending' }}
            </span>
            <span class="text-xs text-gray-500 font-medium">
              📅 {{ formatDate(task.created_at) }}
            </span>
          </div>
        </div>
      </div>

      <!-- ✅ Subtle gradient overlay on hover -->
      <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl pointer-events-none"></div>
    </div>

    <!-- ✅ Empty state with modern illustration -->
    <div v-if="!props.tasks || props.tasks.length === 0" class="text-center py-16">
      <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
      </div>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">No tasks yet</h3>
      <p class="text-gray-500">Your task list is waiting for its first item!</p>
    </div>
  </div>
</template>

<script setup>
// Modern task list with native HTML5 drag and drop
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  tasks: {
    type: Array,
    required: true,
    default: () => []
  },
})

const emit = defineEmits(['edit', 'delete', 'toggle-status', 'reorder'])

const authStore = useAuthStore()

// ✅ Drag and drop state
const isDragging = ref(false)
const draggedTaskId = ref(null)
const draggedIndex = ref(null)
const dragOverIndex = ref(null)

// ✅ Native HTML5 drag and drop handlers
function handleDragStart(event, task, index) {
  isDragging.value = true
  draggedTaskId.value = task.id
  draggedIndex.value = index
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/html', event.target)
}

function handleDragOver(event, index) {
  event.preventDefault()
  event.dataTransfer.dropEffect = 'move'
  dragOverIndex.value = index
}

function handleDrop(event, dropIndex) {
  event.preventDefault()

  if (draggedIndex.value !== null && draggedIndex.value !== dropIndex) {
    // Create new order mapping
    const taskOrders = {}
    const reorderedTasks = [...props.tasks]

    // Remove dragged item and insert at new position
    const draggedTask = reorderedTasks.splice(draggedIndex.value, 1)[0]
    reorderedTasks.splice(dropIndex, 0, draggedTask)

    // Create order mapping
    reorderedTasks.forEach((task, index) => {
      taskOrders[task.id] = index
    })

    emit('reorder', taskOrders)
  }

  handleDragEnd()
}

function handleDragEnd() {
  isDragging.value = false
  draggedTaskId.value = null
  draggedIndex.value = null
  dragOverIndex.value = null
}

// ✅ Enhanced priority classes with modern design
function getPriorityClass(priority) {
  const classes = {
    low: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    medium: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    high: 'bg-red-50 text-red-700 ring-red-600/20',
  }
  return classes[priority] || 'bg-gray-50 text-gray-700 ring-gray-600/20'
}

// ✅ Priority icons for better visual hierarchy
function getPriorityIcon(priority) {
  const icons = {
    low: '🟢',
    medium: '🟡',
    high: '🔴',
  }
  return icons[priority] || '⚪'
}

function formatDate(dateString) {
  if (!dateString) return 'Unknown'

  try {
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays === 0) return 'Today'
    if (diffDays === 1) return 'Yesterday'
    if (diffDays < 7) return `${diffDays} days ago`
    return date.toLocaleDateString()
  } catch (error) {
    console.error('Date formatting error:', error)
    return 'Invalid date'
  }
}
</script>
