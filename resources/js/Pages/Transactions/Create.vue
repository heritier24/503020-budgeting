<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Add New Transaction
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            
            <!-- Back Button -->
            <div class="mb-6">
              <Link 
                :href="route('dashboard')" 
                class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Dashboard
              </Link>
            </div>

            <!-- Transaction Form -->
            <form @submit.prevent="submit" class="space-y-6">
              
              <!-- Transaction Type -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Transaction Type
                </label>
                <div class="grid grid-cols-2 gap-4">
                  <button 
                    type="button"
                    @click="form.type = 'income'"
                    :class="[
                      'p-4 rounded-lg border-2 text-center font-medium transition-all duration-200',
                      form.type === 'income' 
                        ? 'border-green-500 bg-green-50 text-green-700 shadow-md' 
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                    ]"
                  >
                    <div class="flex flex-col items-center space-y-2">
                      <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                      </div>
                      <span class="font-semibold">Income</span>
                      <span class="text-xs text-gray-500">Money coming in</span>
                    </div>
                  </button>
                  
                  <button 
                    type="button"
                    @click="form.type = 'expense'"
                    :class="[
                      'p-4 rounded-lg border-2 text-center font-medium transition-all duration-200',
                      form.type === 'expense' 
                        ? 'border-red-500 bg-red-50 text-red-700 shadow-md' 
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                    ]"
                  >
                    <div class="flex flex-col items-center space-y-2">
                      <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                      </div>
                      <span class="font-semibold">Expense</span>
                      <span class="text-xs text-gray-500">Money going out</span>
                    </div>
                  </button>
                </div>
                <div v-if="errors.type" class="mt-2 text-sm text-red-600">
                  {{ errors.type }}
                </div>
              </div>

              <!-- Category -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Category
                </label>
                <select 
                  v-model="form.category_id" 
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                  required
                >
                  <option value="">Select a category</option>
                  <option v-for="category in filteredCategories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                <div v-if="errors.category_id" class="mt-2 text-sm text-red-600">
                  {{ errors.category_id }}
                </div>
              </div>

              <!-- Amount -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Amount (RWF)
                </label>
                <div class="relative">
                  <input
                    v-model="form.amount"
                    type="number"
                    step="100"
                    min="0"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Enter amount"
                    required
                  />
                </div>
                
                <!-- Quick Amount Buttons -->
                <div class="mt-3">
                  <p class="text-xs text-gray-500 mb-2">Quick amounts:</p>
                  <div class="grid grid-cols-4 gap-2">
                    <button 
                      v-for="amount in quickAmounts" 
                      :key="amount"
                      type="button"
                      @click="form.amount = amount"
                      class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded border transition-colors"
                    >
                      {{ amount.toLocaleString() }}
                    </button>
                  </div>
                </div>
                <div v-if="errors.amount" class="mt-2 text-sm text-red-600">
                  {{ errors.amount }}
                </div>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Description
                </label>
                <input
                  v-model="form.description"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                  placeholder="What is this transaction for?"
                  required
                />
                <div v-if="errors.description" class="mt-2 text-sm text-red-600">
                  {{ errors.description }}
                </div>
              </div>

              <!-- Date -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Date
                </label>
                <input
                  v-model="form.transaction_date"
                  type="date"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                  required
                />
                <div v-if="errors.transaction_date" class="mt-2 text-sm text-red-600">
                  {{ errors.transaction_date }}
                </div>
              </div>

              <!-- Notes -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Notes (Optional)
                </label>
                <textarea
                  v-model="form.notes"
                  rows="3"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                  placeholder="Additional notes about this transaction..."
                ></textarea>
                <div v-if="errors.notes" class="mt-2 text-sm text-red-600">
                  {{ errors.notes }}
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-between pt-6">
                <Link 
                  :href="route('dashboard')" 
                  class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors"
                >
                  Cancel
                </Link>
                
                <button
                  type="submit"
                  :disabled="processing"
                  class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  <span v-if="processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                  </span>
                  <span v-else>Save Transaction</span>
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
import { ref, watch, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  categories: Array
})

// Get URL parameters for pre-filling form
const urlParams = new URLSearchParams(window.location.search)

// Form
const form = useForm({
  type: urlParams.get('type') || 'expense',
  category_id: '',
  amount: urlParams.get('amount') || '',
  description: urlParams.get('description') || '',
  transaction_date: new Date().toISOString().split('T')[0],
  notes: ''
})

// Quick amount buttons
const quickAmounts = ref([1000, 2000, 5000, 10000, 20000, 50000, 100000, 200000])

// Computed properties
const filteredCategories = computed(() => {
  if (!form.type) {
    return props.categories || []
  }
  
  // Filter categories based on selected transaction type
  return (props.categories || []).filter(category => {
    if (form.type === 'income') {
      // For income transactions, show only income categories
      return category.type === 'income'
    } else if (form.type === 'expense') {
      // For expense transactions, show needs/wants/savings categories (50/30/20)
      return ['needs', 'wants', 'savings'].includes(category.type)
    }
    return true
  })
})

// Submit form
const submit = () => {
  form.post('/transactions', {
    onSuccess: () => {
      // Redirect to dashboard after successful save
      window.location.href = '/dashboard'
    }
  })
}

// Computed
const processing = ref(false)
const errors = ref({})

// Watch for form processing state
watch(() => form.processing, (newValue) => {
  processing.value = newValue
})

// Watch for form errors
watch(() => form.errors, (newErrors) => {
  errors.value = newErrors
})

// Watch for type changes to clear category
watch(() => form.type, (newType, oldType) => {
  if (newType !== oldType) {
    // Clear category when type changes
    form.category_id = ''
  }
})
</script>