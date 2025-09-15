<template>
  <AuthenticatedLayout :user="data.user">
    <Head :title="data.goal.name" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
              <div class="flex items-center space-x-4">
                <span class="text-4xl">{{ data.goal.icon }}</span>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">{{ data.goal.name }}</h1>
                  <div class="flex items-center space-x-2 mt-1">
                    <span 
                      :class="getStatusBadgeClass(data.goal.status)"
                      class="px-2 py-1 text-xs font-medium rounded-full"
                    >
                      {{ data.goal.status.replace('_', ' ').toUpperCase() }}
                    </span>
                    <span 
                      :class="getPriorityBadgeClass(data.goal.priority)"
                      class="px-2 py-1 text-xs font-medium rounded-full"
                    >
                      {{ data.goal.priority.toUpperCase() }}
                    </span>
                  </div>
                </div>
              </div>
              
              <div class="flex space-x-2">
                <Link 
                  :href="`/goals/${data.goal.id}/edit`"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                >
                  Edit Goal
                </Link>
                <button 
                  @click="updateProgress"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                >
                  Update Progress
                </button>
                <button 
                  @click="showContributionModal = true"
                  class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors"
                >
                  Add Contribution
                </button>
              </div>
            </div>

            <!-- Description -->
            <div v-if="data.goal.description" class="mb-6">
              <p class="text-gray-600">{{ data.goal.description }}</p>
            </div>

            <!-- Progress Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
              <!-- Progress Bar -->
              <div class="md:col-span-2">
                <div class="bg-gray-50 rounded-lg p-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Progress Overview</h3>
                  
                  <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                      <span>Progress</span>
                      <span>{{ data.goal.progress_percentage.toFixed(1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                      <div 
                        class="h-4 rounded-full transition-all duration-300" 
                        :style="`width: ${data.goal.progress_percentage}%; background-color: ${data.goal.color}`"
                      ></div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <p class="text-gray-500">Current Amount</p>
                      <p class="text-lg font-semibold text-green-600">{{ data.goal.current_amount.toLocaleString() }} RWF</p>
                    </div>
                    <div>
                      <p class="text-gray-500">Target Amount</p>
                      <p class="text-lg font-semibold text-gray-900">{{ data.goal.target_amount.toLocaleString() }} RWF</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Timeline Info -->
              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                
                <div class="space-y-3 text-sm">
                  <div>
                    <p class="text-gray-500">Start Date</p>
                    <p class="font-medium">{{ formatDate(data.goal.start_date) }}</p>
                  </div>
                  
                  <div>
                    <p class="text-gray-500">Target Date</p>
                    <p class="font-medium">{{ formatDate(data.goal.target_date) }}</p>
                  </div>
                  
                  <div>
                    <p class="text-gray-500">Days Remaining</p>
                    <p class="font-medium" :class="data.goal.days_remaining < 30 ? 'text-red-600' : 'text-gray-900'">
                      {{ data.goal.days_remaining }} days
                    </p>
                  </div>
                  
                  <div>
                    <p class="text-gray-500">Days Elapsed</p>
                    <p class="font-medium">{{ data.goal.days_elapsed }} days</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Financial Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <!-- Monthly Target -->
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Monthly Target</h3>
                <p class="text-2xl font-bold text-blue-900">{{ data.goal.monthly_target.toLocaleString() }} RWF</p>
                <p class="text-sm text-blue-700 mt-1">Amount needed per month to reach goal</p>
              </div>

              <!-- Remaining Amount -->
              <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-orange-900 mb-2">Remaining Amount</h3>
                <p class="text-2xl font-bold text-orange-900">{{ (data.goal.target_amount - data.goal.current_amount).toLocaleString() }} RWF</p>
                <p class="text-sm text-orange-700 mt-1">Still needed to complete goal</p>
              </div>
            </div>

            <!-- Goal Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Goal Details</h3>
                
                <div class="space-y-3 text-sm">
                  <div>
                    <p class="text-gray-500">Type</p>
                    <p class="font-medium capitalize">{{ data.goal.type.replace('_', ' ') }}</p>
                  </div>
                  
                  <div>
                    <p class="text-gray-500">Priority</p>
                    <p class="font-medium capitalize">{{ data.goal.priority }}</p>
                  </div>
                  
                  <div>
                    <p class="text-gray-500">Status</p>
                    <p class="font-medium capitalize">{{ data.goal.status.replace('_', ' ') }}</p>
                  </div>
                  
                  <div v-if="data.goal.is_overdue" class="bg-red-100 border border-red-200 rounded p-3">
                    <p class="text-red-800 text-sm font-medium">⚠️ This goal is overdue!</p>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                <p v-if="data.goal.notes" class="text-gray-600 text-sm">{{ data.goal.notes }}</p>
                <p v-else class="text-gray-400 text-sm italic">No notes added yet</p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
              <Link
                href="/goals"
                class="text-blue-600 hover:text-blue-800 font-medium"
              >
                ← Back to Goals
              </Link>
              
              <div class="flex space-x-2">
                <button 
                  @click="deleteGoal"
                  class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                >
                  Delete Goal
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Update Progress Modal -->
    <div v-if="showProgressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Update Progress</h3>
          
          <form @submit.prevent="submitProgress">
            <div class="mb-4">
              <label for="current_amount" class="block text-sm font-medium text-gray-700 mb-2">
                Current Amount (RWF)
              </label>
              <input
                id="current_amount"
                v-model="progressForm.current_amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              />
            </div>
            
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showProgressModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="progressForm.processing"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ progressForm.processing ? 'Updating...' : 'Update' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Add Contribution Modal -->
    <div v-if="showContributionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Add Contribution</h3>
          <p class="text-sm text-gray-600 mb-4">This amount will be deducted from your 20% savings budget.</p>
          
          <form @submit.prevent="submitContribution">
            <div class="mb-4">
              <label for="contribution_amount" class="block text-sm font-medium text-gray-700 mb-2">
                Contribution Amount (RWF) *
              </label>
              <input
                id="contribution_amount"
                v-model="contributionForm.amount"
                type="number"
                step="0.01"
                min="0.01"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                placeholder="Enter amount to contribute"
                required
              />
            </div>
            
            <div class="mb-4">
              <label for="contribution_date" class="block text-sm font-medium text-gray-700 mb-2">
                Contribution Date *
              </label>
              <input
                id="contribution_date"
                v-model="contributionForm.contribution_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                required
              />
            </div>
            
            <div class="mb-4">
              <label for="contribution_notes" class="block text-sm font-medium text-gray-700 mb-2">
                Notes (Optional)
              </label>
              <textarea
                id="contribution_notes"
                v-model="contributionForm.notes"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                placeholder="Add notes about this contribution..."
              ></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showContributionModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="contributionForm.processing"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
              >
                {{ contributionForm.processing ? 'Adding...' : 'Add Contribution' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

// Props
const props = defineProps({
  data: {
    user: Object,
    goal: Object
  }
})

// Reactive data
const showProgressModal = ref(false)
const showContributionModal = ref(false)

// Progress form
const progressForm = useForm({
  current_amount: props.data.goal.current_amount
})

// Contribution form
const contributionForm = useForm({
  amount: '',
  contribution_date: new Date().toISOString().split('T')[0],
  notes: ''
})

// Methods
const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'active':
      return 'bg-green-100 text-green-800'
    case 'completed':
      return 'bg-blue-100 text-blue-800'
    case 'paused':
      return 'bg-yellow-100 text-yellow-800'
    case 'cancelled':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getPriorityBadgeClass = (priority) => {
  switch (priority) {
    case 'urgent':
      return 'bg-red-100 text-red-800'
    case 'high':
      return 'bg-orange-100 text-orange-800'
    case 'medium':
      return 'bg-blue-100 text-blue-800'
    case 'low':
      return 'bg-gray-100 text-gray-800'
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

const updateProgress = () => {
  progressForm.current_amount = props.data.goal.current_amount
  showProgressModal.value = true
}

const submitProgress = () => {
  progressForm.post(`/goals/${props.data.goal.id}/progress`, {
    onSuccess: () => {
      showProgressModal.value = false
    },
    onError: (errors) => {
      console.error('Progress update errors:', errors)
    }
  })
}

const submitContribution = () => {
  contributionForm.post(`/goals/${props.data.goal.id}/contribute`, {
    onSuccess: () => {
      showContributionModal.value = false
      contributionForm.reset()
    },
    onError: (errors) => {
      console.error('Contribution errors:', errors)
    }
  })
}

const deleteGoal = () => {
  if (confirm(`Are you sure you want to delete "${props.data.goal.name}"?`)) {
    router.delete(`/goals/${props.data.goal.id}`, {
      onSuccess: () => {
        // Goal deleted successfully
      },
      onError: (errors) => {
        alert(errors.message || 'Error deleting goal')
      }
    })
  }
}
</script>