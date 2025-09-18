<template>
  <AuthenticatedLayout :user="data.user">
    <Head title="Loan Details" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <div class="flex items-center space-x-3">
                <Link href="/loans" class="text-gray-400 hover:text-gray-600">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </Link>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">{{ data.loan.name }}</h1>
                  <p class="text-gray-600">{{ data.loan.lender_name }} • {{ data.loan.loan_type }}</p>
                </div>
              </div>
              <div class="flex space-x-3">
                <Link 
                  v-if="data.loan.status === 'active'"
                  :href="`/loans/${data.loan.id}/payment`"
                  class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors"
                >
                  Record Payment
                </Link>
                <Link 
                  :href="`/loans/${data.loan.id}/edit`"
                  class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors"
                >
                  Edit Loan
                </Link>
              </div>
            </div>

            <!-- Loan Status Badge -->
            <div class="mb-6">
              <span 
                :class="getStatusBadgeClass(data.loan.status)"
                class="px-3 py-1 text-sm font-medium rounded-full"
              >
                {{ data.loan.status.replace('_', ' ').toUpperCase() }}
              </span>
            </div>

            <!-- Loan Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600">Original Amount</p>
                    <p class="text-2xl font-bold text-blue-900">{{ data.loan.original_amount.toLocaleString() }} RWF</p>
                  </div>
                </div>
              </div>

              <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-red-600">Current Balance</p>
                    <p class="text-2xl font-bold text-red-900">{{ data.loan.current_balance.toLocaleString() }} RWF</p>
                  </div>
                </div>
              </div>

              <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-green-600">Total Paid</p>
                    <p class="text-2xl font-bold text-green-900">{{ (data.loan.total_paid || 0).toLocaleString() }} RWF</p>
                  </div>
                </div>
              </div>

              <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-purple-600">Monthly Payment</p>
                    <p class="text-2xl font-bold text-purple-900">{{ data.loan.monthly_payment.toLocaleString() }} RWF</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Progress Section -->
            <div class="bg-gray-50 p-6 rounded-lg mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Progress</h3>
              <div class="space-y-4">
                <div>
                  <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Progress</span>
                    <span class="font-medium">{{ (data.loan.progress_percentage || 0).toFixed(1) }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3">
                    <div 
                      class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                      :style="`width: ${data.loan.progress_percentage || 0}%`"
                    ></div>
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                  <div>
                    <p class="text-gray-500">Remaining Payments</p>
                    <p class="font-semibold">{{ data.loan.remaining_payments || 0 }} payments</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Interest Rate</p>
                    <p class="font-semibold">{{ data.loan.interest_rate || 'N/A' }}%</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Payment Frequency</p>
                    <p class="font-semibold">{{ data.loan.payment_frequency }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Loan Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Loan Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Information</h3>
                <dl class="space-y-3">
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Lender Type</dt>
                    <dd class="text-sm font-medium">{{ data.loan.lender_type }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Loan Type</dt>
                    <dd class="text-sm font-medium">{{ data.loan.loan_type }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Start Date</dt>
                    <dd class="text-sm font-medium">{{ formatDate(data.loan.start_date) }}</dd>
                  </div>
                  <div class="flex justify-between" v-if="data.loan.end_date">
                    <dt class="text-sm text-gray-500">End Date</dt>
                    <dd class="text-sm font-medium">{{ formatDate(data.loan.end_date) }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Created</dt>
                    <dd class="text-sm font-medium">{{ formatDate(data.loan.created_at) }}</dd>
                  </div>
                </dl>
                
                <div v-if="data.loan.notes" class="mt-4">
                  <h4 class="text-sm font-medium text-gray-900 mb-2">Notes</h4>
                  <p class="text-sm text-gray-600">{{ data.loan.notes }}</p>
                </div>
              </div>

              <!-- Payment History -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Payments</h3>
                <div v-if="data.loan.loan_transactions && data.loan.loan_transactions.length > 0" class="space-y-3">
                  <div 
                    v-for="transaction in data.loan.loan_transactions.slice(0, 5)" 
                    :key="transaction.id"
                    class="flex justify-between items-center p-3 bg-gray-50 rounded-lg"
                  >
                    <div>
                      <p class="text-sm font-medium">{{ formatDate(transaction.transaction_date) }}</p>
                      <p class="text-xs text-gray-500">{{ transaction.type }}</p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-semibold text-red-600">-{{ transaction.amount.toLocaleString() }} RWF</p>
                      <p v-if="transaction.principal_amount" class="text-xs text-gray-500">
                        Principal: {{ transaction.principal_amount.toLocaleString() }} RWF
                      </p>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-8">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <h3 class="mt-2 text-sm font-medium text-gray-900">No payments yet</h3>
                  <p class="mt-1 text-sm text-gray-500">Record your first payment to get started.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  data: {
    user: Object,
    loan: Object
  }
})

// Methods
const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'active':
      return 'bg-green-100 text-green-800'
    case 'paid_off':
      return 'bg-blue-100 text-blue-800'
    case 'defaulted':
      return 'bg-red-100 text-red-800'
    case 'paused':
      return 'bg-yellow-100 text-yellow-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>