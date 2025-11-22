<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Declare global route function
declare const route: any

// Props
defineProps<{
  user: {
    id: number
    name: string
    email: string
    is_admin: boolean
    created_at: string
    updated_at: string
    transactions: Array<{
      id: number
      amount: number
      description: string
      type: string
      created_at: string
      category: {
        id: number
        name: string
        type: string
      }
    }>
    goals: Array<any>
    loans: Array<any>
    budget_config: any
    income_sources: Array<any>
  }
  transaction_stats: {
    total_transactions: number
    total_amount: number
    this_month: number
    this_month_amount: number
  }
  recent_activity: Array<{
    id: number
    amount: number
    description: string
    type: string
    created_at: string
    category: {
      id: number
      name: string
      type: string
    }
  }>
}>()
</script>

<template>
  <Head :title="`Admin - User Details: ${user.name}`" />

  <AdminLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Details: {{ user.name }}
          </h2>
          <p class="text-sm text-gray-600 mt-1">{{ user.email }}</p>
        </div>
        <Link 
          :href="route('admin.users')" 
          class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          ← Back to Users
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- User Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Name:</span>
                    <span class="text-sm font-medium">{{ user.name }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Email:</span>
                    <span class="text-sm font-medium">{{ user.email }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Role:</span>
                    <span v-if="user.is_admin" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                      Admin
                    </span>
                    <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                      User
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Joined:</span>
                    <span class="text-sm font-medium">{{ new Date(user.created_at).toLocaleDateString() }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Last Active:</span>
                    <span class="text-sm font-medium">{{ new Date(user.updated_at).toLocaleDateString() }}</span>
                  </div>
                </div>
              </div>

              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Account Status</h3>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Budget Config:</span>
                    <span v-if="user.budget_config" class="text-sm font-medium text-green-600">✓ Configured</span>
                    <span v-else class="text-sm font-medium text-red-600">✗ Not Set</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Income Sources:</span>
                    <span class="text-sm font-medium">{{ user.income_sources.length }} sources</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Goals:</span>
                    <span class="text-sm font-medium">{{ user.goals.length }} goals</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Loans:</span>
                    <span class="text-sm font-medium">{{ user.loans.length }} loans</span>
                  </div>
                </div>
              </div>

              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                  <button class="w-full text-left px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded">
                    View Full Profile
                  </button>
                  <button class="w-full text-left px-3 py-2 text-sm text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 rounded">
                    Edit User
                  </button>
                  <button class="w-full text-left px-3 py-2 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded">
                    Suspend User
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Transaction Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
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
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Transactions</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ transaction_stats.total_transactions }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Amount</dt>
                    <dd class="text-lg font-medium text-gray-900">${{ transaction_stats.total_amount.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ transaction_stats.this_month }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Month Amount</dt>
                    <dd class="text-lg font-medium text-gray-900">${{ transaction_stats.this_month_amount.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Description
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Category
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Amount
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="transaction in recent_activity" :key="transaction.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ new Date(transaction.created_at).toLocaleDateString() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ transaction.description }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="transaction.category.type === 'expense' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                        {{ transaction.category.name }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="transaction.type === 'expense' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                        {{ transaction.type }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                        :class="transaction.type === 'expense' ? 'text-red-600' : 'text-green-600'">
                      {{ transaction.type === 'expense' ? '-' : '+' }}${{ transaction.amount.toLocaleString() }}
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