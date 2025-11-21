<template>
  <AuthenticatedLayout :user="data.user">
    <SeoHead
      title="Analytics"
      description="Analyze your spending patterns, view detailed financial reports, track budget performance, and gain insights into your financial habits with interactive charts and visualizations."
      keywords="financial analytics, spending analysis, budget reports, expense reports, financial insights, spending trends, budget performance, financial charts, data visualization"
    />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="mb-6">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                  <h1 class="text-3xl font-bold text-gray-900">Analytics & Reports</h1>
                  <p class="text-gray-600 mt-2">Track your spending patterns and budget performance.</p>
                </div>
                
                <!-- Date Range Picker -->
                <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                  <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Date Range:</label>
                    <input
                      v-model="dateRange.start"
                      type="date"
                      class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:border-blue-500 focus:ring-blue-500"
                      @change="updateDateRange"
                    />
                    <span class="text-gray-500">to</span>
                    <input
                      v-model="dateRange.end"
                      type="date"
                      class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:border-blue-500 focus:ring-blue-500"
                      @change="updateDateRange"
                    />
                  </div>
                  
                  <!-- Quick Date Buttons -->
                  <div class="flex space-x-1">
                    <button
                      v-for="period in quickPeriods"
                      :key="period.value"
                      @click="setQuickPeriod(period.value)"
                      :class="[
                        'px-3 py-1 text-xs rounded-md transition-colors',
                        selectedPeriod === period.value
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                      ]"
                    >
                      {{ period.label }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex items-center justify-center py-12">
              <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                <p class="text-gray-600">Loading analytics data...</p>
              </div>
            </div>

            <!-- Error State -->
            <div v-else-if="hasError" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-red-800">Error loading analytics data</h3>
                  <p class="text-sm text-red-700 mt-1">{{ errorMessage }}</p>
                  <button 
                    @click="refreshData"
                    class="mt-2 text-sm bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded-md transition-colors"
                  >
                    Try Again
                  </button>
                </div>
              </div>
            </div>

            <!-- Charts Section -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
              <!-- Budget Allocation Chart -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-gray-900">Budget Allocation</h3>
                  <div v-if="isRefreshing" class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
                </div>
                <div v-if="!data.budget_allocation" class="text-center py-8 text-gray-500">
                  <p>No budget data available</p>
                </div>
                <BudgetPieChart v-else :data="data.budget_allocation" />
              </div>

              <!-- Spending Trends Chart -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-gray-900">Spending Trends</h3>
                  <div v-if="isRefreshing" class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
                </div>
                <div v-if="!data.spending_trends || data.spending_trends.length === 0" class="text-center py-8 text-gray-500">
                  <p>No spending data available for the selected period</p>
                </div>
                <SpendingTrendsChart v-else :data="data.spending_trends" />
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
              
              <div class="flex flex-wrap gap-3">
                <button 
                  @click="showExportModal = true"
                  class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors flex items-center space-x-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <span>Export Data</span>
                </button>
                
                <button 
                  @click="exportToPDF"
                  :disabled="isExporting"
                  class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors flex items-center space-x-2 disabled:opacity-50"
                >
                  <svg v-if="!isExporting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                  <div v-else class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                  <span>{{ isExporting ? 'Generating...' : 'Export PDF' }}</span>
                </button>
                
                <button 
                  @click="shareReport"
                  class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center space-x-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                  </svg>
                  <span>Share Report</span>
                </button>
              </div>
              
              <!-- Export Status -->
              <div v-if="exportStatus" class="mt-4 p-3 rounded-md" :class="exportStatus.type === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'">
                <p class="text-sm">{{ exportStatus.message }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Export Modal -->
    <ExportModal 
      :isOpen="showExportModal"
      @close="showExportModal = false"
      @export="handleExport"
    />
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BudgetPieChart from '@/Components/Charts/BudgetPieChart.vue'
import SpendingTrendsChart from '@/Components/Charts/SpendingTrendsChart.vue'
import ExportModal from '@/Components/Export/ExportModal.vue'
import { router } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'

// Props
defineProps<{
  data: {
    user: any
    budget_config: any
    monthly_income: number
    available_income: number
    loan_info: {
      total_monthly_payments: number
      active_loans_count: number
    }
    goals_info: {
      active_goals_count: number
      savings_goals: {
        count: number
        target_amount: number
        current_amount: number
        progress_percentage: number
      }
      debt_payoff_goals: {
        count: number
        target_amount: number
        current_amount: number
        progress_percentage: number
      }
    }
    budget_allocation: {
      needs: { percentage: number; amount: number; spent: number; remaining: number }
      wants: { percentage: number; amount: number; spent: number; remaining: number }
      savings: { percentage: number; amount: number; spent: number; remaining: number }
    }
    spending_trends: Array<{
      month: string
      expenses: number
      income: number
    }>
    category_spending: Array<{
      name: string
      type: string
      amount: number
      count: number
    }>
    insights: {
      total_expenses: number
      average_transaction: number
      most_spent_category: any
      budget_health: {
        score: number
        status: string
        message: string
      }
    }
    categories: any[]
  }
}>()

// Reactive data
const showExportModal = ref(false)
const isLoading = ref(false)
const isRefreshing = ref(false)
const hasError = ref(false)
const errorMessage = ref('')
const isExporting = ref(false)
const exportStatus = ref<{type: 'success' | 'error', message: string} | null>(null)

// Date range management
const dateRange = ref({
  start: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 30 days ago
  end: new Date().toISOString().split('T')[0] // today
})

const selectedPeriod = ref('30')

const quickPeriods = [
  { label: '7D', value: '7' },
  { label: '30D', value: '30' },
  { label: '90D', value: '90' },
  { label: '1Y', value: '365' }
]

// Methods
const handleExport = (exportData: any) => {
  console.log('Exporting data:', exportData)
  // TODO: Implement export functionality
  showExportModal.value = false
}

const updateDateRange = () => {
  isRefreshing.value = true
  hasError.value = false
  
  // Simulate API call delay
  setTimeout(() => {
    isRefreshing.value = false
    // In real implementation, this would fetch new data based on date range
    console.log('Date range updated:', dateRange.value)
  }, 1000)
}

const setQuickPeriod = (period: string) => {
  selectedPeriod.value = period
  const days = parseInt(period)
  const endDate = new Date()
  const startDate = new Date(Date.now() - days * 24 * 60 * 60 * 1000)
  
  dateRange.value = {
    start: startDate.toISOString().split('T')[0],
    end: endDate.toISOString().split('T')[0]
  }
  
  updateDateRange()
}

const refreshData = () => {
  hasError.value = false
  errorMessage.value = ''
  isLoading.value = true
  
  // Simulate API call
  setTimeout(() => {
    isLoading.value = false
    // In real implementation, this would reload the page or fetch new data
    router.reload()
  }, 1500)
}

const exportToPDF = async () => {
  isExporting.value = true
  exportStatus.value = null
  
  try {
    // Simulate PDF generation
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    exportStatus.value = {
      type: 'success',
      message: 'PDF report generated successfully!'
    }
    
    // In real implementation, this would trigger a download
    console.log('PDF export completed')
  } catch (error) {
    exportStatus.value = {
      type: 'error',
      message: 'Failed to generate PDF report. Please try again.'
    }
  } finally {
    isExporting.value = false
    
    // Clear status message after 5 seconds
    setTimeout(() => {
      exportStatus.value = null
    }, 5000)
  }
}

const shareReport = () => {
  if (navigator.share) {
    navigator.share({
      title: 'My Budget Analytics Report',
        text: 'Check out my spending analytics from Rule 50 30 20',
      url: window.location.href
    }).catch(console.error)
  } else {
    // Fallback: copy URL to clipboard
    navigator.clipboard.writeText(window.location.href).then(() => {
      exportStatus.value = {
        type: 'success',
        message: 'Report URL copied to clipboard!'
      }
      
      setTimeout(() => {
        exportStatus.value = null
      }, 3000)
    }).catch(() => {
      exportStatus.value = {
        type: 'error',
        message: 'Failed to copy URL. Please try again.'
      }
    })
  }
}

// Initialize date range on mount
onMounted(() => {
  // Set initial date range based on selected period
  setQuickPeriod(selectedPeriod.value)
})

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