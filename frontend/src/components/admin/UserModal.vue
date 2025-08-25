<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
      <!-- Modal Header -->
      <div class="bg-indigo-600 text-white p-6 rounded-t-lg">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold">
            {{ modalTitle }}
          </h2>
          <button
            @click="$emit('close')"
            class="text-white hover:text-gray-300 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-6">
        <!-- View Mode -->
        <div v-if="mode === 'view'" class="space-y-4">
          <div class="text-center mb-6">
            <div v-if="user.avatar" class="mx-auto h-20 w-20 rounded-full overflow-hidden mb-4">
              <img :src="user.avatar" :alt="user.name" class="h-full w-full object-cover">
            </div>
            <div v-else class="mx-auto h-20 w-20 bg-indigo-600 rounded-full flex items-center justify-center mb-4">
              <span class="text-white font-bold text-2xl">{{ getInitials(user.name) }}</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900">{{ user.name }}</h3>
            <p class="text-gray-600">{{ user.email }}</p>
            <span 
              :class="[
                'inline-flex px-3 py-1 text-sm font-semibold rounded-full mt-2',
                user.is_admin ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
              ]"
            >
              {{ user.is_admin ? 'Administrator' : 'Regular User' }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
              <p class="text-2xl font-bold text-gray-900">{{ user.tasks_count || 0 }}</p>
              <p class="text-sm text-gray-600">Total Tasks</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
              <p class="text-2xl font-bold text-green-600">{{ user.completed_tasks_count || 0 }}</p>
              <p class="text-sm text-gray-600">Completed</p>
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Member Since:</span>
              <span class="font-medium">{{ formatDate(user.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Last Updated:</span>
              <span class="font-medium">{{ formatDate(user.updated_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">User ID:</span>
              <span class="font-medium">#{{ user.id }}</span>
            </div>
          </div>
        </div>

        <!-- Add/Edit Mode -->
        <form v-else @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Name Field -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
              Full Name <span class="text-red-500">*</span>
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              :class="{ 'border-red-300': errors.name }"
            >
            <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
          </div>

          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
              Email Address <span class="text-red-500">*</span>
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              :class="{ 'border-red-300': errors.email }"
            >
            <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
          </div>

          <!-- Password Field (Add mode only) -->
          <div v-if="mode === 'add'">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
              Password <span class="text-red-500">*</span>
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              minlength="8"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              :class="{ 'border-red-300': errors.password }"
            >
            <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
            <p class="text-gray-500 text-sm mt-1">Password must be at least 8 characters long</p>
          </div>

          <!-- Password Confirmation (Add mode only) -->
          <div v-if="mode === 'add'">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
              Confirm Password <span class="text-red-500">*</span>
            </label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              :class="{ 'border-red-300': errors.password_confirmation }"
            >
            <p v-if="errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ errors.password_confirmation }}</p>
          </div>

          <!-- Admin Role Toggle -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <label for="is_admin" class="text-sm font-medium text-gray-700">
                Administrator Privileges
              </label>
              <p class="text-xs text-gray-500">Grant admin access to this user</p>
            </div>
            <div class="flex items-center">
              <input
                id="is_admin"
                v-model="form.is_admin"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              >
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50"
            >
              {{ saving ? 'Saving...' : (mode === 'add' ? 'Create User' : 'Update User') }}
            </button>
          </div>
        </form>
      </div>

      <!-- Action Buttons for View Mode -->
      <div v-if="mode === 'view'" class="bg-gray-50 px-6 py-4 rounded-b-lg">
        <div class="flex justify-end space-x-3">
          <button
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Close
          </button>
          <button
            @click="switchToEditMode"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          >
            Edit User
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const props = defineProps({
  user: {
    type: Object,
    default: null
  },
  mode: {
    type: String,
    required: true,
    validator: value => ['view', 'edit', 'add'].includes(value)
  }
})

const emit = defineEmits(['close', 'save'])

// Reactive data
const saving = ref(false)
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  is_admin: false
})
const errors = ref({})

// Computed properties
const modalTitle = computed(() => {
  switch (props.mode) {
    case 'add': return 'Add New User'
    case 'edit': return 'Edit User'
    case 'view': return 'User Details'
    default: return 'User'
  }
})

// Methods
const initializeForm = () => {
  if (props.user && (props.mode === 'edit' || props.mode === 'view')) {
    form.value = {
      name: props.user.name,
      email: props.user.email,
      password: '',
      password_confirmation: '',
      is_admin: props.user.is_admin || false
    }
  } else {
    form.value = {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      is_admin: false
    }
  }
  errors.value = {}
}

const validateForm = () => {
  errors.value = {}
  let isValid = true

  // Name validation
  if (!form.value.name || form.value.name.trim().length < 2) {
    errors.value.name = 'Name must be at least 2 characters long'
    isValid = false
  }

  // Email validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!form.value.email || !emailRegex.test(form.value.email)) {
    errors.value.email = 'Please enter a valid email address'
    isValid = false
  }

  // Password validation (only for add mode)
  if (props.mode === 'add') {
    if (!form.value.password || form.value.password.length < 8) {
      errors.value.password = 'Password must be at least 8 characters long'
      isValid = false
    }

    if (form.value.password !== form.value.password_confirmation) {
      errors.value.password_confirmation = 'Passwords do not match'
      isValid = false
    }
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  try {
    saving.value = true
    
    const userData = {
      name: form.value.name,
      email: form.value.email,
      is_admin: form.value.is_admin
    }

    // Add password for new users
    if (props.mode === 'add') {
      userData.password = form.value.password
      userData.password_confirmation = form.value.password_confirmation
    }

    emit('save', userData)
  } catch (error) {
    console.error('Error submitting form:', error)
  } finally {
    saving.value = false
  }
}

const switchToEditMode = () => {
  // This would need to be handled by the parent component
  emit('close')
  // Parent should then open modal in edit mode
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Watch for prop changes
watch(() => props.user, () => {
  initializeForm()
}, { immediate: true })

watch(() => props.mode, () => {
  initializeForm()
})

// Initialize form on mount
onMounted(() => {
  initializeForm()
})
</script>
