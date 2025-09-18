<template>
  <AuthenticatedLayout :user="data.user">
    <Head title="Analytics" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="mb-6">
              <h1 class="text-3xl font-bold text-gray-900">Analytics & Reports</h1>
              <p class="text-gray-600 mt-2">Track your spending patterns and budget performance.</p>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
              <!-- Budget Allocation Chart -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget Allocation</h3>
                <BudgetPieChart :data="data.budget_allocation" />
              </div>

              <!-- Spending Trends Chart -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Spending Trends (Last 6 Months)</h3>
                <SpendingTrendsChart :data="data.spending_trends" />
              </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
              <div class="bg-blue-50 p-6 rounded-lg">
                <h4 class="text-sm font-medium text-blue-600 mb-2">Monthly Income</h4>
                <p class="text-2xl font-bold text-blue-900">RWF {{ data.monthly_income?.toLocaleString() }}</p>
              </div>
              
              <div class="bg-green-50 p-6 rounded-lg">
                <h4 class="text-sm font-medium text-green-600 mb-2">Needs Budget</h4>
                <p class="text-2xl font-bold text-green-900">RWF {{ data.budget_allocation.needs.amount?.toLocaleString() }}</p>
                <p class="text-sm text-green-600">{{ data.budget_allocation.needs.percentage }}% of income</p>
              </div>
              
              <div class="bg-orange-50 p-6 rounded-lg">
                <h4 class="text-sm font-medium text-orange-600 mb-2">Wants Budget</h4>
                <p class="text-2xl font-bold text-orange-900">RWF {{ data.budget_allocation.wants.amount?.toLocaleString() }}</p>
                <p class="text-sm text-orange-600">{{ data.budget_allocation.wants.percentage }}% of income</p>
              </div>
              
              <div class="bg-purple-50 p-6 rounded-lg">
                <h4 class="text-sm font-medium text-purple-600 mb-2">Savings Target</h4>
                <p class="text-2xl font-bold text-purple-900">RWF {{ data.budget_allocation.savings.amount?.toLocaleString() }}</p>
                <p class="text-sm text-purple-600">{{ data.budget_allocation.savings.percentage }}% of income</p>
              </div>
            </div>

            <!-- Budget Health & Goals Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
              <!-- Budget Health -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget Health</h3>
                <div class="text-center">
                  <div class="text-4xl font-bold mb-2" :class="getHealthColor(data.insights.budget_health.status)">
                    {{ data.insights.budget_health.score.toFixed(0) }}%
                  </div>
                  <p class="text-sm text-gray-600 mb-4">{{ data.insights.budget_health.message }}</p>
                  
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <span>Needs:</span>
                      <span class="font-medium">RWF {{ data.budget_allocation.needs.spent?.toLocaleString() }} / {{ data.budget_allocation.needs.amount?.toLocaleString() }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Wants:</span>
                      <span class="font-medium">RWF {{ data.budget_allocation.wants.spent?.toLocaleString() }} / {{ data.budget_allocation.wants.amount?.toLocaleString() }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Savings:</span>
                      <span class="font-medium">RWF {{ data.budget_allocation.savings.spent?.toLocaleString() }} / {{ data.budget_allocation.savings.amount?.toLocaleString() }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Goals Progress -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Goals Progress</h3>
                <div v-if="data.goals_info.active_goals_count > 0" class="space-y-4">
                  <div v-if="data.goals_info.savings_goals.count > 0">
                    <div class="flex justify-between items-center mb-2">
                      <span class="text-sm font-medium text-gray-700">Savings Goals</span>
                      <span class="text-sm text-gray-600">{{ data.goals_info.savings_goals.progress_percentage.toFixed(1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" :style="`width: ${data.goals_info.savings_goals.progress_percentage}%`"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                      RWF {{ data.goals_info.savings_goals.current_amount.toLocaleString() }} / {{ data.goals_info.savings_goals.target_amount.toLocaleString() }}
                    </p>
                  </div>
                  
                  <div v-if="data.goals_info.debt_payoff_goals.count > 0">
                    <div class="flex justify-between items-center mb-2">
                      <span class="text-sm font-medium text-gray-700">Debt Payoff Goals</span>
                      <span class="text-sm text-gray-600">{{ data.goals_info.debt_payoff_goals.progress_percentage.toFixed(1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div class="bg-red-500 h-2 rounded-full transition-all duration-300" :style="`width: ${data.goals_info.debt_payoff_goals.progress_percentage}%`"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                      RWF {{ data.goals_info.debt_payoff_goals.current_amount.toLocaleString() }} / {{ data.goals_info.debt_payoff_goals.target_amount.toLocaleString() }}
                    </p>
                  </div>
                  
                  <div class="pt-2 border-t border-gray-200">
                    <Link href="/goals" class="text-sm text-blue-600 hover:text-blue-800">View All Goals →</Link>
                  </div>
                </div>
                <div v-else class="text-center text-gray-500">
                  <p class="text-sm">No active goals yet</p>
                  <Link href="/goals/create" class="text-sm text-blue-600 hover:text-blue-800">Create your first goal →</Link>
                </div>
              </div>
            </div>

            <!-- Category Spending Insights -->
            <div v-if="data.category_spending && data.category_spending.length > 0" class="bg-gray-50 p-6 rounded-lg mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Spending Categories</h3>
              <div class="space-y-3">
                <div 
                  v-for="category in data.category_spending.slice(0, 5)" 
                  :key="category.name"
                  class="flex items-center justify-between p-3 bg-white rounded-lg"
                >
                  <div class="flex items-center space-x-3">
                    <div :class="`w-3 h-3 rounded-full ${getCategoryColor(category.type)}`"></div>
                    <div>
                      <p class="font-medium text-gray-900">{{ category.name }}</p>
                      <p class="text-sm text-gray-500">{{ category.count }} transactions</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="font-semibold text-gray-900">RWF {{ category.amount.toLocaleString() }}</p>
                    <p class="text-sm text-gray-500">{{ ((category.amount / data.insights.total_expenses) * 100).toFixed(1) }}% of expenses</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Export Section -->
            <div class="bg-gray-50 p-6 rounded-lg">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Data</h3>
              <p class="text-gray-600 mb-4">Download your financial data for external analysis.</p>
              <button 
                @click="showExportModal = true"
                  class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
              >
                Export Data
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Export Modal -->
    <ExportModal 
      v-if="showExportModal" 
      @close="showExportModal = false"
      @export="handleExport"
    />
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BudgetPieChart from '@/Components/Charts/BudgetPieChart.vue'
import SpendingTrendsChart from '@/Components/Charts/SpendingTrendsChart.vue'
import ExportModal from '@/Components/Export/ExportModal.vue'
import { Head } from '@inertiajs/vue3'

// Props
const props = defineProps<{
  data: {
    user: any
    budget_config: any
    monthly_income: number
    budget_allocation: {
      needs: { percentage: number; amount: number }
      wants: { percentage: number; amount: number }
      savings: { percentage: number; amount: number }
    }
    spending_trends: Array<{
      month: string
      expenses: number
      income: number
    }>
    categories: any[]
  }
}>()

// Reactive data
const showExportModal = ref(false)

// Methods
const handleExport = (exportData: any) => {
  console.log('Exporting data:', exportData)
  // TODO: Implement export functionality
  showExportModal.value = false
}

const getHealthColor = (status: string) => {
  switch (status) {
    case 'excellent':
      return 'text-green-600'
    case 'good':
      return 'text-blue-600'
    case 'warning':
      return 'text-yellow-600'
    case 'danger':
      return 'text-red-600'
    default:
      return 'text-gray-600'
  }
}

const getCategoryColor = (type: string) => {
  switch (type) {
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