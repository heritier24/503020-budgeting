<template>
  <AuthenticatedLayout :user="user">
    <Head title="Transactions" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="mb-6">
              <div class="flex justify-between items-center">
                <div>
                  <h1 class="text-3xl font-bold text-gray-900">Transactions</h1>
                  <p class="text-gray-600 mt-2">Manage your income and expenses</p>
                </div>
                <Link 
                  href="/transactions/create"
                  class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
                >
                  Add Transaction
                </Link>
              </div>
            </div>

            <!-- Filters -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                  <select v-model="filters.type" @change="applyFilters" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Types</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                  <select v-model="filters.category_id" @change="applyFilters" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Categories</option>
                    <option v-for="category in filteredCategories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                  <input 
                    v-model="filters.date_from" 
                    @change="applyFilters"
                    type="date" 
                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                  >
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                  <input 
                    v-model="filters.date_to" 
                    @change="applyFilters"
                    type="date" 
                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                  >
                </div>
              </div>
            </div>

            <!-- Transactions List -->
            <div class="space-y-4">
              <div 
                v-for="transaction in transactions.data" 
                :key="transaction.id"
                class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-4">
                    <div :class="`w-3 h-3 rounded-full ${transaction.type === 'income' ? 'bg-green-500' : 'bg-red-500'}`"></div>
                    <div>
                      <h3 class="font-medium text-gray-900">{{ transaction.description }}</h3>
                      <p class="text-sm text-gray-500">{{ transaction.category?.name }} • {{ new Date(transaction.transaction_date).toLocaleDateString() }}</p>
                    </div>
                  </div>
                  
                  <div class="flex items-center space-x-4">
                    <div class="text-right">
                      <p :class="`font-semibold ${transaction.type === 'income' ? 'text-green-600' : 'text-red-600'}`">
                        {{ transaction.type === 'income' ? '+' : '-' }}RWF {{ transaction.amount?.toLocaleString() }}
                      </p>
                    </div>
                    
                    <div class="flex space-x-2">
                      <button 
                        @click="editTransaction(transaction)"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                      >
                        Edit
                      </button>
                      <button 
                        @click="deleteTransaction(transaction)"
                        class="text-red-600 hover:text-red-800 text-sm"
                      >
                        Delete
                      </button>
                    </div>
                  </div>
                </div>
                
                <div v-if="transaction.notes" class="mt-2 text-sm text-gray-600">
                  {{ transaction.notes }}
                </div>
              </div>
              
              <div v-if="transactions.data.length === 0" class="text-center py-8">
                <p class="text-gray-500">No transactions found. Add your first transaction to get started!</p>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="transactions.links" class="mt-6 flex justify-center">
              <nav class="flex space-x-2">
                <button 
                  v-for="link in transactions.links" 
                  :key="link.label"
                  @click="link.url && goToPage(link.url)"
                  :disabled="!link.url"
                  :class="[
                    'px-3 py-2 text-sm border rounded-md',
                    link.active 
                      ? 'bg-blue-600 text-white border-blue-600' 
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                    !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                  ]"
                  v-html="link.label"
                ></button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  user: Object,
  transactions: Object,
  categories: Array,
  filters: Object
})

// Component is now simplified - no modal needed
const filters = ref(props.filters || {})

// Computed properties
const filteredCategories = computed(() => {
  if (!filters.value.type) {
    return props.categories || []
  }
  
  // Filter categories based on selected transaction type
  return (props.categories || []).filter(category => {
    if (filters.value.type === 'income') {
      // For income transactions, show only income categories
      return category.type === 'income'
    } else if (filters.value.type === 'expense') {
      // For expense transactions, show needs/wants/savings categories (50/30/20)
      return ['needs', 'wants', 'savings'].includes(category.type)
    }
    return true
  })
})

// Watchers
watch(() => filters.value.type, (newType, oldType) => {
  if (newType !== oldType) {
    // Clear category filter when type changes
    filters.value.category_id = ''
  }
})

// Methods
const applyFilters = () => {
  router.get('/transactions', filters.value, {
    preserveState: true,
    replace: true
  })
}

const editTransaction = (transaction) => {
  // Redirect to edit page (we can create an edit page later if needed)
  // For now, just show an alert
  alert('Edit functionality will be implemented in the next update. Transaction ID: ' + transaction.id)
}

const deleteTransaction = (transaction) => {
  if (confirm('Are you sure you want to delete this transaction?')) {
    router.delete(`/transactions/${transaction.id}`, {
      onSuccess: () => {
        // Transaction deleted successfully
      }
    })
  }
}

// Modal functions removed - now using dedicated pages

const goToPage = (url) => {
  router.visit(url)
}
</script>