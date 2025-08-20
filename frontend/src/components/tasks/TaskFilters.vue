<!--
  TaskFilters.vue
  This component lets you filter your tasks by search, status, and priority.
  - The search box lets you type to find tasks by name or description.
  - The dropdowns let you filter by status (pending/completed) and priority (low/medium/high).
  - The clear button resets all filters.
-->
<template>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="flex flex-wrap gap-4 items-center">
      <!-- Search box for typing keywords to find tasks -->
      <div class="flex-1 min-w-[200px]">
        <input
          v-model="localFilters.search"
          type="text"
          placeholder="Search tasks..."
          class="input"
          @input="debouncedSearch"
        />
      </div>

      <!-- Dropdown to filter by status (pending/completed) -->
      <select
        v-model="localFilters.status"
        @change="applyFilters"
        class="input w-auto"
      >
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
      </select>

      <!-- Dropdown to filter by priority (low/medium/high) -->
      <select
        v-model="localFilters.priority"
        @change="applyFilters"
        class="input w-auto"
      >
        <option value="">All Priorities</option>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
      </select>

      <!-- Button to clear all filters and show all tasks -->
      <button
        @click="clearFilters"
        class="btn btn-secondary"
        :disabled="!hasActiveFilters"
      >
        Clear Filters
      </button>
    </div>
  </div>
</template>

<script setup>
// Import Vue features and the task store
import { ref, computed } from 'vue'
import { useTaskStore } from '@/stores/tasks'
import { useDebounceFn } from '@vueuse/core'

const taskStore = useTaskStore()

// Local filters for the search box and dropdowns
const localFilters = ref({
  search: taskStore.filters?.search || '',
  status: taskStore.filters?.status || '',
  priority: taskStore.filters?.priority || '',
})

// Checks if any filter is active (for disabling the clear button)
const hasActiveFilters = computed(() => {
  return localFilters.value.search ||
         localFilters.value.status ||
         localFilters.value.priority
})

// Debounce the search input so it doesn't trigger on every keystroke
const debouncedSearch = useDebounceFn(() => {
  applyFilters()
}, 300)

// Apply the selected filters to the task store
function applyFilters() {
  console.log('🔍 Applying filters:', localFilters.value) // ✅ Added logging
  taskStore.setFilter('search', localFilters.value.search)
  taskStore.setFilter('status', localFilters.value.status)
  taskStore.setFilter('priority', localFilters.value.priority)

  // ✅ FIXED: Don't fetch tasks again, just apply client-side filtering
  console.log('📊 Filtered tasks count:', taskStore.filteredTasks?.length)
}

// Clear all filters and reset the task list
function clearFilters() {
  console.log('🧹 Clearing filters') // ✅ Added logging
  localFilters.value = {
    search: '',
    status: '',
    priority: '',
  }
  taskStore.clearFilters()
  console.log('✅ Filters cleared, tasks count:', taskStore.filteredTasks?.length)
}
</script>
