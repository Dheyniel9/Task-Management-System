<!-- src/App.vue -->
<template>
  <router-view />
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useTaskStore } from '@/stores/tasks'
import echo from '@/services/echo'

const authStore = useAuthStore()
const taskStore = useTaskStore()

onMounted(() => {
  // Setup WebSocket listeners if authenticated
  if (authStore.isAuthenticated && authStore.user) {
    setupWebSocketListeners()
  }
})

function setupWebSocketListeners() {
  const userId = authStore.user.id

  // Listen to user-specific channel
  echo.private(`user.${userId}`)
    .listen('TaskCreated', (e) => {
      taskStore.handleTaskCreated(e.task)
    })
    .listen('TaskUpdated', (e) => {
      taskStore.handleTaskUpdated(e.task)
    })
    .listen('TaskDeleted', (e) => {
      taskStore.handleTaskDeleted(e.task_id)
    })
    .listen('TasksReordered', (e) => {
      taskStore.handleTasksReordered(e.task_orders)
    })

  // Listen to admin channel if admin
  if (authStore.isAdmin) {
    echo.private('admin')
      .listen('SystemUpdate', (e) => {
        console.log('System update:', e)
      })
  }
}
</script>
