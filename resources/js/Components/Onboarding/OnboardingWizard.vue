<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-lg">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-bold">Welcome to Tamba BudgetTracker</h2>
          <button @click="$emit('close')" class="text-white hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <p class="text-blue-100 mt-2">Let's set up your personalized budgeting system</p>
      </div>

      <!-- Progress Bar -->
      <div class="px-6 py-4 bg-gray-50">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700">Step {{ currentStep }} of 5</span>
          <span class="text-sm text-gray-500">{{ Math.round((currentStep / 5) * 100) }}% Complete</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
               :style="{ width: `${(currentStep / 5) * 100}%` }"></div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6">
        <!-- Step 1: Welcome -->
        <div v-if="currentStep === 1" class="text-center">
          <div class="mb-6">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Welcome to Smart Budgeting!</h3>
            <p class="text-gray-600">We'll help you set up a personalized budget using the proven 50/30/20 rule to manage your finances effectively.</p>
          </div>
        </div>

        <!-- Step 2: Budget Configuration -->
        <div v-if="currentStep === 2">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Configure Your Budget Allocation</h3>
          <p class="text-gray-600 mb-6">Set your budget percentages. The total should equal 100%.</p>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Needs (Essential expenses)</label>
              <div class="flex items-center space-x-3">
                <input 
                  v-model.number="budgetConfig.needs" 
                  type="number" 
                  min="0" 
                  max="100" 
                  step="1"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="50"
                >
                <span class="text-gray-500">%</span>
              </div>
              <p class="text-sm text-gray-500 mt-1">Housing, food, utilities, transportation</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Wants (Lifestyle expenses)</label>
              <div class="flex items-center space-x-3">
                <input 
                  v-model.number="budgetConfig.wants" 
                  type="number" 
                  min="0" 
                  max="100" 
                  step="1"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="30"
                >
                <span class="text-gray-500">%</span>
              </div>
              <p class="text-sm text-gray-500 mt-1">Entertainment, dining out, hobbies</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Savings & Debt</label>
              <div class="flex items-center space-x-3">
                <input 
                  v-model.number="budgetConfig.savings" 
                  type="number" 
                  min="0" 
                  max="100" 
                  step="1"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="20"
                >
                <span class="text-gray-500">%</span>
              </div>
              <p class="text-sm text-gray-500 mt-1">Emergency fund, investments, debt payments</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="flex justify-between items-center">
                <span class="font-medium text-gray-700">Total:</span>
                <span :class="totalPercentage === 100 ? 'text-green-600' : 'text-red-600'" class="font-bold">
                  {{ totalPercentage }}%
                </span>
              </div>
              <p v-if="totalPercentage !== 100" class="text-sm text-red-600 mt-1">
                Please ensure the total equals 100%
              </p>
            </div>
          </div>
        </div>

        <!-- Step 3: Income Sources -->
        <div v-if="currentStep === 3">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Add Your Income Sources</h3>
          <p class="text-gray-600 mb-6">Tell us about your income sources to calculate your budget amounts.</p>
          
          <div class="space-y-4">
            <div v-for="(source, index) in incomeSources" :key="index" class="border border-gray-200 rounded-lg p-4">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Source Name</label>
                  <input 
                    v-model="source.name" 
                    type="text" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., Salary, Freelance"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                  <select 
                    v-model="source.type" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="salary">Salary</option>
                    <option value="freelance">Freelance</option>
                    <option value="business">Business</option>
                    <option value="investment">Investment</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Amount (RWF)</label>
                  <input 
                    v-model.number="source.expected_monthly" 
                    type="number" 
                    min="0" 
                    step="1000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="500000"
                  >
                </div>
              </div>
              <button 
                v-if="incomeSources.length > 1" 
                @click="removeIncomeSource(index)" 
                class="mt-2 text-red-600 hover:text-red-800 text-sm"
              >
                Remove Source
              </button>
            </div>
            
            <button 
              @click="addIncomeSource" 
              class="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors"
            >
              + Add Another Income Source
            </button>
          </div>
        </div>

        <!-- Step 4: First Transaction (Optional) -->
        <div v-if="currentStep === 4">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Add Your First Transaction (Optional)</h3>
          <p class="text-gray-600 mb-6">You can add your first transaction now or skip this step and add it later.</p>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <input 
                v-model="firstTransaction.description" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="e.g., Grocery shopping, Salary payment"
              >
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount (RWF)</label>
                <input 
                  v-model.number="firstTransaction.amount" 
                  type="number" 
                  min="0" 
                  step="100"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="50000"
                >
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select 
                  v-model="firstTransaction.type" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="expense">Expense</option>
                  <option value="income">Income</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 5: Summary -->
        <div v-if="currentStep === 5">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Review Your Setup</h3>
          <p class="text-gray-600 mb-6">Please review your budget configuration before we complete the setup.</p>
          
          <div class="space-y-6">
            <!-- Budget Summary -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="font-semibold text-gray-900 mb-3">Budget Allocation</h4>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span>Needs:</span>
                  <span class="font-medium">{{ budgetConfig.needs }}%</span>
                </div>
                <div class="flex justify-between">
                  <span>Wants:</span>
                  <span class="font-medium">{{ budgetConfig.wants }}%</span>
                </div>
                <div class="flex justify-between">
                  <span>Savings:</span>
                  <span class="font-medium">{{ budgetConfig.savings }}%</span>
                </div>
              </div>
            </div>

            <!-- Income Summary -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="font-semibold text-gray-900 mb-3">Income Sources</h4>
              <div class="space-y-2">
                <div v-for="source in incomeSources.filter(s => s.name && s.expected_monthly)" :key="source.name" class="flex justify-between">
                  <span>{{ source.name }} ({{ source.type }})</span>
                  <span class="font-medium">RWF {{ source.expected_monthly?.toLocaleString() }}</span>
                </div>
                <div class="border-t pt-2 flex justify-between font-semibold">
                  <span>Total Monthly Income:</span>
                  <span>RWF {{ totalMonthlyIncome.toLocaleString() }}</span>
                </div>
              </div>
            </div>

            <!-- First Transaction Summary -->
            <div v-if="firstTransaction.description && firstTransaction.amount" class="bg-gray-50 p-4 rounded-lg">
              <h4 class="font-semibold text-gray-900 mb-3">First Transaction</h4>
              <div class="flex justify-between">
                <span>{{ firstTransaction.description }}</span>
                <span class="font-medium">{{ firstTransaction.type === 'expense' ? '-' : '+' }}RWF {{ firstTransaction.amount?.toLocaleString() }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-between items-center">
        <button 
          v-if="currentStep > 1" 
          @click="previousStep" 
          class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors"
        >
          ← Previous
        </button>
        <div v-else></div>

        <div class="flex space-x-3">
          <button 
            v-if="currentStep < 5" 
            @click="nextStep" 
            :disabled="!canProceed"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
          >
            Next →
          </button>
          
          <button 
            v-if="currentStep === 5" 
            @click="completeOnboarding" 
            :disabled="isSaving"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
          >
            <svg v-if="isSaving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isSaving ? saveProgress || 'Completing...' : 'Get Started' }}</span>
          </button>
        </div>
      </div>

      <!-- Notification Popup -->
      <div v-if="showNotification" 
           :class="[
             'fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm',
             notificationType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'
           ]">
        <div class="flex items-center">
          <svg v-if="notificationType === 'success'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          <svg v-else class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
          <span class="text-sm font-medium">{{ notificationMessage }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

// Props
defineProps<{
  isOpen: boolean
}>()

// Emits
defineEmits<{
  close: []
}>()

// Reactive data
const currentStep = ref(1)
const isSaving = ref(false)
const saveProgress = ref('')
const showNotification = ref(false)
const notificationMessage = ref('')
const notificationType = ref<'success' | 'error'>('success')

// Budget configuration
const budgetConfig = ref({
  needs: 50,
  wants: 30,
  savings: 20
})

// Income sources
const incomeSources = ref([
  {
    name: '',
    type: 'salary',
    expected_monthly: null as number | null
  }
])

// First transaction
const firstTransaction = ref({
  description: '',
  amount: null as number | null,
  type: 'expense'
})

// Computed properties
const totalPercentage = computed(() => {
  return budgetConfig.value.needs + budgetConfig.value.wants + budgetConfig.value.savings
})

const totalMonthlyIncome = computed(() => {
  return incomeSources.value
    .filter(source => source.expected_monthly)
    .reduce((total, source) => total + (source.expected_monthly || 0), 0)
})

const canProceed = computed(() => {
  switch (currentStep.value) {
    case 1:
      return true
    case 2:
      return Math.abs(totalPercentage.value - 100) <= 1 // Allow 1% tolerance
    case 3:
      return incomeSources.value.some(source => 
        source.name.trim() && source.expected_monthly && source.expected_monthly > 0
      )
    case 4:
      return true // Optional step
    case 5:
      return true
    default:
      return false
  }
})

// Methods
const nextStep = () => {
  if (canProceed.value && currentStep.value < 5) {
    currentStep.value++
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

const addIncomeSource = () => {
  incomeSources.value.push({
    name: '',
    type: 'salary',
    expected_monthly: null
  })
}

const removeIncomeSource = (index: number) => {
  if (incomeSources.value.length > 1) {
    incomeSources.value.splice(index, 1)
  }
}

const showSuccessNotification = (message: string) => {
  notificationMessage.value = message
  notificationType.value = 'success'
  showNotification.value = true
  setTimeout(() => {
    showNotification.value = false
  }, 5000)
}

const showErrorNotification = (message: string) => {
  notificationMessage.value = message
  notificationType.value = 'error'
  showNotification.value = true
  setTimeout(() => {
    showNotification.value = false
  }, 8000)
}

const completeOnboarding = () => {
  if (isSaving.value) return // Prevent multiple clicks
  
  isSaving.value = true
  saveProgress.value = 'Completing setup...'
  
  // Prepare data for submission
  const formData = {
    budget_config: {
      needs: budgetConfig.value.needs,
      wants: budgetConfig.value.wants,
      savings: budgetConfig.value.savings,
    },
    income_sources: incomeSources.value.filter(source => 
      source.name.trim() && source.expected_monthly
    ).map(source => ({
      name: source.name.trim(),
      type: source.type,
      expected_monthly: parseFloat(source.expected_monthly?.toString() || '0'),
    })),
    first_transaction: firstTransaction.value.description && firstTransaction.value.amount ? {
      description: firstTransaction.value.description.trim(),
      amount: parseFloat(firstTransaction.value.amount?.toString() || '0'),
      type: firstTransaction.value.type,
    } : null
  }
  
  console.log('Submitting onboarding data:', formData)
  
  // Use Inertia.js to submit the form
  router.post('/onboarding/complete', formData, {
    onStart: () => {
      saveProgress.value = 'Saving your data...'
    },
    onSuccess: () => {
      showSuccessNotification('Setup completed successfully! Redirecting to dashboard...')
      setTimeout(() => {
        isSaving.value = false
        saveProgress.value = ''
      }, 2000)
    },
    onError: (errors) => {
      console.error('Onboarding completion failed:', errors)
      isSaving.value = false
      saveProgress.value = ''
      
      // Show specific error messages
      if (errors.setup) {
        showErrorNotification(errors.setup)
      } else if (errors.budget_config) {
        showErrorNotification('Budget configuration error: ' + Object.values(errors.budget_config).join(', '))
      } else if (errors.income_sources) {
        showErrorNotification('Income sources error: ' + Object.values(errors.income_sources).join(', '))
      } else {
        showErrorNotification('Setup failed. Please check your inputs and try again.')
      }
    },
    onFinish: () => {
      // This runs after success or error
    }
  })
}
</script>

<style scoped>
/* Custom styles if needed */
</style>