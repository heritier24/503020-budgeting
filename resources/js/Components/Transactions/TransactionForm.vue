<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-secondary-brand/10 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-secondary-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-primary-text">
                {{ isEditing ? 'Edit Transaction' : 'Add New Transaction' }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-secondary-text">
                  {{ isEditing ? 'Update your transaction details.' : 'Record a new expense or income.' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Transaction Form -->
          <form @submit.prevent="handleSubmit" class="mt-6 space-y-4">
            <!-- Transaction Type -->
            <div>
              <label class="block text-sm font-medium text-primary-text mb-2">
                Transaction Type
              </label>
              <div class="grid grid-cols-2 gap-2">
                <button 
                  @click="form.type = 'income'"
                  :class="[
                    'w-full p-3 rounded-md border-2 text-center font-medium transition-colors',
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
                  @click="form.type = 'expense'"
                  :class="[
                    'w-full p-3 rounded-md border-2 text-center font-medium transition-colors',
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
            </div>

            <!-- Category -->
            <div>
              <label class="block text-sm font-medium text-primary-text mb-2">
                Category
              </label>
              <select 
                v-model="form.category_id" 
                class="w-full border border-primary-border rounded-md px-3 py-2 focus:border-secondary-brand focus:ring-secondary-brand"
                required
              >
                <option value="">Select category</option>
                <option v-for="category in filteredCategories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>

            <!-- Amount -->
            <div>
              <label class="block text-sm font-medium text-primary-text mb-2">
                Amount (RWF)
              </label>
              <div class="relative">
                <input
                  v-model="form.amount"
                  type="number"
                  step="100"
                  min="0"
                  class="w-full border border-primary-border rounded-md px-3 py-2 focus:border-secondary-brand focus:ring-secondary-brand"
                  placeholder="Enter amount"
                  required
                />
              </div>
              
              <!-- Quick Amount Buttons -->
              <div class="mt-2">
                <p class="text-xs text-gray-500 mb-2">Quick amounts:</p>
                <div class="grid grid-cols-4 gap-2">
                  <button 
                    v-for="amount in quickAmounts" 
                    :key="amount"
                    @click="form.amount = amount"
                    class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded border"
                  >
                    {{ amount.toLocaleString() }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-primary-text mb-2">
                Description
              </label>
              <input
                v-model="form.description"
                type="text"
                class="w-full border border-primary-border rounded-md px-3 py-2 focus:border-secondary-brand focus:ring-secondary-brand"
                placeholder="Enter description"
                required
              />
            </div>

            <!-- Date -->
            <div>
              <label class="block text-sm font-medium text-primary-text mb-2">
                Date
              </label>
              <input
                v-model="form.transaction_date"
                type="date"
                class="w-full border border-primary-border rounded-md px-3 py-2 focus:border-secondary-brand focus:ring-secondary-brand"
                required
              />
            </div>

            <!-- Notes -->
            <div>
              <label class="block text-sm font-medium text-primary-text mb-2">
                Notes (Optional)
              </label>
              <textarea
                v-model="form.notes"
                rows="3"
                class="w-full border border-primary-border rounded-md px-3 py-2 focus:border-secondary-brand focus:ring-secondary-brand"
                placeholder="Additional notes..."
              ></textarea>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center py-4">
              <svg class="animate-spin h-5 w-5 text-secondary-brand mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-sm text-secondary-text">{{ isEditing ? 'Updating...' : 'Saving...' }}</span>
            </div>

            <!-- Success State -->
            <div v-if="success" class="p-4 bg-accent-success/10 border border-accent-success/20 rounded-lg">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-accent-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-sm font-medium text-accent-success">
                  {{ isEditing ? 'Transaction updated successfully!' : 'Transaction added successfully!' }}
                </span>
              </div>
            </div>

            <!-- Error State -->
            <div v-if="error" class="p-4 bg-accent-danger/10 border border-accent-danger/20 rounded-lg">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-accent-danger mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="text-sm font-medium text-accent-danger">{{ error }}</span>
              </div>
            </div>
          </form>
        </div>

        <!-- Modal Actions -->
        <div class="bg-secondary-surface px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            v-if="!success"
            type="button"
            @click="handleSubmit"
            :disabled="loading"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-secondary-brand text-base font-medium text-white hover:bg-secondary-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-brand sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isEditing ? 'Update Transaction' : 'Add Transaction' }}
          </button>
          <button
            type="button"
            @click="closeModal"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-primary-border shadow-sm px-4 py-2 bg-white text-base font-medium text-secondary-text hover:bg-secondary-surface focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-brand sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            {{ success ? 'Close' : 'Cancel' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'

// Props
const props = defineProps<{
  isOpen: boolean
  transaction?: any
  categories?: any[]
}>()

// Emits
const emit = defineEmits<{
  close: []
  success: []
}>()

// Reactive data
const form = ref({
  type: 'expense',
  category_id: '',
  amount: '',
  description: '',
  transaction_date: new Date().toISOString().split('T')[0],
  notes: ''
})

const loading = ref(false)
const success = ref(false)
const error = ref('')
const isEditing = ref(false)

// Quick amount buttons for common values
const quickAmounts = ref([1000, 2000, 5000, 10000, 20000, 50000, 100000, 200000])

// Computed properties
const filteredCategories = computed(() => {
  if (!form.value.type) {
    return props.categories || []
  }
  
  // Filter categories based on selected transaction type
  return (props.categories || []).filter(category => {
    if (form.value.type === 'income') {
      // For income transactions, show only income categories
      return category.type === 'income'
    } else if (form.value.type === 'expense') {
      // For expense transactions, show needs/wants/savings categories (50/30/20)
      return ['needs', 'wants', 'savings'].includes(category.type)
    }
    return true
  })
})

// Methods
const closeModal = () => {
  emit('close')
  resetForm()
}

const resetForm = () => {
  form.value = {
    type: 'expense',
    category_id: '',
    amount: '',
    description: '',
    transaction_date: new Date().toISOString().split('T')[0],
    notes: ''
  }
  loading.value = false
  success.value = false
  error.value = ''
  isEditing.value = false
}

// Categories are now passed as props

const handleSubmit = () => {
  if (!form.value.type || !form.value.category_id || !form.value.amount || !form.value.description) {
    error.value = 'Please fill in all required fields'
    return
  }

  loading.value = true
  error.value = ''

  const formData = {
    ...form.value,
    amount: parseFloat(form.value.amount),
    currency: 'RWF',
  }

  const url = isEditing.value ? `/transactions/${props.transaction.id}` : '/transactions'
  const method = isEditing.value ? 'put' : 'post'

  router[method](url, formData, {
    onSuccess: () => {
      success.value = true
      setTimeout(() => {
        emit('success')
        closeModal()
      }, 1500)
    },
    onError: (errors) => {
      error.value = Object.values(errors).join(', ') || 'Failed to save transaction'
    },
    onFinish: () => {
      loading.value = false
    }
  })
}

// Watch for modal open/transaction prop changes
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    if (props.transaction) {
      // Editing mode
      isEditing.value = true
      form.value = {
        type: props.transaction.type,
        category_id: props.transaction.category_id,
        amount: props.transaction.amount.toString(),
        description: props.transaction.description,
        transaction_date: props.transaction.transaction_date,
        notes: props.transaction.notes || ''
      }
    } else {
      // Adding mode
      isEditing.value = false
      resetForm()
    }
  }
})

// Watch for type changes to clear category
watch(() => form.value.type, (newType, oldType) => {
  if (newType !== oldType) {
    // Clear category when type changes
    form.value.category_id = ''
  }
})

// Categories are passed as props, no need to fetch
</script>

<style scoped>
/* Modal styles */
</style>