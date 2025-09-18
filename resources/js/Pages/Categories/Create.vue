<template>
  <AuthenticatedLayout :user="data.user">
    <Head title="Create Category" />

    <div class="py-12">
      <div class="max-w-md mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Category</h3>
            <p class="text-sm text-gray-600 mb-6">
              Add a new category to organize your transactions better.
            </p>

            <form @submit.prevent="submit" class="space-y-4">
              <!-- Category Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                  Category Name
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                  placeholder="e.g., Groceries, Salary, Entertainment"
                  required
                />
                <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
              </div>

              <!-- Category Type -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Category Type
                </label>
                <div class="grid grid-cols-2 gap-2">
                  <button
                    type="button"
                    @click="form.type = 'income'"
                    :class="[
                      'p-3 rounded-md border-2 text-center font-medium transition-colors',
                      form.type === 'income'
                        ? 'border-green-500 bg-green-50 text-green-700'
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                    ]"
                  >
                    <div class="flex items-center justify-center space-x-2">
                      <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                      <span>Income</span>
                    </div>
                  </button>
                  <button
                    type="button"
                    @click="form.type = 'expense'"
                    :class="[
                      'p-3 rounded-md border-2 text-center font-medium transition-colors',
                      form.type === 'expense'
                        ? 'border-red-500 bg-red-50 text-red-700'
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                    ]"
                  >
                    <div class="flex items-center justify-center space-x-2">
                      <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                      <span>Expense</span>
                    </div>
                  </button>
                </div>
                <p v-if="form.errors.type" class="text-sm text-red-600 mt-1">{{ form.errors.type }}</p>
              </div>

              <!-- Expense Sub-type (only for expenses) -->
              <div v-if="form.type === 'expense'">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Budget Allocation (50/30/20 Rule)
                </label>
                <div class="grid grid-cols-3 gap-2">
                  <button
                    type="button"
                    @click="form.expense_type = 'needs'"
                    :class="[
                      'p-3 rounded-md border-2 text-center font-medium transition-colors',
                      form.expense_type === 'needs'
                        ? 'border-red-500 bg-red-50 text-red-700'
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                    ]"
                  >
                    <div class="text-center">
                      <div class="text-sm font-bold">50%</div>
                      <div class="text-xs">Needs</div>
                    </div>
                  </button>
                  <button
                    type="button"
                    @click="form.expense_type = 'wants'"
                    :class="[
                      'p-3 rounded-md border-2 text-center font-medium transition-colors',
                      form.expense_type === 'wants'
                        ? 'border-yellow-500 bg-yellow-50 text-yellow-700'
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                    ]"
                  >
                    <div class="text-center">
                      <div class="text-sm font-bold">30%</div>
                      <div class="text-xs">Wants</div>
                    </div>
                  </button>
                  <button
                    type="button"
                    @click="form.expense_type = 'savings'"
                    :class="[
                      'p-3 rounded-md border-2 text-center font-medium transition-colors',
                      form.expense_type === 'savings'
                        ? 'border-green-500 bg-green-50 text-green-700'
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                    ]"
                  >
                    <div class="text-center">
                      <div class="text-sm font-bold">20%</div>
                      <div class="text-xs">Savings</div>
                    </div>
                  </button>
                </div>
              </div>

              <!-- Color -->
              <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                  Color
                </label>
                <div class="flex space-x-2">
                  <input
                    id="color"
                    v-model="form.color"
                    type="color"
                    class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
                  />
                  <input
                    v-model="form.color"
                    type="text"
                    class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="#6b7280"
                  />
                </div>
                <p v-if="form.errors.color" class="text-sm text-red-600 mt-1">{{ form.errors.color }}</p>
              </div>

              <!-- Icon -->
              <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                  Icon (emoji or text)
                </label>
                <input
                  id="icon"
                  v-model="form.icon"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                  placeholder="🏠 or home"
                />
                <p v-if="form.errors.icon" class="text-sm text-red-600 mt-1">{{ form.errors.icon }}</p>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-3">
                <Link
                  href="/categories"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="processing"
                  class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  <span v-if="processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating...
                  </span>
                  <span v-else>Create Category</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  data: {
    user: Object
  }
})

// Form
const form = useForm({
  name: '',
  type: 'expense',
  expense_type: 'needs', // For expense categories
  color: '#6b7280',
  icon: '🏷️'
})

// Processing state
const processing = ref(false)

// Watch for type changes
watch(() => form.type, (newType) => {
  if (newType === 'income') {
    form.expense_type = null
  } else if (newType === 'expense') {
    form.expense_type = 'needs'
  }
})

// Submit form
const submit = () => {
  // Prepare the data for submission
  const submitData = {
    name: form.name,
    type: form.type === 'expense' ? form.expense_type : 'income',
    color: form.color,
    icon: form.icon
  }

  processing.value = true
  
  form.transform(() => submitData).post('/categories', {
    onSuccess: () => {
      // Redirect to categories index
      window.location.href = '/categories'
    },
    onError: () => {
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>