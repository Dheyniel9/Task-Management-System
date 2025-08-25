<!--
  This is the main tasks page. It shows your tasks, lets you filter, add, edit, delete, and reorder them.
  It also displays task statistics and uses modals for creating/editing tasks.
-->
<template>
  <div v-if="!isAdmin" class="space-y-6">
    <!-- ✅ Enhanced Statistics Dashboard -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 p-8">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
            My Tasks
          </h1>
          <p class="text-gray-600 mt-2">Track your productivity and manage your workflow</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="btn btn-primary flex items-center gap-3 shadow-lg hover:shadow-xl"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Add New Task
        </button>
      </div>

      <!-- ✅ Comprehensive Statistics Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Tasks Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-6 border border-blue-200/50 group hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-blue-600 mb-1">Total Tasks</p>
              <p class="text-3xl font-bold text-blue-900">{{ taskStore.statistics?.total || 0 }}</p>
              <p class="text-xs text-blue-700 mt-1">{{ getTasksChange() }}</p>
            </div>
            <div class="p-3 bg-blue-200/50 rounded-xl group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
          </div>
          <!-- Progress indicator -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-200">
            <div class="h-full bg-blue-500 transition-all duration-500" :style="{ width: '100%' }"></div>
          </div>
        </div>

        <!-- Completed Tasks Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 to-green-100 rounded-2xl p-6 border border-emerald-200/50 group hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-emerald-600 mb-1">Completed</p>
              <p class="text-3xl font-bold text-emerald-900">{{ taskStore.statistics?.completed || 0 }}</p>
              <p class="text-xs text-emerald-700 mt-1">{{ getCompletionRate() }}% completion rate</p>
            </div>
            <div class="p-3 bg-emerald-200/50 rounded-xl group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <!-- Progress indicator -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-200">
            <div class="h-full bg-emerald-500 transition-all duration-500" :style="{ width: getCompletionRate() + '%' }"></div>
          </div>
        </div>

        <!-- Pending Tasks Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 to-yellow-100 rounded-2xl p-6 border border-amber-200/50 group hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-amber-600 mb-1">Pending</p>
              <p class="text-3xl font-bold text-amber-900">{{ taskStore.statistics?.pending || 0 }}</p>
              <p class="text-xs text-amber-700 mt-1">{{ getPendingPercentage() }}% remaining</p>
            </div>
            <div class="p-3 bg-amber-200/50 rounded-xl group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <!-- Progress indicator -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-200">
            <div class="h-full bg-amber-500 transition-all duration-500" :style="{ width: getPendingPercentage() + '%' }"></div>
          </div>
        </div>

        <!-- High Priority Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-red-50 to-pink-100 rounded-2xl p-6 border border-red-200/50 group hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-red-600 mb-1">High Priority</p>
              <p class="text-3xl font-bold text-red-900">{{ taskStore.statistics?.by_priority?.high || 0 }}</p>
              <p class="text-xs text-red-700 mt-1">{{ getHighPriorityPercentage() }}% of total</p>
            </div>
            <div class="p-3 bg-red-200/50 rounded-xl group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
          </div>
          <!-- Progress indicator -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-red-200">
            <div class="h-full bg-red-500 transition-all duration-500" :style="{ width: getHighPriorityPercentage() + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- ✅ Detailed Statistics Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Priority Breakdown -->
        <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-200/50">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Priority Breakdown
          </h3>
          <div class="space-y-4">
            <!-- High Priority -->
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                <span class="text-sm font-medium text-gray-700">High Priority</span>
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-sm font-bold text-gray-900">{{ taskStore.statistics?.by_priority?.high || 0 }}</span>
                <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-red-500 transition-all duration-500" :style="{ width: getHighPriorityPercentage() + '%' }"></div>
                </div>
              </div>
            </div>
            <!-- Medium Priority -->
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-3 h-3 bg-amber-500 rounded-full mr-3"></div>
                <span class="text-sm font-medium text-gray-700">Medium Priority</span>
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-sm font-bold text-gray-900">{{ taskStore.statistics?.by_priority?.medium || 0 }}</span>
                <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-amber-500 transition-all duration-500" :style="{ width: getMediumPriorityPercentage() + '%' }"></div>
                </div>
              </div>
            </div>
            <!-- Low Priority -->
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-3 h-3 bg-emerald-500 rounded-full mr-3"></div>
                <span class="text-sm font-medium text-gray-700">Low Priority</span>
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-sm font-bold text-gray-900">{{ taskStore.statistics?.by_priority?.low || 0 }}</span>
                <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-emerald-500 transition-all duration-500" :style="{ width: getLowPriorityPercentage() + '%' }"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Overview -->
        <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-200/50">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            Progress Overview
          </h3>

          <!-- Circular Progress -->
          <div class="flex items-center justify-center mb-6">
            <div class="relative w-32 h-32">
              <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                <path
                  d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"
                  fill="none"
                  stroke="#e5e7eb"
                  stroke-width="2"
                />
                <path
                  d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"
                  fill="none"
                  stroke="#10b981"
                  stroke-width="2"
                  :stroke-dasharray="`${getCompletionRate()}, 100`"
                  class="transition-all duration-1000 ease-out"
                />
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                  <div class="text-2xl font-bold text-gray-900">{{ getCompletionRate() }}%</div>
                  <div class="text-xs text-gray-500">Complete</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Tasks completed today</span>
              <span class="text-sm font-semibold text-emerald-600">{{ getTasksCompletedToday() }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Average completion rate</span>
              <span class="text-sm font-semibold text-blue-600">{{ getAverageCompletionRate() }}%</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Most common priority</span>
              <span class="text-sm font-semibold text-amber-600">{{ getMostCommonPriority() }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <TaskFilters />

    <!-- Task List -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50">
      <div v-if="taskStore.loading" class="p-12 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
        <p class="mt-4 text-lg text-gray-600">Loading your tasks...</p>
      </div>
      <div v-else-if="!taskStore.filteredTasks || taskStore.filteredTasks.length === 0" class="p-12 text-center">
        <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
          <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No tasks found</h3>
        <p class="text-gray-600 mb-6">
          {{ taskStore.tasks && taskStore.tasks.length > 0 ? 'Try adjusting your filters above' : 'Get started by creating your first task!' }}
        </p>
        <div class="space-x-3">
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
import { ref, onMounted, computed } from 'vue'
import { useTaskStore } from '@/stores/tasks'
import { useAuthStore } from '@/stores/auth'
import TaskList from '@/components/tasks/TaskList.vue'
import TaskFilters from '@/components/tasks/TaskFilters.vue'
import TaskModal from '@/components/tasks/TaskModal.vue'

const taskStore = useTaskStore()
const authStore = useAuthStore()
const isAdmin = computed(() => authStore.isAdmin)
const showCreateModal = ref(false)
const editingTask = ref(null)

onMounted(async () => {
  if (isAdmin.value) return
  try {
    console.log('🚀 Starting to fetch tasks...')
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

// ...existing code for statistics and methods...
const getCompletionRate = () => {
  const total = taskStore.statistics?.total || 0
  const completed = taskStore.statistics?.completed || 0
  return total > 0 ? Math.round((completed / total) * 100) : 0
}
const getPendingPercentage = () => {
  const total = taskStore.statistics?.total || 0
  const pending = taskStore.statistics?.pending || 0
  return total > 0 ? Math.round((pending / total) * 100) : 0
}
const getHighPriorityPercentage = () => {
  const total = taskStore.statistics?.total || 0
  const high = taskStore.statistics?.by_priority?.high || 0
  return total > 0 ? Math.round((high / total) * 100) : 0
}
const getMediumPriorityPercentage = () => {
  const total = taskStore.statistics?.total || 0
  const medium = taskStore.statistics?.by_priority?.medium || 0
  return total > 0 ? Math.round((medium / total) * 100) : 0
}
const getLowPriorityPercentage = () => {
  const total = taskStore.statistics?.total || 0
  const low = taskStore.statistics?.by_priority?.low || 0
  return total > 0 ? Math.round((low / total) * 100) : 0
}
const getTasksChange = () => {
  return "No change from yesterday"
}
const getTasksCompletedToday = () => {
  return taskStore.statistics?.completed || 0
}
const getAverageCompletionRate = () => {
  return getCompletionRate()
}
const getMostCommonPriority = () => {
  const priorities = taskStore.statistics?.by_priority || {}
  const highest = Math.max(priorities.low || 0, priorities.medium || 0, priorities.high || 0)
  if (priorities.high === highest) return 'High'
  if (priorities.medium === highest) return 'Medium'
  if (priorities.low === highest) return 'Low'
  return 'None'
}
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
    await taskStore.fetchStatistics()
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
    await taskStore.fetchStatistics()
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
function clearAllFilters() {
  taskStore.clearFilters()
  console.log('🧹 All filters cleared manually')
  console.log('📋 Tasks after clear:', taskStore.filteredTasks)
}
function forceRefresh() {
  taskStore.fetchTasks()
  console.log('🔄 Force refresh triggered')
}
// ...existing code...
</script>
