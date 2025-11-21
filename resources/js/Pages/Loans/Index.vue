<template>
  <AuthenticatedLayout :user="data.user">
    <SeoHead
      title="Loans"
      description="Manage your loans and track loan payments. Monitor loan balances, payment schedules, and track your progress towards becoming debt-free."
      keywords="loan management, loan tracking, debt management, loan payments, loan calculator, debt payoff, loan balance"
    />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Loan Management</h1>
                <p class="text-gray-600 mt-1">Track and manage your loans from different lenders</p>
              </div>
              <Link 
                href="/loans/create"
                  class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors"
              >
                Add New Loan
              </Link>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
              <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-red-600">Total Debt</p>
                    <p class="text-2xl font-bold text-red-900">{{ data.totalDebt.toLocaleString() }} RWF</p>
                  </div>
                </div>
              </div>

              <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-orange-600">Monthly Payments</p>
                    <p class="text-2xl font-bold text-orange-900">{{ data.totalMonthlyPayments.toLocaleString() }} RWF</p>
                  </div>
                </div>
              </div>

              <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600">Active Loans</p>
                    <p class="text-2xl font-bold text-blue-900">{{ activeLoansCount }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Loans List -->
            <div v-if="data.loans.length > 0" class="space-y-4">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Loans</h2>
              
              <div 
                v-for="loan in data.loans" 
                :key="loan.id"
                class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
              >
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                      <h3 class="text-lg font-semibold text-gray-900">{{ loan.name }}</h3>
                      <span 
                        :class="getStatusBadgeClass(loan.status)"
                        class="px-2 py-1 text-xs font-medium rounded-full"
                      >
                        {{ loan.status.replace('_', ' ').toUpperCase() }}
                      </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                      <div>
                        <p class="text-gray-500">Lender</p>
                        <p class="font-medium">{{ loan.lender_name }}</p>
                        <p class="text-gray-500 text-xs">{{ loan.lender_type }} • {{ loan.loan_type }}</p>
                      </div>
                      
                      <div>
                        <p class="text-gray-500">Current Balance</p>
                        <p class="font-medium text-red-600">{{ loan.current_balance.toLocaleString() }} RWF</p>
                        <p class="text-gray-500 text-xs">Original: {{ loan.original_amount.toLocaleString() }} RWF</p>
                      </div>
                      
                      <div>
                        <p class="text-gray-500">Monthly Payment</p>
                        <p class="font-medium">{{ loan.monthly_payment.toLocaleString() }} RWF</p>
                        <p class="text-gray-500 text-xs">{{ loan.payment_frequency }}</p>
                      </div>
                      
                      <div>
                        <p class="text-gray-500">Progress</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                          <div 
                            class="bg-blue-600 h-2 rounded-full" 
                            :style="`width: ${loan.progress_percentage || 0}%`"
                          ></div>
                        </div>
                        <p class="text-xs text-gray-500">{{ (loan.progress_percentage || 0).toFixed(1) }}% paid</p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex space-x-2 ml-4">
                    <Link 
                      :href="`/loans/${loan.id}`"
                      class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                    >
                      View
                    </Link>
                    <Link 
                      v-if="loan.status === 'active'"
                      :href="`/loans/${loan.id}/payment`"
                      class="text-green-600 hover:text-green-800 text-sm font-medium"
                    >
                      Record Payment
                    </Link>
                    <Link 
                      :href="`/loans/${loan.id}/edit`"
                      class="text-gray-600 hover:text-gray-800 text-sm font-medium"
                    >
                      Edit
                    </Link>
                    <button 
                      @click="deleteLoan(loan)"
                      class="text-red-600 hover:text-red-800 text-sm font-medium"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No loans yet</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by adding your first loan.</p>
              <div class="mt-6">
                <Link 
                  href="/loans/create"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700"
                >
                  Add New Loan
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed } from 'vue'

// Props
const props = defineProps({
  data: {
    user: Object,
    loans: Array,
    totalMonthlyPayments: Number,
    totalDebt: Number
  }
})

// Computed
const activeLoansCount = computed(() => {
  return props.data.loans.filter(loan => loan.status === 'active').length
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

const deleteLoan = (loan) => {
  if (confirm(`Are you sure you want to delete "${loan.name}"?`)) {
    router.delete(`/loans/${loan.id}`, {
      onSuccess: () => {
        // Loan deleted successfully
      },
      onError: (errors) => {
        alert(errors.message || 'Error deleting loan')
      }
    })
  }
}
</script>