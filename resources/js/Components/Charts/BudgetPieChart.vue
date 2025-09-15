<template>
  <div class="bg-white p-6 rounded-lg border border-gray-200">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Budget Allocation</h3>
      <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-2">
          <div class="w-3 h-3 bg-red-500 rounded-full"></div>
          <span class="text-sm text-gray-600">Needs</span>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
          <span class="text-sm text-gray-600">Wants</span>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-3 h-3 bg-green-500 rounded-full"></div>
          <span class="text-sm text-gray-600">Savings</span>
        </div>
      </div>
    </div>
    
    <!-- Simple Visual Representation -->
    <div class="mb-6">
      <div class="flex h-8 rounded-lg overflow-hidden">
        <div 
          class="bg-red-500 flex items-center justify-center text-white text-sm font-medium"
          :style="`width: ${needsPercentage}%`"
        >
          {{ needsPercentage }}%
        </div>
        <div 
          class="bg-yellow-500 flex items-center justify-center text-white text-sm font-medium"
          :style="`width: ${wantsPercentage}%`"
        >
          {{ wantsPercentage }}%
        </div>
        <div 
          class="bg-green-500 flex items-center justify-center text-white text-sm font-medium"
          :style="`width: ${savingsPercentage}%`"
        >
          {{ savingsPercentage }}%
        </div>
      </div>
    </div>
    
    <div class="grid grid-cols-3 gap-4">
      <div class="text-center p-4 bg-red-50 rounded-lg">
        <p class="text-2xl font-bold text-red-600">{{ needsPercentage }}%</p>
        <p class="text-sm text-gray-600">Needs</p>
        <p class="text-xs text-gray-800">RWF {{ needsAmount?.toLocaleString() }}</p>
      </div>
      <div class="text-center p-4 bg-yellow-50 rounded-lg">
        <p class="text-2xl font-bold text-yellow-600">{{ wantsPercentage }}%</p>
        <p class="text-sm text-gray-600">Wants</p>
        <p class="text-xs text-gray-800">RWF {{ wantsAmount?.toLocaleString() }}</p>
      </div>
      <div class="text-center p-4 bg-green-50 rounded-lg">
        <p class="text-2xl font-bold text-green-600">{{ savingsPercentage }}%</p>
        <p class="text-sm text-gray-600">Savings</p>
        <p class="text-xs text-gray-800">RWF {{ savingsAmount?.toLocaleString() }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

// Props
const props = defineProps<{
  data?: {
    needs: { percentage: number; amount: number }
    wants: { percentage: number; amount: number }
    savings: { percentage: number; amount: number }
  }
}>()

// Computed properties
const needsPercentage = computed(() => props.data?.needs?.percentage || 50)
const wantsPercentage = computed(() => props.data?.wants?.percentage || 30)
const savingsPercentage = computed(() => props.data?.savings?.percentage || 20)

const needsAmount = computed(() => props.data?.needs?.amount || 0)
const wantsAmount = computed(() => props.data?.wants?.amount || 0)
const savingsAmount = computed(() => props.data?.savings?.amount || 0)
</script>

<style scoped>
/* Chart container styles */
canvas {
  max-height: 300px;
}
</style>