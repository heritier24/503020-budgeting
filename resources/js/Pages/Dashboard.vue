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
defineProps<{
  data: {
    user: any
    budget_config: any
    income_sources: any[]
    monthly_income: number
    available_income: number
    current_expenses: number
    current_income: number
    remaining_budget: number
    loan_info: {
      total_monthly_payments: number
      total_debt: number
      active_loans_count: number
    }
    goals_info: {
      active_goals_count: number
      total_contributions: number
      savings_goals: {
        count: number
        target_amount: number
        current_amount: number
        progress_percentage: number
      }
      debt_payoff_goals: {
        count: number
        target_amount: number
        current_amount: number
        progress_percentage: number
      }
      expense_reduction_goals_count: number
      income_goals_count: number
    }
    budget_allocation: {
      needs: { percentage: number; amount: number; spent: number }
      wants: { percentage: number; amount: number; spent: number }
      savings: { 
        percentage: number
        amount: number
        spent: number
        spent_with_goals: number
        goal_contributions: number
      }
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