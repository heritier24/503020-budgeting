<template>
  <AuthenticatedLayout :user="data.user">
    <Head title="Dashboard" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="mb-6">
              <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
              <p class="text-gray-600 mt-2">Welcome back, {{ data.user.name }}! Here's your financial overview.</p>
            </div>

            <!-- Dashboard Overview Component -->
            <DashboardOverview :data="data" @refresh="handleRefresh" />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DashboardOverview from '@/Components/Dashboard/DashboardOverview.vue'
import { Head } from '@inertiajs/vue3'

// Props
const props = defineProps<{
  data: {
    user: any
    budget_config: any
    income_sources: any[]
    monthly_income: number
    current_expenses: number
    current_income: number
    remaining_budget: number
    budget_allocation: {
      needs: { percentage: number; amount: number; spent: number }
      wants: { percentage: number; amount: number; spent: number }
      savings: { percentage: number; amount: number; spent: number }
    }
    recent_transactions: any[]
    categories: any[]
  }
}>()

const handleRefresh = () => {
  // Refresh the page to get updated data
  window.location.reload()
}
</script>