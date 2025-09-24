<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
defineProps<{
  data: {
    daily_active_users: number
    weekly_active_users: number
    monthly_active_users: number
    feature_usage_stats: {
      transactions_per_user: number
      goals_per_user: number
      loans_per_user: number
    }
    transaction_volume_by_month: Array<{
      month: string
      amount: number
    }>
    user_retention: Array<{
      month: string
      total: number
      retained: number
      retention_rate: number
    }>
    user_distribution: {
      total_users: number
      active_users: number
      inactive_users: number
    }
  }
}>()
</script>

<template>
  <Head title="Admin - Analytics" />

  <AdminLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Analytics & Insights
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <!-- Daily Active Users -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Daily Active Users</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ data.daily_active_users.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Weekly Active Users -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Weekly Active Users</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ data.weekly_active_users.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Monthly Active Users -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Monthly Active Users</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ data.monthly_active_users.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Feature Usage Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Feature Usage Stats -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Feature Usage Statistics</h3>
              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600">Avg Transactions per User</span>
                  <span class="text-sm font-medium text-gray-900">{{ data.feature_usage_stats.transactions_per_user.toFixed(1) }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600">Avg Goals per User</span>
                  <span class="text-sm font-medium text-gray-900">{{ data.feature_usage_stats.goals_per_user.toFixed(1) }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600">Avg Loans per User</span>
                  <span class="text-sm font-medium text-gray-900">{{ data.feature_usage_stats.loans_per_user.toFixed(1) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- User Distribution -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">User Distribution</h3>
              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600">Total Users</span>
                  <span class="text-sm font-medium text-gray-900">{{ data.user_distribution.total_users.toLocaleString() }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600">Active Users (30d)</span>
                  <span class="text-sm font-medium text-green-600">{{ data.user_distribution.active_users.toLocaleString() }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600">Inactive Users</span>
                  <span class="text-sm font-medium text-red-600">{{ data.user_distribution.inactive_users.toLocaleString() }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-green-600 h-2 rounded-full" 
                    :style="`width: ${(data.user_distribution.active_users / data.user_distribution.total_users) * 100}%`"
                  ></div>
                </div>
                <div class="text-xs text-gray-500 text-center">
                  {{ ((data.user_distribution.active_users / data.user_distribution.total_users) * 100).toFixed(1) }}% Active
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Transaction Volume Chart -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction Volume by Month</h3>
            <div class="space-y-3">
              <div v-for="month in data.transaction_volume_by_month" :key="month.month" class="flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ month.month }}</span>
                <div class="flex items-center">
                  <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                    <div 
                      class="bg-blue-600 h-2 rounded-full" 
                      :style="`width: ${(month.amount / Math.max(...data.transaction_volume_by_month.map(m => m.amount))) * 100}%`"
                    ></div>
                  </div>
                  <span class="text-sm font-medium text-gray-900 w-20 text-right">${{ month.amount.toLocaleString() }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- User Retention -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">User Retention by Cohort</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Cohort Month
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total Users
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Retained
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Retention Rate
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="cohort in data.user_retention" :key="cohort.month">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ cohort.month }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ cohort.total }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ cohort.retained }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span 
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        :class="cohort.retention_rate >= 50 ? 'bg-green-100 text-green-800' : cohort.retention_rate >= 25 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'"
                      >
                        {{ cohort.retention_rate }}%
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>