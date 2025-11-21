<template>
  <AuthenticatedLayout :user="user">
    <SeoHead
      title="Transactions"
      description="View and manage all your financial transactions. Track income and expenses, filter by date, category, and type. Keep a complete record of your financial activity."
      keywords="transactions, expense tracking, income records, financial transactions, transaction history, money management, expense log"
    />

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
                  class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
                >
                  Add Transaction
                </Link>
              </div>
            </div>

            <!-- Filters -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
              <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                  <select v-model="filters.type" @change="applyFilters" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Types</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Category Type</label>
                  <select v-model="filters.category_type" @change="applyFilters" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Types</option>
                    <option value="needs">Needs</option>
                    <option value="wants">Wants</option>
                    <option value="savings">Savings</option>
                    <option value="income">Income</option>
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

            <!-- Top 5 Category Overspent -->
            <div v-if="topCategoryOverspent && topCategoryOverspent.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
              <h3 class="text-lg font-semibold text-blue-900 mb-4">Top 5 Category Overspent</h3>
              <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div 
                  v-for="category in topCategoryOverspent" 
                  :key="category.name"
                  class="text-center bg-white rounded-lg p-4 border border-blue-100"
                >
                  <div :class="`w-3 h-3 rounded-full mx-auto mb-2 ${getCategoryColor(category.type)}`"></div>
                  <p class="text-sm font-medium text-gray-900 mb-1">{{ category.name }}</p>
                  <p class="text-lg font-bold text-blue-600">{{ category.percentage.toFixed(1) }}%</p>
                  <p class="text-xs text-gray-500">{{ category.count }} transactions</p>
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
                  <div class="flex items-center space-x-4 flex-1">
                    <div :class="`w-3 h-3 rounded-full ${transaction.type === 'income' ? 'bg-green-500' : 'bg-red-500'}`"></div>
                    <div class="flex-1">
                      <h3 class="font-medium text-gray-900">{{ transaction.description }}</h3>
                      <p class="text-sm text-gray-500">{{ transaction.category?.name }} • {{ new Date(transaction.transaction_date).toLocaleDateString() }}</p>
                    </div>
                  </div>
                  
                  <!-- Progress Bar Column -->
                  <div class="w-32 mx-4">
                    <div class="text-xs text-gray-500 mb-1">{{ getCategoryPercentage(transaction.category?.id) }}%</div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div 
                        class="h-2 rounded-full transition-all duration-300" 
                        :class="getCategoryColor(transaction.category?.type)"
                        :style="`width: ${getCategoryPercentage(transaction.category?.id)}%`"
                      ></div>
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
                      ? 'bg-gray-800 text-white border-gray-800' 
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
import { Link, router } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  user: Object,
  transactions: Object,
  categories: Array,
  groupedCategories: Object,
  topCategoryOverspent: Array,
  filters: Object
})

// Component is now simplified - no modal needed
const filters = ref(props.filters || {})

// Computed properties
const filteredCategories = computed(() => {
  if (!filters.value.type && !filters.value.category_type) {
    return props.categories || []
  }
  
  let categories = props.categories || []
  
  // Filter by transaction type
  if (filters.value.type) {
    if (filters.value.type === 'income') {
      categories = categories.filter(cat => cat.type === 'income')
    } else if (filters.value.type === 'expense') {
      categories = categories.filter(cat => ['needs', 'wants', 'savings'].includes(cat.type))
    }
  }
  
  // Filter by category type
  if (filters.value.category_type) {
    categories = categories.filter(cat => cat.type === filters.value.category_type)
  }
  
  return categories
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

// Calculate category percentage for progress bars
const getCategoryPercentage = (categoryId) => {
  if (!categoryId || !props.topCategoryOverspent) return 0
  
  const category = props.topCategoryOverspent.find(cat => {
    // Find category by matching with transactions
    const categoryTransactions = props.transactions.data.filter(t => t.category?.id === categoryId)
    return categoryTransactions.length > 0
  })
  
  if (!category) return 0
  
  // Calculate percentage based on total transactions in this category
  const totalTransactionsInCategory = props.transactions.data.filter(t => t.category?.id === categoryId).length
  const totalTransactions = props.transactions.data.length
  
  return totalTransactions > 0 ? (totalTransactionsInCategory / totalTransactions) * 100 : 0
}

// Get category color based on type
const getCategoryColor = (categoryType) => {
  switch (categoryType) {
    case 'needs':
      return 'bg-red-500'
    case 'wants':
      return 'bg-yellow-500'
    case 'savings':
      return 'bg-green-500'
    case 'income':
      return 'bg-blue-500'
    default:
      return 'bg-gray-500'
  }
}
</script>