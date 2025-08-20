<!-- src/views/auth/Login.vue -->
<!--
  This is the login page for users to sign in.
  It includes a form, error messages, and demo credential buttons for quick testing.
-->
<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
      <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
      <p class="mt-2 text-sm text-gray-600">
        Don't have an account?
        <router-link to="/register" class="font-medium text-primary-600 hover:text-primary-500">
          Sign up
        </router-link>
      </p>
    </div>
    <!-- Form -->
    <div class="card">
      <form @submit.prevent="handleLogin" class="space-y-6">
        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Email address
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="input mt-1"
            placeholder="john@example.com"
          />
        </div>
        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">
            Password
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="input mt-1"
            placeholder="••••••••"
          />
        </div>
        <!-- Error message -->
        <div v-if="authStore.error" class="rounded-md bg-red-50 p-4">
          <p class="text-sm text-red-800">{{ authStore.error }}</p>
        </div>
        <!-- Submit button -->
        <button
          type="submit"
          :disabled="authStore.loading"
          class="w-full btn btn-primary"
        >
          <span v-if="authStore.loading">Signing in...</span>
          <span v-else>Sign in</span>
        </button>
      </form>
      <!-- Demo credentials -->
      <div class="mt-6 pt-6 border-t">
        <p class="text-sm text-gray-600 mb-3">Demo Credentials:</p>
        <div class="space-y-2 text-sm">
          <button
            @click="fillDemoUser"
            class="w-full text-left px-3 py-2 bg-gray-50 rounded hover:bg-gray-100"
          >
            <span class="font-medium">Regular User:</span> demo@example.com / demo123
          </button>
          <button
            @click="fillAdminUser"
            class="w-full text-left px-3 py-2 bg-gray-50 rounded hover:bg-gray-100"
          >
            <span class="font-medium">Admin User:</span> admin@example.com / password
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// This script handles the login logic and demo credential autofill
import { reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

async function handleLogin() {
  try {
    await authStore.login(form)
  } catch (error) {
    console.error('Login failed:', error)
  }
}

function fillDemoUser() {
  form.email = 'demo@example.com'
  form.password = 'demo123'
}

function fillAdminUser() {
  form.email = 'admin@example.com'
  form.password = 'password'
}
</script>
