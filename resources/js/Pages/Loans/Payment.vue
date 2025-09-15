<template>
  <AuthenticatedLayout :user="data.user">
    <Head title="Record Loan Payment" />

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
                <h1 class="text-2xl font-bold text-gray-900">Record Loan Payment</h1>
              </div>
              <p class="text-gray-600">Record a payment for {{ data.loan.name }}</p>
            </div>

            <!-- Loan Information -->
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Details</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <p class="text-sm text-gray-500">Current Balance</p>
                  <p class="text-lg font-semibold text-red-600">{{ data.loan.current_balance.toLocaleString() }} RWF</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Monthly Payment</p>
                  <p class="text-lg font-semibold">{{ data.loan.monthly_payment.toLocaleString() }} RWF</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Lender</p>
                  <p class="text-lg font-semibold">{{ data.loan.lender_name }}</p>
                </div>
              </div>
            </div>

            <!-- Payment Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                      Total Payment Amount (RWF) *
                    </label>
                    <input
                      type="number"
                      id="amount"
                      v-model="form.amount"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="25000"
                      min="0"
                      step="1000"
                      required
                    />
                    <div v-if="form.errors.amount" class="text-red-500 text-sm mt-1">
                      {{ form.errors.amount }}
                    </div>
                  </div>

                  <div>
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                      Payment Date *
                    </label>
                    <input
                      type="date"
                      id="transaction_date"
                      v-model="form.transaction_date"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      required
                    />
                    <div v-if="form.errors.transaction_date" class="text-red-500 text-sm mt-1">
                      {{ form.errors.transaction_date }}
                    </div>
                  </div>

                  <div>
                    <label for="principal_amount" class="block text-sm font-medium text-gray-700 mb-2">
                      Principal Amount (RWF)
                    </label>
                    <input
                      type="number"
                      id="principal_amount"
                      v-model="form.principal_amount"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="20000"
                      min="0"
                      step="1000"
                    />
                    <p class="text-xs text-gray-500 mt-1">Leave empty to use total amount as principal</p>
                    <div v-if="form.errors.principal_amount" class="text-red-500 text-sm mt-1">
                      {{ form.errors.principal_amount }}
                    </div>
                  </div>

                  <div>
                    <label for="interest_amount" class="block text-sm font-medium text-gray-700 mb-2">
                      Interest Amount (RWF)
                    </label>
                    <input
                      type="number"
                      id="interest_amount"
                      v-model="form.interest_amount"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="5000"
                      min="0"
                      step="1000"
                    />
                    <p class="text-xs text-gray-500 mt-1">Leave empty if no interest</p>
                    <div v-if="form.errors.interest_amount" class="text-red-500 text-sm mt-1">
                      {{ form.errors.interest_amount }}
                    </div>
                  </div>
                </div>

                <div class="mt-6">
                  <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes (Optional)
                  </label>
                  <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Any additional notes about this payment..."
                  ></textarea>
                  <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">
                    {{ form.errors.notes }}
                  </div>
                </div>
              </div>

              <!-- Quick Payment Buttons -->
              <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Payment Amounts</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                  <button
                    type="button"
                    @click="setPaymentAmount(data.loan.monthly_payment)"
                    class="p-3 bg-blue-100 border border-blue-200 rounded-lg hover:bg-blue-200 transition-colors text-center"
                  >
                    <div class="text-sm font-medium text-blue-800">Monthly Payment</div>
                    <div class="text-xs text-blue-600">{{ data.loan.monthly_payment.toLocaleString() }} RWF</div>
                  </button>
                  
                  <button
                    type="button"
                    @click="setPaymentAmount(data.loan.monthly_payment * 2)"
                    class="p-3 bg-green-100 border border-green-200 rounded-lg hover:bg-green-200 transition-colors text-center"
                  >
                    <div class="text-sm font-medium text-green-800">Double Payment</div>
                    <div class="text-xs text-green-600">{{ (data.loan.monthly_payment * 2).toLocaleString() }} RWF</div>
                  </button>
                  
                  <button
                    type="button"
                    @click="setPaymentAmount(data.loan.current_balance)"
                    class="p-3 bg-red-100 border border-red-200 rounded-lg hover:bg-red-200 transition-colors text-center"
                  >
                    <div class="text-sm font-medium text-red-800">Pay Off Loan</div>
                    <div class="text-xs text-red-600">{{ data.loan.current_balance.toLocaleString() }} RWF</div>
                  </button>
                  
                  <button
                    type="button"
                    @click="setPaymentAmount(data.loan.monthly_payment * 0.5)"
                    class="p-3 bg-yellow-100 border border-yellow-200 rounded-lg hover:bg-yellow-200 transition-colors text-center"
                  >
                    <div class="text-sm font-medium text-yellow-800">Half Payment</div>
                    <div class="text-xs text-yellow-600">{{ (data.loan.monthly_payment * 0.5).toLocaleString() }} RWF</div>
                  </button>
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
                    Recording...
                  </span>
                  <span v-else>Record Payment</span>
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

// Form
const form = useForm({
  amount: '',
  principal_amount: '',
  interest_amount: '',
  transaction_date: new Date().toISOString().split('T')[0],
  notes: ''
})

// Methods
const setPaymentAmount = (amount) => {
  form.amount = amount.toString()
}

// Submit form
const submit = () => {
  form.post(`/loans/${props.data.loan.id}/payments`, {
    onSuccess: () => {
      // Redirect to loan details after successful payment
    }
  })
}
</script>