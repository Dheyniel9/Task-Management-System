<!-- src/components/tasks/TaskList.vue -->
<!--
  This component displays a list of tasks with checkboxes and action buttons.
  Temporarily simplified to debug display issues.
-->
<template>
  <div class="divide-y divide-gray-200">
    <!-- ✅ Debug info for TaskList -->
    <div class="p-4 bg-yellow-50 border-b border-yellow-200">
      <p class="text-sm text-yellow-800">
        TaskList Debug: Received {{ props.tasks?.length || 0 }} tasks
      </p>
      <p class="text-xs text-yellow-700">
        Tasks: {{ props.tasks?.map(t => t.title).join(', ') || 'None' }}
      </p>
    </div>

    <!-- ✅ Simple task display without draggable -->
    <div
      v-for="task in props.tasks"
      :key="task.id"
      class="p-4 hover:bg-gray-50 transition-colors duration-200 border-l-4"
      :class="task.status === 'completed' ? 'border-green-400 bg-green-50' : 'border-blue-400'"
    >
      <div class="flex items-start space-x-3">
        <!-- Checkbox -->
        <input
          :id="`task-${task.id}`"
          type="checkbox"
          :checked="task.status === 'completed'"
          @change="$emit('toggle-status', task)"
          class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
        />

        <!-- Task content -->
        <div class="flex-1">
          <label
            :for="`task-${task.id}`"
            class="block cursor-pointer"
            :class="task.status === 'completed' ? 'line-through text-gray-500' : 'text-gray-900'"
          >
            <span class="font-medium text-lg">{{ task.title }}</span>
          </label>
          <p
            v-if="task.description"
            class="mt-1 text-sm text-gray-600"
            :class="task.status === 'completed' && 'line-through'"
          >
            {{ task.description }}
          </p>

          <!-- Task meta -->
          <div class="mt-3 flex items-center space-x-4">
            <span
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border"
              :class="getPriorityClass(task.priority)"
            >
              🔥 {{ task.priority.toUpperCase() }}
            </span>
            <span
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
              :class="task.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
            >
              {{ task.status === 'completed' ? '✅ Completed' : '⏳ Pending' }}
            </span>
            <span class="text-xs text-gray-500">
              📅 Created {{ formatDate(task.created_at) }}
            </span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2">
          <button
            @click="$emit('edit', task)"
            class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
            title="Edit task"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          <button
            @click="$emit('delete', task.id)"
            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            title="Delete task"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- ✅ Show message if no tasks -->
    <div v-if="!props.tasks || props.tasks.length === 0" class="p-8 text-center text-gray-500">
      No tasks provided to TaskList component
    </div>
  </div>
</template>

<script setup>
// This script handles task display and emits events
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

// ✅ Log tasks when component receives them
console.log('📋 TaskList received tasks:', props.tasks)

function getPriorityClass(priority) {
  const classes = {
    low: 'priority-low',
    medium: 'priority-medium',
    high: 'priority-high',
  }
  return classes[priority] || 'bg-gray-100 text-gray-800 border-gray-300'
}

function formatDate(dateString) {
  if (!dateString) return 'Unknown'

  try {
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays === 0) return 'today'
    if (diffDays === 1) return 'yesterday'
    if (diffDays < 7) return `${diffDays} days ago`
    return date.toLocaleDateString()
  } catch (error) {
    console.error('Date formatting error:', error)
    return 'Invalid date'
  }
}
</script>
