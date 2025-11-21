<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-bg to-secondary-surface">
    <SeoHead
      title="Tamba Group - Budget Tracker | Master the 50/30/20 Rule"
      description="Take control of your finances with Tamba Group's intuitive budget tracker. Master the 50/30/20 budgeting rule, track expenses, manage income, and achieve your financial goals. Free budget planning tool with smart analytics."
      keywords="budget tracker, 50 30 20 rule, personal finance, expense tracking, financial planning, budgeting app, money management, savings goals, income tracking, financial analytics, Tamba Group, budget calculator, expense manager, financial goals, smart budgeting"
      url="/"
    />
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-sm border-b border-primary-border">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center">
            <img src="/tambagroup.png" alt="Tamba Group" class="h-10 w-auto mr-3" />
            <h1 class="text-2xl font-bold text-primary-text">Tamba Group</h1>
          </div>
          <div class="flex items-center space-x-4">
            <Link :href="route('login')" class="text-secondary-text hover:text-secondary-brand font-medium transition-colors">
              Sign In
            </Link>
            <Link :href="route('register')" class="btn-primary">
              Get Started
            </Link>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="text-center">
        <!-- Logo Section -->
        <div class="mb-8">
          <div class="flex items-center justify-center mb-4">
            <img src="/tambagroup.png" alt="Tamba Group" class="h-24 w-auto" />
          </div>
          <p class="text-lg text-secondary-text">Master the 50/30/20 Budgeting Rule</p>
        </div>
        
        <h1 class="text-5xl font-bold text-primary-text mb-6">
          Take Control of Your
          <span class="text-secondary-brand">Finances</span>
        </h1>
        <p class="text-xl text-secondary-text mb-8 max-w-3xl mx-auto">
          Master the 50/30/20 budgeting rule with our intuitive platform. Track expenses, manage income, and achieve your financial goals with ease.
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
          <Link :href="route('register')" class="btn-primary text-lg px-8 py-4">
            Start Your Free Account
          </Link>
          <Link :href="route('login')" class="btn-secondary text-lg px-8 py-4">
            Sign In
          </Link>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
          <div class="card text-center">
            <div class="w-16 h-16 bg-accent-success/10 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-accent-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-primary-text mb-2">50/30/20 Rule</h3>
            <p class="text-secondary-text">Automatically allocate your income into needs (50%), wants (30%), and savings (20%)</p>
          </div>

          <div class="card text-center">
            <div class="w-16 h-16 bg-accent-info/10 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-accent-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-primary-text mb-2">Smart Analytics</h3>
            <p class="text-secondary-text">Visualize your spending patterns with interactive charts and detailed reports</p>
          </div>

          <div class="card text-center">
            <div class="w-16 h-16 bg-accent-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-accent-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-primary-text mb-2">Mobile Ready</h3>
            <p class="text-secondary-text">Access your budget anywhere with our responsive, mobile-first design</p>
          </div>
        </div>

        <!-- How It Works -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-16">
          <h2 class="text-3xl font-bold text-primary-text mb-8">How It Works</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
              <div class="w-12 h-12 bg-gray-800 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">1</div>
              <h3 class="font-semibold text-primary-text mb-2">Sign Up</h3>
              <p class="text-sm text-secondary-text">Create your free account in seconds</p>
            </div>
            <div class="text-center">
              <div class="w-12 h-12 bg-gray-800 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">2</div>
              <h3 class="font-semibold text-primary-text mb-2">Set Budget</h3>
              <p class="text-sm text-secondary-text">Configure your 50/30/20 allocation</p>
            </div>
            <div class="text-center">
              <div class="w-12 h-12 bg-gray-800 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">3</div>
              <h3 class="font-semibold text-primary-text mb-2">Track Expenses</h3>
              <p class="text-sm text-secondary-text">Record your income and expenses</p>
            </div>
            <div class="text-center">
              <div class="w-12 h-12 bg-gray-800 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">4</div>
              <h3 class="font-semibold text-primary-text mb-2">Achieve Goals</h3>
              <p class="text-sm text-secondary-text">Watch your financial health improve</p>
            </div>
          </div>
        </div>

        <!-- Final CTA -->
        <div class="bg-gray-800 rounded-2xl p-8 text-white">
          <div class="flex items-center justify-center mb-4">
            <img src="/tambagroup.png" alt="Tamba Group" class="h-12 w-auto mr-3" />
            <h2 class="text-3xl font-bold">Ready to Transform Your Finances?</h2>
          </div>
          <p class="text-xl mb-6 opacity-90">Join thousands of users who have mastered the 50/30/20 budgeting rule with our trusted solution</p>
          <Link :href="route('register')" class="bg-white text-gray-800 hover:bg-gray-100 font-semibold py-3 px-8 rounded-lg transition-colors">
            Get Started Free
          </Link>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-primary-border py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="flex items-center justify-center mb-4">
            <img src="/tambagroup.png" alt="Tamba Group" class="h-6 w-auto mr-2" />
            <span class="text-secondary-text">© 2024 Tamba Group. All rights reserved.</span>
        </div>
        <p class="text-sm text-secondary-text">Master the 50/30/20 Budgeting Rule</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
})
</script>
