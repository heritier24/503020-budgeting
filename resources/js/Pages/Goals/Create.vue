<template>
  <AuthenticatedLayout :user="data.user">
    <Head title="Create New Goal" />

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="mb-6">
              <h1 class="text-2xl font-bold text-gray-900">Create New Financial Goal</h1>
              <p class="text-gray-600 mt-1">Set a new financial objective to track your progress</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Basic Information -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Goal Name *
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., Emergency Fund, Vacation to Europe"
                    required
                  />
                  <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                    {{ form.errors.name }}
                  </div>
                </div>

                <div>
                  <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Goal Type *
                  </label>
                  <select
                    id="type"
                    v-model="form.type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                  >
                    <option value="">Select goal type</option>
                    <option value="savings">Savings</option>
                    <option value="debt_payoff">Debt Payoff</option>
                    <option value="income">Income Increase</option>
                    <option value="expense_reduction">Expense Reduction</option>
                    <option value="investment">Investment</option>
                    <option value="other">Other</option>
                  </select>
                  <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">
                    {{ form.errors.type }}
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                  Description
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Describe your goal and why it's important to you..."
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                  {{ form.errors.description }}
                </div>
              </div>

              <!-- Financial Targets -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="target_amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Target Amount (RWF) *
                  </label>
                  <input
                    id="target_amount"
                    v-model="form.target_amount"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="1000000"
                    required
                  />
                  <div v-if="form.errors.target_amount" class="text-red-500 text-sm mt-1">
                    {{ form.errors.target_amount }}
                  </div>
                </div>

                <div>
                  <label for="current_amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Current Amount (RWF)
                  </label>
                  <input
                    id="current_amount"
                    v-model="form.current_amount"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0"
                  />
                  <div v-if="form.errors.current_amount" class="text-red-500 text-sm mt-1">
                    {{ form.errors.current_amount }}
                  </div>
                </div>
              </div>

              <!-- Timeline -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Start Date *
                  </label>
                  <input
                    id="start_date"
                    v-model="form.start_date"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                  />
                  <div v-if="form.errors.start_date" class="text-red-500 text-sm mt-1">
                    {{ form.errors.start_date }}
                  </div>
                </div>

                <div>
                  <label for="target_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Target Date *
                  </label>
                  <input
                    id="target_date"
                    v-model="form.target_date"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                  />
                  <div v-if="form.errors.target_date" class="text-red-500 text-sm mt-1">
                    {{ form.errors.target_date }}
                  </div>
                </div>
              </div>

              <!-- Priority and Customization -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                    Priority *
                  </label>
                  <select
                    id="priority"
                    v-model="form.priority"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                  >
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                  </select>
                  <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">
                    {{ form.errors.priority }}
                  </div>
                </div>

                <div>
                  <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                    Color
                  </label>
                  <input
                    id="color"
                    v-model="form.color"
                    type="color"
                    class="w-full h-10 px-1 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <div v-if="form.errors.color" class="text-red-500 text-sm mt-1">
                    {{ form.errors.color }}
                  </div>
                </div>

                <div>
                  <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                    Icon
                  </label>
                  <input
                    id="icon"
                    v-model="form.icon"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="🎯"
                    maxlength="2"
                  />
                  <div v-if="form.errors.icon" class="text-red-500 text-sm mt-1">
                    {{ form.errors.icon }}
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                  Notes
                </label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Any additional notes or reminders..."
                ></textarea>
                <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">
                  {{ form.errors.notes }}
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <Link
                  href="/goals"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  {{ form.processing ? 'Creating...' : 'Create Goal' }}
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
    user: Object
  }
})

// Form
const form = useForm({
  name: '',
  description: '',
  type: '',
  target_amount: '',
  current_amount: 0,
  target_date: '',
  start_date: new Date().toISOString().split('T')[0], // Today's date
  priority: 'medium',
  color: '#3b82f6',
  icon: '🎯',
  notes: ''
})

// Methods
const submit = () => {
  form.post('/goals', {
    onSuccess: () => {
      // Goal created successfully
    },
    onError: (errors) => {
      console.error('Form errors:', errors)
    }
  })
}
</script>