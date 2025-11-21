<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import OnboardingWizard from '@/Components/Onboarding/OnboardingWizard.vue'
import SeoHead from '@/Components/SeoHead.vue'
import { ref } from 'vue'

// Props
defineProps({
  user: Object
})

// Reactive data
const showOnboarding = ref(true)

// Methods
const handleOnboardingComplete = () => {
  showOnboarding.value = false
  // The OnboardingWizard will handle the redirect via Inertia.js
}
</script>

<template>
    <SeoHead
      title="Onboarding"
      description="Complete your budget setup in just a few steps. Configure your 50/30/20 allocation, add income sources, and start tracking your finances."
      keywords="onboarding, budget setup, initial setup, configure budget, budget configuration"
      noindex="true"
    />

    <AuthenticatedLayout :user="user">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-2xl text-primary-text leading-tight">
                        Welcome to Your Budgeting Journey!
                    </h2>
                    <p class="text-secondary-text mt-1">Let's set up your budget in just a few steps</p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Onboarding Content -->
                <div class="text-center py-12">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-secondary-brand/10 mb-6">
                        <svg class="h-10 w-10 text-secondary-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-primary-text mb-4">Ready to Get Started?</h3>
                    <p class="text-secondary-text mb-8 max-w-2xl mx-auto">
                        Our setup wizard will guide you through configuring your budget, adding income sources, and recording your first transaction. This will only take a few minutes.
                    </p>
                    <button 
                        @click="showOnboarding = true"
                        class="btn-primary text-lg px-8 py-3"
                    >
                        Start Setup Wizard
                    </button>
                </div>
            </div>
        </div>

        <!-- Onboarding Wizard -->
        <OnboardingWizard 
            :is-open="showOnboarding" 
            @close="showOnboarding = false"
            @complete="handleOnboardingComplete"
        />
    </AuthenticatedLayout>
</template>