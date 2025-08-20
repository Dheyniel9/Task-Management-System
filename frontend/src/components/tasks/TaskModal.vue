<!--
  TaskModal.vue
  This component shows a popup (modal) for creating or editing a task.
  - If you pass a task prop, it fills the form for editing. If not, it's for creating a new task.
  - The form has fields for title, description, priority, and (when editing) status.
  - The Cancel button closes the modal. The Create/Update button saves the task.
-->
<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- The dark background behind the modal. Click to close. -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="$emit('close')"></div>

    <!-- The modal box itself -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full animate-slide-up">
        <!-- Modal header with title and close button -->
        <div class="flex items-center justify-between p-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ task ? 'Edit Task' : 'Create New Task' }}
          </h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- The form for entering task details -->
        <form @submit.prevent="handleSubmit" class="p-4 space-y-4">
          <!-- Title input (required) -->
          <div>
            <label for="task-title" class="block text-sm font-medium text-gray-700 mb-1">
              Title *
            </label>
            <input
              id="task-title"
              v-model="form.title"
              type="text"
              required
              class="input"
              placeholder="Enter task title"
            />
          </div>

          <!-- Description input (optional) -->
          <div>
            <label for="task-description" class="block text-sm font-medium text-gray-700 mb-1">
              Description
            </label>
            <textarea
              id="task-description"
              v-model="form.description"
              rows="3"
              class="input resize-none"
              placeholder="Enter task description (optional)"
            ></textarea>
          </div>

          <!-- Priority dropdown -->
          <div>
            <label for="task-priority" class="block text-sm font-medium text-gray-700 mb-1">
              Priority
            </label>
            <select
              id="task-priority"
              v-model="form.priority"
              class="input"
            >
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>

          <!-- Status dropdown (only shows when editing a task) -->
          <div v-if="task">
            <label for="task-status" class="block text-sm font-medium text-gray-700 mb-1">
              Status
            </label>
            <select
              id="task-status"
              v-model="form.status"
              class="input"
            >
              <option value="pending">Pending</option>
              <option value="completed">Completed</option>
            </select>
          </div>

          <!-- Action buttons: Cancel and Create/Update -->
          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="$emit('close')"
              class="btn btn-secondary"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="btn btn-primary"
            >
              {{ task ? 'Update' : 'Create' }} Task
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
// Import Vue features for reactivity and watching props
import { reactive, watch } from 'vue'

// Props: receives a task object if editing, or null if creating
const props = defineProps({
  task: {
    type: Object,
    default: null,
  },
})

// Emits events to parent: close (when closing modal), save (when submitting form)
const emit = defineEmits(['close', 'save'])

// The form data for the modal
const form = reactive({
  title: '',
  description: '',
  priority: 'medium',
  status: 'pending',
})

// If editing, fill the form with the task's data
watch(() => props.task, (newTask) => {
  if (newTask) {
    form.title = newTask.title || ''
    form.description = newTask.description || ''
    form.priority = newTask.priority || 'medium'
    form.status = newTask.status || 'pending'
  }
}, { immediate: true })

// Handle form submission: emit the data to parent
function handleSubmit() {
  const data = {
    title: form.title.trim(),
    description: form.description.trim(),
    priority: form.priority,
  }

  if (props.task) {
    data.status = form.status
  }

  emit('save', data)
}
</script>
