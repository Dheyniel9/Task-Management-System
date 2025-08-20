<!-- src/views/Tasks.vue -->
<!--
  This is the main tasks page. It shows your tasks, lets you filter, add, edit, delete, and reorder them.
  It also displays task statistics and uses modals for creating/editing tasks.
-->
<template>
  <div class="space-y-6">
    <!-- ✅ Debug Panel -->
    <!-- <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
      <h3 class="font-semibold text-blue-900 mb-2">Debug Info:</h3>
      <div class="text-sm text-blue-800 space-y-1">
        <p>Loading: {{ taskStore.loading }}</p>
        <p>Raw Tasks Count: {{ taskStore.tasks ? taskStore.tasks.length : 'null' }}</p>
        <p>Filtered Tasks Count: {{ taskStore.filteredTasks ? taskStore.filteredTasks.length : 'null' }}</p>
        <p>Statistics: {{ JSON.stringify(taskStore.statistics) }}</p>
        <p>Active Filters: {{ JSON.stringify(taskStore.filters) }}</p>
        <p>Your Task: {{ taskStore.tasks?.[0]?.title || 'No task found' }}</p>
      </div>
      <div class="mt-3 space-x-2">
        <button @click="forceRefresh" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Force Refresh</button>
        <button @click="clearAllFilters" class="px-3 py-1 bg-green-600 text-white rounded text-sm">Clear All Filters</button>
      </div>
    </div> -->

    <!-- Header with statistics -->
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-900">My Tasks</h1>
        <button
          @click="showCreateModal = true"
          class="btn btn-primary"
        >
          + Add Task
        </button>
      </div>
      <!-- Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 rounded-lg p-4">
          <p class="text-sm text-gray-600">Total Tasks</p>
          <p class="text-2xl font-bold text-gray-900">{{ taskStore.statistics?.total || 0 }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
          <p class="text-sm text-green-600">Completed</p>
          <p class="text-2xl font-bold text-green-900">{{ taskStore.statistics?.completed || 0 }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4">
          <p class="text-sm text-yellow-600">Pending</p>
          <p class="text-2xl font-bold text-yellow-900">{{ taskStore.statistics?.pending || 0 }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-4">
          <p class="text-sm text-red-600">High Priority</p>
          <p class="text-2xl font-bold text-red-900">{{ taskStore.statistics?.by_priority?.high || 0 }}</p>
        </div>
      </div>
    </div>
    <!-- Filters -->
    <TaskFilters />
    <!-- Task List -->
    <div class="bg-white rounded-lg shadow">
      <div v-if="taskStore.loading" class="p-8 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        <p class="mt-2 text-gray-600">Loading tasks...</p>
      </div>
      <div v-else-if="!taskStore.filteredTasks || taskStore.filteredTasks.length === 0" class="p-8 text-center">
        <p class="text-gray-600">No tasks found.</p>
        <p class="text-sm text-gray-500 mt-1">
          {{ taskStore.tasks && taskStore.tasks.length > 0 ? 'Try adjusting your filters above' : 'Create your first task to get started' }}
        </p>

        <!-- ✅ Show raw task data for debugging -->
        <div v-if="taskStore.tasks && taskStore.tasks.length > 0" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
          <p class="text-sm text-yellow-800">
            Found {{ taskStore.tasks.length }} task(s) in database, but filters are hiding them.
          </p>
          <p class="text-xs text-yellow-700 mt-1">
            Task: "{{ taskStore.tasks[0].title }}" ({{ taskStore.tasks[0].status }}, {{ taskStore.tasks[0].priority }})
          </p>
        </div>

        <div class="mt-4 space-x-2">
          <button
            @click="showCreateModal = true"
            class="btn btn-primary"
          >
            {{ taskStore.tasks && taskStore.tasks.length > 0 ? 'Add New Task' : 'Create your first task' }}
          </button>
          <button
            v-if="taskStore.tasks && taskStore.tasks.length > 0"
            @click="clearAllFilters"
            class="btn btn-secondary"
          >
            Clear All Filters
          </button>
        </div>
      </div>
      <TaskList
        v-else
        :tasks="taskStore.filteredTasks"
        @edit="editTask"
        @delete="deleteTask"
        @toggle-status="toggleTaskStatus"
        @reorder="handleReorder"
      />
    </div>
    <!-- Create/Edit Modal -->
    <TaskModal
      v-if="showCreateModal || editingTask"
      :task="editingTask"
      @close="closeModal"
      @save="saveTask"
    />
  </div>
</template>

<script setup>
// This script handles all the logic for displaying, editing, and managing tasks
import { ref, onMounted } from 'vue'
import { useTaskStore } from '@/stores/tasks'
import TaskList from '@/components/tasks/TaskList.vue'
import TaskFilters from '@/components/tasks/TaskFilters.vue'
import TaskModal from '@/components/tasks/TaskModal.vue'

const taskStore = useTaskStore()
const showCreateModal = ref(false)
const editingTask = ref(null)

onMounted(async () => {
  try {
    console.log('🚀 Starting to fetch tasks...')

    // ✅ FORCE clear all filters first
    taskStore.clearFilters()
    console.log('🧹 Filters cleared before fetch')

    await taskStore.fetchTasks()
    console.log('📋 Tasks fetched:', taskStore.tasks)
    console.log('🔍 Filtered tasks after fetch:', taskStore.filteredTasks)

    await taskStore.fetchStatistics()
    console.log('📊 Statistics fetched:', taskStore.statistics)

  } catch (error) {
    console.error('❌ Error loading tasks:', error)
  }
})

function editTask(task) {
  editingTask.value = task
}

async function deleteTask(taskId) {
  if (confirm('Are you sure you want to delete this task?')) {
    try {
      await taskStore.deleteTask(taskId)
      console.log('🗑️ Task deleted:', taskId)
    } catch (error) {
      console.error('❌ Error deleting task:', error)
    }
  }
}

async function toggleTaskStatus(task) {
  const newStatus = task.status === 'pending' ? 'completed' : 'pending'
  try {
    await taskStore.updateTask(task.id, { status: newStatus })
    console.log('✅ Task status updated:', task.id, newStatus)
  } catch (error) {
    console.error('❌ Error updating task status:', error)
  }
}

async function saveTask(taskData) {
  try {
    if (editingTask.value) {
      await taskStore.updateTask(editingTask.value.id, taskData)
      console.log('📝 Task updated:', editingTask.value.id)
    } else {
      await taskStore.createTask(taskData)
      console.log('🆕 Task created:', taskData)
    }
    closeModal()
  } catch (error) {
    console.error('❌ Error saving task:', error)
  }
}

function closeModal() {
  showCreateModal.value = false
  editingTask.value = null
}

async function handleReorder(taskOrders) {
  try {
    await taskStore.reorderTasks(taskOrders)
    console.log('🔄 Tasks reordered')
  } catch (error) {
    console.error('❌ Error reordering tasks:', error)
  }
}

// ✅ Added debug functions
function clearAllFilters() {
  taskStore.clearFilters()
  console.log('🧹 All filters cleared manually')
  console.log('📋 Tasks after clear:', taskStore.filteredTasks)
}

function forceRefresh() {
  taskStore.fetchTasks()
  console.log('🔄 Force refresh triggered')
}
</script>
