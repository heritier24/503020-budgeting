<template>
  <div class="bg-white p-6 rounded-lg border border-gray-200">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Spending Trends</h3>
      <div class="flex items-center space-x-4">
        <select 
          v-model="selectedPeriod" 
          @change="updateData"
          class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:border-blue-500 focus:ring-blue-500"
        >
          <option value="7">Last 7 days</option>
          <option value="30">Last 30 days</option>
          <option value="90">Last 90 days</option>
        </select>
      </div>
    </div>
    
    <!-- Simple Bar Chart Representation -->
    <div class="mb-6">
      <div class="flex items-end justify-between h-48 space-x-2">
        <div 
          v-for="(day, index) in chartData" 
          :key="index"
          class="flex flex-col items-center flex-1"
        >
          <div 
            class="bg-blue-500 rounded-t w-full transition-all duration-300 hover:bg-blue-600"
            :style="`height: ${(day.amount / maxAmount) * 100}%`"
            :title="`${day.date}: RWF ${day.amount.toLocaleString()}`"
          ></div>
          <span class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-left">
            {{ day.date }}
          </span>
        </div>
      </div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="text-center p-4 bg-blue-50 rounded-lg">
        <p class="text-lg font-bold text-blue-600">RWF {{ totalSpent?.toLocaleString() }}</p>
        <p class="text-sm text-gray-600">Total Spent</p>
      </div>
      <div class="text-center p-4 bg-green-50 rounded-lg">
        <p class="text-lg font-bold text-green-600">RWF {{ averageDaily?.toLocaleString() }}</p>
        <p class="text-sm text-gray-600">Daily Average</p>
      </div>
      <div class="text-center p-4 rounded-lg" :class="trendDirection === 'up' ? 'bg-red-50' : 'bg-green-50'">
        <p class="text-lg font-bold" :class="trendDirection === 'up' ? 'text-red-600' : 'text-green-600'">
          {{ trendPercentage }}%
        </p>
        <p class="text-sm text-gray-600">vs Previous</p>
      </div>
      <div class="text-center p-4 bg-purple-50 rounded-lg">
        <p class="text-lg font-bold text-purple-600">RWF {{ highestDay?.toLocaleString() }}</p>
        <p class="text-sm text-gray-600">Highest Day</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

// Props
const props = defineProps<{
  data?: Array<{
    date: string
    amount: number
    category: string
  }>
}>()

// Refs
const selectedPeriod = ref('30')

// Generate sample data for demonstration
const chartData = computed(() => {
  if (props.data && props.data.length > 0) {
    // Convert spending trends data to chart format
    return props.data.slice(0, parseInt(selectedPeriod.value)).map(item => ({
      date: item.month || item.date || 'Unknown',
      amount: item.expenses || item.amount || 0
    }))
  }
  
  // Generate sample data if no real data
  const days = parseInt(selectedPeriod.value)
  const sampleData = []
  for (let i = days - 1; i >= 0; i--) {
    const date = new Date()
    date.setDate(date.getDate() - i)
    sampleData.push({
      date: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
      amount: Math.floor(Math.random() * 50000) + 10000 // Random amount between 10k-60k
    })
  }
  return sampleData
})

const maxAmount = computed(() => {
  return Math.max(...chartData.value.map(d => d.amount), 1)
})

const totalSpent = computed(() => {
  return chartData.value.reduce((sum, day) => sum + day.amount, 0)
})

const averageDaily = computed(() => {
  return chartData.value.length > 0 ? Math.round(totalSpent.value / chartData.value.length) : 0
})

const trendPercentage = computed(() => {
  if (chartData.value.length < 2) return 0
  
  const firstHalf = chartData.value.slice(0, Math.floor(chartData.value.length / 2))
  const secondHalf = chartData.value.slice(Math.floor(chartData.value.length / 2))
  
  const firstAvg = firstHalf.reduce((sum, day) => sum + day.amount, 0) / firstHalf.length
  const secondAvg = secondHalf.reduce((sum, day) => sum + day.amount, 0) / secondHalf.length
  
  return Math.round(((secondAvg - firstAvg) / firstAvg) * 100)
})

const trendDirection = computed(() => {
  return trendPercentage.value >= 0 ? 'up' : 'down'
})

const highestDay = computed(() => {
  return Math.max(...chartData.value.map(d => d.amount), 0)
})

const updateData = () => {
  // This will trigger reactivity and update the chart
}
</script>
