<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import SeoHead from '@/Components/SeoHead.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <SeoHead
          title="Log in"
          description="Sign in to your Tamba Group Budget Tracker account to access your financial dashboard, track expenses, and manage your budget."
          keywords="login, sign in, budget tracker login, financial dashboard login"
          noindex="true"
        />

        <!-- Status Message -->
        <div v-if="status" class="mb-6 p-4 bg-accent-success/10 border border-accent-success/20 rounded-lg">
            <p class="text-sm font-medium text-accent-success">{{ status }}</p>
        </div>

        <!-- Login Form -->
        <div class="card max-w-md mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-primary-text mb-2">Welcome Back</h1>
                <p class="text-secondary-text">Sign in to your budgeting account</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <InputLabel for="email" value="Email Address" class="text-primary-text font-medium" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-2 block w-full border-primary-border focus:border-secondary-brand focus:ring-secondary-brand"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Enter your email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Password Field -->
                <div>
                    <InputLabel for="password" value="Password" class="text-primary-text font-medium" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-2 block w-full border-primary-border focus:border-secondary-brand focus:ring-secondary-brand"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-secondary-text">Remember me</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-secondary-brand hover:text-secondary-accent font-medium transition-colors"
                    >
                        Forgot password?
                    </Link>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <PrimaryButton 
                        class="w-full btn-primary justify-center" 
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Signing in...
                        </span>
                        <span v-else>Sign In</span>
                    </PrimaryButton>
                </div>

                <!-- Sign Up Link -->
                <div class="text-center pt-4 border-t border-primary-border">
                    <p class="text-sm text-secondary-text">
                        Don't have an account?
                        <Link :href="route('register')" class="text-secondary-brand hover:text-secondary-accent font-medium transition-colors">
                            Sign up here
                        </Link>
                    </p>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
