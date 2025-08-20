<!-- src/views/auth/Register.vue -->
<!--
  This is the registration page for new users to create an account.
  It includes a form, error messages, and a link to the login page.
-->
<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
      <h2 class="text-3xl font-bold text-gray-900">Create an account</h2>
      <p class="mt-2 text-sm text-gray-600">
        Already have an account?
        <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500">
          Sign in
        </router-link>
      </p>
    </div>
    <!-- Form -->
    <div class="card">
      <form @submit.prevent="handleRegister" class="space-y-6">
        <!-- Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">
            Full name
          </label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="input mt-1"
            placeholder="John Doe"
          />
        </div>
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
            minlength="8"
            class="input mt-1"
            placeholder="••••••••"
          />
        </div>
        <!-- Confirm Password -->
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
            Confirm password
          </label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            required
            minlength="8"
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
          <span v-if="authStore.loading">Creating account...</span>
          <span v-else>Create account</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
// This script handles the registration logic and password validation
import { reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

async function handleRegister() {
  // Check if passwords match before sending to backend
  if (form.password !== form.password_confirmation) {
    authStore.error = 'Passwords do not match'
    return
  }
  try {
    await authStore.register(form)
  } catch (error) {
    console.error('Registration failed:', error)
  }
}
</script>
