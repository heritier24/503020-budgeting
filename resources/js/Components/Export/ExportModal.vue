<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-secondary-brand/10 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-secondary-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-primary-text">
                Export Data
              </h3>
              <div class="mt-2">
                <p class="text-sm text-secondary-text">
                  Choose what data you'd like to export and the date range.
                </p>
              </div>
            </div>
          </div>

          <!-- Export Form -->
          <form @submit.prevent="handleExport" class="mt-6 space-y-4">
            <!-- Export Type -->
            <div>
              <label class="block text-sm font-medium text-primary-text mb-2">
                Export Type
              </label>
              <select 
                v-model="exportType" 
                class="w-full border border-primary-border rounded-md px-3 py-2 focus:border-secondary-brand focus:ring-secondary-brand"
                required
              >
                <option value="">Select export type</option>
                <option value="transactions">Transactions</option>
                <option value="income">Income Records</option>
                <option value="budget-report">Budget Report</option>
              </select>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-primary-text mb-2">
                  Start Date
                </label>
                <input
                  v-model="startDate"
                  type="date"
                  class="w-full border border-primary-border rounded-md px-3 py-2 focus:border-secondary-brand focus:ring-secondary-brand"
                  required
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-primary-text mb-2">
                  End Date
                </label>
                <input
                  v-model="endDate"
                  type="date"
                  class="w-full border border-primary-border rounded-md px-3 py-2 focus:border-secondary-brand focus:ring-secondary-brand"
                  required
                />
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center py-4">
              <svg class="animate-spin h-5 w-5 text-secondary-brand mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-sm text-secondary-text">Exporting data...</span>
            </div>

            <!-- Success State -->
            <div v-if="downloadUrl" class="p-4 bg-accent-success/10 border border-accent-success/20 rounded-lg">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-accent-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-sm font-medium text-accent-success">Export completed successfully!</span>
              </div>
              <div class="mt-2">
                <a 
                  :href="downloadUrl" 
                  :download="filename"
                  class="text-sm text-secondary-brand hover:text-secondary-accent font-medium"
                >
                  Download {{ filename }}
                </a>
              </div>
            </div>

            <!-- Error State -->
            <div v-if="error" class="p-4 bg-accent-danger/10 border border-accent-danger/20 rounded-lg">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-accent-danger mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="text-sm font-medium text-accent-danger">{{ error }}</span>
              </div>
            </div>
          </form>
        </div>

        <!-- Modal Actions -->
        <div class="bg-secondary-surface px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            v-if="!downloadUrl"
            type="button"
            @click="handleExport"
            :disabled="loading || !exportType || !startDate || !endDate"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-secondary-brand text-base font-medium text-white hover:bg-secondary-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-brand sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Export Data
          </button>
          <button
            type="button"
            @click="closeModal"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-primary-border shadow-sm px-4 py-2 bg-white text-base font-medium text-secondary-text hover:bg-secondary-surface focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-brand sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            {{ downloadUrl ? 'Close' : 'Cancel' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

// Props
const props = defineProps<{
  isOpen: boolean
}>()

// Emits
const emit = defineEmits<{
  close: []
}>()

// Reactive data
const exportType = ref('')
const startDate = ref('')
const endDate = ref('')
const loading = ref(false)
const downloadUrl = ref('')
const filename = ref('')
const error = ref('')

// Methods
const closeModal = () => {
  emit('close')
  resetForm()
}

const resetForm = () => {
  exportType.value = ''
  startDate.value = ''
  endDate.value = ''
  loading.value = false
  downloadUrl.value = ''
  filename.value = ''
  error.value = ''
}

const handleExport = async () => {
  if (!exportType.value || !startDate.value || !endDate.value) {
    return
  }

  loading.value = true
  error.value = ''
  downloadUrl.value = ''

  try {
    const response = await fetch(`/api/exports/${exportType.value}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
      },
      body: JSON.stringify({
        start_date: startDate.value,
        end_date: endDate.value,
      }),
    })

    const data = await response.json()

    if (data.success) {
      downloadUrl.value = data.data.download_url
      filename.value = data.data.filename
    } else {
      error.value = data.message || 'Export failed'
    }
  } catch (err) {
    error.value = 'Network error occurred'
    console.error('Export error:', err)
  } finally {
    loading.value = false
  }
}

// Set default date range (last 30 days)
const setDefaultDateRange = () => {
  const end = new Date()
  const start = new Date()
  start.setDate(start.getDate() - 30)
  
  endDate.value = end.toISOString().split('T')[0]
  startDate.value = start.toISOString().split('T')[0]
}

// Watch for modal open to set default dates
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    setDefaultDateRange()
  }
})
</script>

<style scoped>
/* Modal styles */
</style>