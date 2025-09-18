<template>
  <AuthenticatedLayout :user="data.user">
    <Head title="Financial Goals" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Financial Goals</h1>
                <p class="text-gray-600 mt-1">Set and track your financial objectives</p>
              </div>
              <Link 
                href="/goals/create"
                  class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors"
              >
                Add New Goal
              </Link>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600">Total Goals</p>
                    <p class="text-2xl font-bold text-blue-900">{{ data.summary.total_goals }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-green-600">Active Goals</p>
                    <p class="text-2xl font-bold text-green-900">{{ data.summary.active_goals }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-purple-600">Target Amount</p>
                    <p class="text-2xl font-bold text-purple-900">{{ data.summary.total_target_amount.toLocaleString() }} RWF</p>
                  </div>
                </div>
              </div>

              <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-orange-600">Overall Progress</p>
                    <p class="text-2xl font-bold text-orange-900">{{ data.summary.overall_progress.toFixed(1) }}%</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Goals List -->
            <div v-if="data.goals.length > 0" class="space-y-4">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Goals</h2>
              
              <div 
                v-for="goal in data.goals" 
                :key="goal.id"
                class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
              >
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                      <span class="text-2xl">{{ goal.icon }}</span>
                      <h3 class="text-lg font-semibold text-gray-900">{{ goal.name }}</h3>
                      <span 
                        :class="getStatusBadgeClass(goal.status)"
                        class="px-2 py-1 text-xs font-medium rounded-full"
                      >
                        {{ goal.status.replace('_', ' ').toUpperCase() }}
                      </span>
                      <span 
                        :class="getPriorityBadgeClass(goal.priority)"
                        class="px-2 py-1 text-xs font-medium rounded-full"
                      >
                        {{ goal.priority.toUpperCase() }}
                      </span>
                    </div>
                    
                    <p v-if="goal.description" class="text-gray-600 mb-4">{{ goal.description }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                      <div>
                        <p class="text-gray-500">Target Amount</p>
                        <p class="font-medium">{{ goal.target_amount.toLocaleString() }} RWF</p>
                      </div>
                      
                      <div>
                        <p class="text-gray-500">Current Amount</p>
                        <p class="font-medium text-green-600">{{ goal.current_amount.toLocaleString() }} RWF</p>
                      </div>
                      
                      <div>
                        <p class="text-gray-500">Target Date</p>
                        <p class="font-medium">{{ formatDate(goal.target_date) }}</p>
                        <p class="text-xs text-gray-500">{{ goal.days_remaining }} days remaining</p>
                      </div>
                      
                      <div>
                        <p class="text-gray-500">Progress</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                          <div 
                            class="h-2 rounded-full transition-all duration-300" 
                            :style="`width: ${goal.progress_percentage}%; background-color: ${goal.color}`"
                          ></div>
                        </div>
                        <p class="text-xs text-gray-500">{{ goal.progress_percentage.toFixed(1) }}% complete</p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex space-x-2 ml-4">
                    <Link 
                      :href="`/goals/${goal.id}`"
                      class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                    >
                      View
                    </Link>
                    <Link 
                      :href="`/goals/${goal.id}/edit`"
                      class="text-gray-600 hover:text-gray-800 text-sm font-medium"
                    >
                      Edit
                    </Link>
                    <button 
                      @click="deleteGoal(goal)"
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No goals yet</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by setting your first financial goal.</p>
              <div class="mt-6">
                <Link 
                  href="/goals/create"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700"
                >
                  Add New Goal
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
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  data: {
    user: Object,
    goals: Array,
    summary: Object
  }
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

const deleteGoal = (goal) => {
  if (confirm(`Are you sure you want to delete "${goal.name}"?`)) {
    router.delete(`/goals/${goal.id}`, {
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