<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, onMounted, onUnmounted } from 'vue'

// Props
defineProps<{
  data: {
    total_users: number
    new_users_this_month: number
    new_users_this_week: number
    active_users: number
    users_with_transactions: number
    users_with_goals: number
    users_with_loans: number
    users_with_budget_config: number
    total_transactions: number
    transactions_this_month: number
    total_transaction_amount: number
    average_transaction_amount: number
    most_used_categories: Array<{
      id: number
      name: string
      type: string
      transactions_count: number
    }>
    total_goals: number
    completed_goals: number
    active_goals: number
    total_loans: number
    active_loans: number
    total_loan_amount: number
    user_growth: Array<{
      month: string
      users: number
    }>
    feature_adoption: {
      transactions: { users: number; percentage: number }
      goals: { users: number; percentage: number }
      loans: { users: number; percentage: number }
      budget_config: { users: number; percentage: number }
    }
    recent_users: Array<{
      id: number
      name: string
      email: string
      created_at: string
    }>
    recent_transactions: Array<{
      id: number
      amount: number
      description: string
      created_at: string
      user: { name: string }
      category: { name: string }
    }>
  }
}>()

// Real-time data
const realTimeTransactions = ref<any[]>([])
const activeUsers = ref(0)
const lastUpdate = ref('')
let intervalId: number | null = null

// Fetch real-time data
const fetchRealTimeData = async () => {
  try {
    const response = await fetch('/admin/real-time-transactions')
    const data = await response.json()
    realTimeTransactions.value = data.recent_transactions
    activeUsers.value = data.active_users
    lastUpdate.value = new Date(data.timestamp).toLocaleTimeString()
  } catch (error) {
    console.error('Error fetching real-time data:', error)
  }
}

// Set up real-time updates
onMounted(() => {
  fetchRealTimeData()
  intervalId = setInterval(fetchRealTimeData, 30000) // Update every 30 seconds
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})
</script>

<template>
  <Head title="Admin Dashboard" />

  <AdminLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Admin Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Users -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ data.total_users.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- New Users This Month -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">New Users (Month)</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ data.new_users_this_month.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Users -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Active Users (30d)</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ data.active_users.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Transactions -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Transactions</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ data.total_transactions.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Feature Adoption -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Feature Adoption Chart -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Feature Adoption</h3>
              <div class="space-y-4">
                <div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Transactions</span>
                    <span class="font-medium">{{ data.feature_adoption.transactions.percentage }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${data.feature_adoption.transactions.percentage}%`"></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Goals</span>
                    <span class="font-medium">{{ data.feature_adoption.goals.percentage }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" :style="`width: ${data.feature_adoption.goals.percentage}%`"></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Loans</span>
                    <span class="font-medium">{{ data.feature_adoption.loans.percentage }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full" :style="`width: ${data.feature_adoption.loans.percentage}%`"></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Budget Config</span>
                    <span class="font-medium">{{ data.feature_adoption.budget_config.percentage }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" :style="`width: ${data.feature_adoption.budget_config.percentage}%`"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Most Used Categories -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Most Used Categories</h3>
              <div class="space-y-3">
                <div v-for="category in data.most_used_categories.slice(0, 5)" :key="category.id" class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-3" :class="category.type === 'expense' ? 'bg-red-500' : 'bg-green-500'"></div>
                    <span class="text-sm text-gray-900">{{ category.name }}</span>
                  </div>
                  <span class="text-sm font-medium text-gray-500">{{ category.transactions_count }} transactions</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Real-time Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <!-- Active Users -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">Active Users</h3>
                  <p class="text-2xl font-bold text-green-600">{{ activeUsers }}</p>
                </div>
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-2">Users active in last 15 minutes</p>
            </div>
          </div>

          <!-- Last Update -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">Last Update</h3>
                  <p class="text-sm font-medium text-gray-600">{{ lastUpdate || 'Loading...' }}</p>
                </div>
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-2">Real-time data updates every 30s</p>
            </div>
          </div>

          <!-- Real-time Status -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">System Status</h3>
                  <p class="text-sm font-medium text-green-600">✓ Live</p>
                </div>
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center animate-pulse">
                  <div class="w-3 h-3 bg-white rounded-full"></div>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-2">Real-time monitoring active</p>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Recent Users -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Users</h3>
              <div class="space-y-3">
                <div v-for="user in data.recent_users" :key="user.id" class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                  </div>
                  <span class="text-xs text-gray-400">{{ new Date(user.created_at).toLocaleDateString() }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Real-time Transactions -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Live Transactions</h3>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  <div class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></div>
                  Live
                </span>
              </div>
              <div class="space-y-3 max-h-64 overflow-y-auto">
                <div v-for="transaction in realTimeTransactions.slice(0, 10)" :key="transaction.id" class="flex items-center justify-between p-2 bg-gray-50 rounded">
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ transaction.description }}</p>
                    <p class="text-sm text-gray-500">{{ transaction.user.name }} • {{ transaction.category.name }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">${{ transaction.amount.toLocaleString() }}</p>
                    <span class="text-xs text-gray-400">{{ new Date(transaction.created_at).toLocaleTimeString() }}</span>
                  </div>
                </div>
                <div v-if="realTimeTransactions.length === 0" class="text-center py-4 text-gray-500">
                  Loading real-time data...
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>