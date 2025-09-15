<template>
  <AuthenticatedLayout :user="data.user">
    <Head title="Edit Loan" />

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="mb-6">
              <div class="flex items-center space-x-3 mb-2">
                <Link :href="`/loans/${data.loan.id}`" class="text-gray-400 hover:text-gray-600">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">Edit Loan</h1>
              </div>
              <p class="text-gray-600">Update details for {{ data.loan.name }}</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Basic Information -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                      Loan Name *
                    </label>
                    <input
                      type="text"
                      id="name"
                      v-model="form.name"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="e.g., Bank ABC Personal Loan"
                      required
                    />
                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                      {{ form.errors.name }}
                    </div>
                  </div>

                  <div>
                    <label for="lender_name" class="block text-sm font-medium text-gray-700 mb-2">
                      Lender Name *
                    </label>
                    <input
                      type="text"
                      id="lender_name"
                      v-model="form.lender_name"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="e.g., Bank ABC, John Doe"
                      required
                    />
                    <div v-if="form.errors.lender_name" class="text-red-500 text-sm mt-1">
                      {{ form.errors.lender_name }}
                    </div>
                  </div>

                  <div>
                    <label for="lender_type" class="block text-sm font-medium text-gray-700 mb-2">
                      Lender Type *
                    </label>
                    <select
                      id="lender_type"
                      v-model="form.lender_type"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      required
                    >
                      <option value="">Select lender type</option>
                      <option value="bank">Bank</option>
                      <option value="personal">Personal (Friend/Family)</option>
                      <option value="credit_card">Credit Card</option>
                      <option value="business">Business</option>
                      <option value="other">Other</option>
                    </select>
                    <div v-if="form.errors.lender_type" class="text-red-500 text-sm mt-1">
                      {{ form.errors.lender_type }}
                    </div>
                  </div>

                  <div>
                    <label for="loan_type" class="block text-sm font-medium text-gray-700 mb-2">
                      Loan Type *
                    </label>
                    <select
                      id="loan_type"
                      v-model="form.loan_type"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      required
                    >
                      <option value="">Select loan type</option>
                      <option value="personal">Personal</option>
                      <option value="business">Business</option>
                      <option value="education">Education</option>
                      <option value="mortgage">Mortgage</option>
                      <option value="auto">Auto</option>
                      <option value="other">Other</option>
                    </select>
                    <div v-if="form.errors.loan_type" class="text-red-500 text-sm mt-1">
                      {{ form.errors.loan_type }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Financial Details -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="original_amount" class="block text-sm font-medium text-gray-700 mb-2">
                      Original Loan Amount (RWF) *
                    </label>
                    <input
                      type="number"
                      id="original_amount"
                      v-model="form.original_amount"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="500000"
                      min="0"
                      step="1000"
                      required
                    />
                    <div v-if="form.errors.original_amount" class="text-red-500 text-sm mt-1">
                      {{ form.errors.original_amount }}
                    </div>
                  </div>

                  <div>
                    <label for="current_balance" class="block text-sm font-medium text-gray-700 mb-2">
                      Current Balance (RWF) *
                    </label>
                    <input
                      type="number"
                      id="current_balance"
                      v-model="form.current_balance"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="450000"
                      min="0"
                      step="1000"
                      required
                    />
                    <div v-if="form.errors.current_balance" class="text-red-500 text-sm mt-1">
                      {{ form.errors.current_balance }}
                    </div>
                  </div>

                  <div>
                    <label for="interest_rate" class="block text-sm font-medium text-gray-700 mb-2">
                      Interest Rate (% per year)
                    </label>
                    <input
                      type="number"
                      id="interest_rate"
                      v-model="form.interest_rate"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="12.5"
                      min="0"
                      max="100"
                      step="0.1"
                    />
                    <div v-if="form.errors.interest_rate" class="text-red-500 text-sm mt-1">
                      {{ form.errors.interest_rate }}
                    </div>
                  </div>

                  <div>
                    <label for="monthly_payment" class="block text-sm font-medium text-gray-700 mb-2">
                      Monthly Payment (RWF) *
                    </label>
                    <input
                      type="number"
                      id="monthly_payment"
                      v-model="form.monthly_payment"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="25000"
                      min="0"
                      step="1000"
                      required
                    />
                    <div v-if="form.errors.monthly_payment" class="text-red-500 text-sm mt-1">
                      {{ form.errors.monthly_payment }}
                    </div>
                  </div>

                  <div>
                    <label for="payment_frequency" class="block text-sm font-medium text-gray-700 mb-2">
                      Payment Frequency *
                    </label>
                    <select
                      id="payment_frequency"
                      v-model="form.payment_frequency"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      required
                    >
                      <option value="">Select frequency</option>
                      <option value="weekly">Weekly</option>
                      <option value="bi-weekly">Bi-weekly</option>
                      <option value="monthly">Monthly</option>
                      <option value="quarterly">Quarterly</option>
                      <option value="yearly">Yearly</option>
                    </select>
                    <div v-if="form.errors.payment_frequency" class="text-red-500 text-sm mt-1">
                      {{ form.errors.payment_frequency }}
                    </div>
                  </div>

                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                      Loan Status *
                    </label>
                    <select
                      id="status"
                      v-model="form.status"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      required
                    >
                      <option value="">Select status</option>
                      <option value="active">Active</option>
                      <option value="paid_off">Paid Off</option>
                      <option value="defaulted">Defaulted</option>
                      <option value="paused">Paused</option>
                    </select>
                    <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">
                      {{ form.errors.status }}
                    </div>
                  </div>

                  <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                      Start Date *
                    </label>
                    <input
                      type="date"
                      id="start_date"
                      v-model="form.start_date"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      required
                    />
                    <div v-if="form.errors.start_date" class="text-red-500 text-sm mt-1">
                      {{ form.errors.start_date }}
                    </div>
                  </div>

                  <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                      End Date (Optional)
                    </label>
                    <input
                      type="date"
                      id="end_date"
                      v-model="form.end_date"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                    <div v-if="form.errors.end_date" class="text-red-500 text-sm mt-1">
                      {{ form.errors.end_date }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                  Notes (Optional)
                </label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Any additional notes about this loan..."
                ></textarea>
                <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">
                  {{ form.errors.notes }}
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-4">
                <Link 
                  :href="`/loans/${data.loan.id}`"
                  class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  <span v-if="form.processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                  </span>
                  <span v-else>Update Loan</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  data: {
    user: Object,
    loan: Object
  }
})

// Form - Initialize with current loan data
const form = useForm({
  name: props.data.loan.name,
  lender_name: props.data.loan.lender_name,
  lender_type: props.data.loan.lender_type,
  loan_type: props.data.loan.loan_type,
  original_amount: props.data.loan.original_amount,
  current_balance: props.data.loan.current_balance,
  interest_rate: props.data.loan.interest_rate || '',
  monthly_payment: props.data.loan.monthly_payment,
  payment_frequency: props.data.loan.payment_frequency,
  status: props.data.loan.status,
  start_date: props.data.loan.start_date,
  end_date: props.data.loan.end_date || '',
  notes: props.data.loan.notes || ''
})

// Submit form
const submit = () => {
  form.put(`/loans/${props.data.loan.id}`, {
    onSuccess: () => {
      // Redirect to loan details after successful update
    }
  })
}
</script>