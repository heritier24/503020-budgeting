<template>
  <div class="min-h-screen bg-primary-bg">
    <!-- Header -->
    <header class="bg-primary-surface border-b border-primary-border">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-primary-text">Budget Dashboard</h1>
            <p class="text-secondary-text">Track your {{ data.budget_config?.needs_percentage || 50 }}/{{ data.budget_config?.wants_percentage || 30 }}/{{ data.budget_config?.savings_percentage || 20 }} budget allocation</p>
          </div>
          <div class="flex items-center space-x-4">
                    <Link
                      href="/transactions/create"
                      class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                    >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add Transaction
            </Link>
          </div>
        </div>
      </div>
    </header>

    <!-- Quick Transaction Buttons -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Add Transactions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <!-- Quick Income Buttons -->
                  <Link
                    href="/transactions/create?type=income&description=Salary"
                    class="flex items-center justify-center p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors"
                  >
            <div class="text-center">
              <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-1">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <span class="text-sm font-medium text-green-700">Salary</span>
            </div>
          </Link>
          
                  <Link
                    href="/transactions/create?type=income&description=Freelance"
                    class="flex items-center justify-center p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors"
                  >
            <div class="text-center">
              <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-1">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <span class="text-sm font-medium text-green-700">Freelance</span>
            </div>
          </Link>
          
          <!-- Quick Expense Buttons -->
                  <Link
                    href="/transactions/create?type=expense&description=Food"
                    class="flex items-center justify-center p-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors"
                  >
            <div class="text-center">
              <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-1">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <span class="text-sm font-medium text-red-700">Food</span>
            </div>
          </Link>
          
                  <Link
                    href="/transactions/create?type=expense&description=Transport"
                    class="flex items-center justify-center p-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors"
                  >
            <div class="text-center">
              <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-1">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <span class="text-sm font-medium text-red-700">Transport</span>
            </div>
          </Link>
        </div>
        <div class="mt-3 text-center">
                  <Link
                    href="/transactions/create"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                  >
            + Add Custom Transaction
          </Link>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 p-6 rounded-lg">
          <h4 class="text-sm font-medium text-blue-600 mb-2">Monthly Income</h4>
          <p class="text-2xl font-bold text-blue-900">RWF {{ data.monthly_income?.toLocaleString() || '0' }}</p>
        </div>
        
        <div class="bg-orange-50 p-6 rounded-lg">
          <h4 class="text-sm font-medium text-orange-600 mb-2">Loan Payments</h4>
          <p class="text-2xl font-bold text-orange-900">RWF {{ data.loan_info?.total_monthly_payments?.toLocaleString() || '0' }}</p>
          <p class="text-xs text-orange-600 mt-1">{{ data.loan_info?.active_loans_count || 0 }} active loans</p>
        </div>
        
        <div class="bg-purple-50 p-6 rounded-lg">
          <h4 class="text-sm font-medium text-purple-600 mb-2">Available After Loans</h4>
          <p class="text-2xl font-bold text-purple-900">RWF {{ data.available_income?.toLocaleString() || '0' }}</p>
          <p class="text-xs text-purple-600 mt-1">For 50/30/20 budget</p>
        </div>
        
        <div class="bg-green-50 p-6 rounded-lg">
          <h4 class="text-sm font-medium text-green-600 mb-2">Remaining Budget</h4>
          <p class="text-2xl font-bold text-green-900">RWF {{ data.remaining_budget?.toLocaleString() || '0' }}</p>
        </div>
      </div>

      <!-- Loan Information Alert -->
      <div v-if="data.loan_info?.total_monthly_payments > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Loan Payments Impact</h3>
            <div class="mt-2 text-sm text-yellow-700">
              <p>You have {{ data.loan_info.active_loans_count }} active loan(s) with monthly payments of RWF {{ data.loan_info.total_monthly_payments.toLocaleString() }}. 
              Your 50/30/20 budget is calculated on the remaining RWF {{ data.available_income.toLocaleString() }} after loan payments.</p>
            </div>
            <div class="mt-3">
              <Link href="/loans" class="text-sm font-medium text-yellow-800 hover:text-yellow-900">
                Manage Loans →
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Goals Information -->
      <div v-if="data.goals_info?.active_goals_count > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">Active Financial Goals</h3>
              <div class="mt-2 text-sm text-blue-700">
                <p>You have {{ data.goals_info.active_goals_count }} active goal(s). 
                <span v-if="data.goals_info.savings_goals.count > 0">
                  {{ data.goals_info.savings_goals.count }} savings goal(s) with {{ data.goals_info.savings_goals.progress_percentage.toFixed(1) }}% progress.
                </span>
                <span v-if="data.goals_info.debt_payoff_goals.count > 0">
                  {{ data.goals_info.debt_payoff_goals.count }} debt payoff goal(s) with {{ data.goals_info.debt_payoff_goals.progress_percentage.toFixed(1) }}% progress.
                </span>
                </p>
              </div>
            </div>
          </div>
          <div class="flex space-x-3">
            <Link href="/goals" class="text-sm font-medium text-blue-800 hover:text-blue-900">
              View Goals →
            </Link>
            <Link href="/goals/create" class="text-sm font-medium text-blue-800 hover:text-blue-900">
              Add Goal →
            </Link>
          </div>
        </div>
      </div>

      <!-- Budget Status Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Needs -->
        <div class="card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-primary-text">Needs ({{ data.budget_allocation.needs.percentage }}%)</h3>
            <div class="w-8 h-8 bg-accent-danger rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-bold">{{ data.budget_allocation.needs.percentage }}</span>
            </div>
          </div>
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-secondary-text">Allocated:</span>
              <span class="text-primary-text font-medium">RWF {{ data.budget_allocation.needs.amount?.toLocaleString() || '0' }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-secondary-text">Spent:</span>
              <span class="text-primary-text font-medium">RWF {{ data.budget_allocation.needs.spent?.toLocaleString() || '0' }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-secondary-text">Remaining:</span>
              <span class="text-accent-success font-medium">RWF {{ ((data.budget_allocation.needs.amount || 0) - (data.budget_allocation.needs.spent || 0)).toLocaleString() }}</span>
            </div>
          </div>
          <div class="mt-4">
            <div class="w-full bg-secondary-surface rounded-full h-2">
              <div :class="`bg-${getStatusColor(needsStatus)} h-2 rounded-full`" :style="`width: ${needsUsagePercentage}%`"></div>
            </div>
            <p class="text-xs text-secondary-text mt-1">{{ needsUsagePercentage.toFixed(1) }}% used</p>
          </div>
        </div>

        <!-- Wants -->
        <div class="card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-primary-text">Wants ({{ data.budget_allocation.wants.percentage }}%)</h3>
            <div class="w-8 h-8 bg-accent-warning rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-bold">{{ data.budget_allocation.wants.percentage }}</span>
            </div>
          </div>
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-secondary-text">Allocated:</span>
              <span class="text-primary-text font-medium">RWF {{ data.budget_allocation.wants.amount?.toLocaleString() || '0' }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-secondary-text">Spent:</span>
              <span class="text-primary-text font-medium">RWF {{ data.budget_allocation.wants.spent?.toLocaleString() || '0' }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-secondary-text">Remaining:</span>
              <span class="text-accent-success font-medium">RWF {{ ((data.budget_allocation.wants.amount || 0) - (data.budget_allocation.wants.spent || 0)).toLocaleString() }}</span>
            </div>
          </div>
          <div class="mt-4">
            <div class="w-full bg-secondary-surface rounded-full h-2">
              <div :class="`bg-${getStatusColor(wantsStatus)} h-2 rounded-full`" :style="`width: ${wantsUsagePercentage}%`"></div>
            </div>
            <p class="text-xs text-secondary-text mt-1">{{ wantsUsagePercentage.toFixed(1) }}% used</p>
          </div>
        </div>

        <!-- Savings -->
        <div class="card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-primary-text">Savings ({{ data.budget_allocation.savings.percentage }}%)</h3>
            <div class="w-8 h-8 bg-accent-success rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-bold">{{ data.budget_allocation.savings.percentage }}</span>
            </div>
          </div>
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-secondary-text">Allocated:</span>
              <span class="text-primary-text font-medium">RWF {{ data.budget_allocation.savings.amount?.toLocaleString() || '0' }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-secondary-text">Spent:</span>
              <span class="text-primary-text font-medium">RWF {{ data.budget_allocation.savings.spent?.toLocaleString() || '0' }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-secondary-text">Remaining:</span>
              <span class="text-accent-success font-medium">RWF {{ ((data.budget_allocation.savings.amount || 0) - (data.budget_allocation.savings.spent || 0)).toLocaleString() }}</span>
            </div>
          </div>
          <div class="mt-4">
            <div class="w-full bg-secondary-surface rounded-full h-2">
              <div :class="`bg-${getStatusColor(savingsStatus)} h-2 rounded-full`" :style="`width: ${savingsUsagePercentage}%`"></div>
            </div>
            <p class="text-xs text-secondary-text mt-1">{{ savingsUsagePercentage.toFixed(1) }}% used</p>
          </div>
          
          <!-- Savings Goals Progress -->
          <div v-if="data.goals_info?.savings_goals?.count > 0" class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex justify-between items-center mb-2">
              <span class="text-xs text-secondary-text">Savings Goals</span>
              <span class="text-xs font-medium text-primary-text">{{ data.goals_info.savings_goals.progress_percentage.toFixed(1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
              <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-300" :style="`width: ${data.goals_info.savings_goals.progress_percentage}%`"></div>
            </div>
            <p class="text-xs text-secondary-text mt-1">
              RWF {{ data.goals_info.savings_goals.current_amount.toLocaleString() }} / {{ data.goals_info.savings_goals.target_amount.toLocaleString() }}
            </p>
          </div>
          
          <!-- Goal Contributions Impact -->
          <div v-if="data.goals_info?.total_contributions > 0" class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex justify-between items-center mb-2">
              <span class="text-xs text-secondary-text">Goal Contributions</span>
              <span class="text-xs font-medium text-orange-600">-RWF {{ data.goals_info.total_contributions.toLocaleString() }}</span>
            </div>
            <p class="text-xs text-orange-600 mt-1">
              Deducted from savings budget
            </p>
          </div>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="card">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-primary-text">Recent Transactions</h3>
          <button @click="emit('refresh')" class="text-secondary-brand hover:text-secondary-accent text-sm">
            Refresh
          </button>
          </div>
          
        <div v-if="data.recent_transactions && data.recent_transactions.length > 0" class="space-y-3">
          <div 
            v-for="transaction in data.recent_transactions" 
            :key="transaction.id"
            class="flex items-center justify-between p-3 bg-secondary-surface rounded-lg"
          >
            <div class="flex items-center space-x-3">
              <div :class="`w-3 h-3 rounded-full ${transaction.type === 'income' ? 'bg-accent-success' : 'bg-accent-danger'}`"></div>
              <div>
                <p class="text-primary-text font-medium">{{ transaction.description }}</p>
                <p class="text-secondary-text text-sm">{{ new Date(transaction.transaction_date).toLocaleDateString() }}</p>
              </div>
            </div>
            <div class="text-right">
              <p :class="`font-semibold ${transaction.type === 'income' ? 'text-accent-success' : 'text-accent-danger'}`">
                {{ transaction.type === 'income' ? '+' : '-' }}RWF {{ transaction.amount?.toLocaleString() }}
              </p>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-8">
          <p class="text-secondary-text">No transactions yet. Add your first transaction to get started!</p>
        </div>
      </div>
    </main>

  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

// Props
const props = defineProps<{
  data: {
    user: any
    budget_config: any
    income_sources: any[]
    monthly_income: number
    current_expenses: number
    current_income: number
    remaining_budget: number
    budget_allocation: {
      needs: { percentage: number; amount: number; spent: number }
      wants: { percentage: number; amount: number; spent: number }
      savings: { percentage: number; amount: number; spent: number }
    }
    recent_transactions: any[]
    categories: any[]
  }
}>()

// Emits
const emit = defineEmits<{
  refresh: []
}>()

// Component is now simplified - no modal needed

// Computed properties
const needsUsagePercentage = computed(() => {
  if (!props.data.budget_allocation.needs.amount) return 0
  return (props.data.budget_allocation.needs.spent / props.data.budget_allocation.needs.amount) * 100
})

const wantsUsagePercentage = computed(() => {
  if (!props.data.budget_allocation.wants.amount) return 0
  return (props.data.budget_allocation.wants.spent / props.data.budget_allocation.wants.amount) * 100
})

const savingsUsagePercentage = computed(() => {
  if (!props.data.budget_allocation.savings.amount) return 0
  return (props.data.budget_allocation.savings.spent / props.data.budget_allocation.savings.amount) * 100
})

const needsStatus = computed(() => {
  const percentage = needsUsagePercentage.value
  if (percentage >= 100) return 'over'
  if (percentage >= 80) return 'warning'
  return 'good'
})

const wantsStatus = computed(() => {
  const percentage = wantsUsagePercentage.value
  if (percentage >= 100) return 'over'
  if (percentage >= 80) return 'warning'
  return 'good'
})

const savingsStatus = computed(() => {
  const percentage = savingsUsagePercentage.value
  if (percentage >= 100) return 'over'
  if (percentage >= 80) return 'warning'
  return 'good'
})

// Methods
const getStatusColor = (status: string) => {
  switch (status) {
    case 'over':
      return 'accent-danger'
    case 'warning':
      return 'accent-warning'
    default:
      return 'accent-success'
  }
}

// Quick transaction buttons now use Link components for better navigation
</script>

<style scoped>
/* Custom styles if needed */
</style>