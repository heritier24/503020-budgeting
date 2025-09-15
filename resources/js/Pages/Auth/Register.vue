<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <!-- Registration Form -->
        <div class="card max-w-md mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-primary-text mb-2">Create Account</h1>
                <p class="text-secondary-text">Start managing your budget today</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Name Field -->
                <div>
                    <InputLabel for="name" value="Full Name" class="text-primary-text font-medium" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-2 block w-full border-primary-border focus:border-secondary-brand focus:ring-secondary-brand"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter your full name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email Field -->
                <div>
                    <InputLabel for="email" value="Email Address" class="text-primary-text font-medium" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-2 block w-full border-primary-border focus:border-secondary-brand focus:ring-secondary-brand"
                        v-model="form.email"
                        required
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
                        autocomplete="new-password"
                        placeholder="Create a strong password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <InputLabel for="password_confirmation" value="Confirm Password" class="text-primary-text font-medium" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-2 block w-full border-primary-border focus:border-secondary-brand focus:ring-secondary-brand"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
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
                            Creating account...
                        </span>
                        <span v-else>Create Account</span>
                    </PrimaryButton>
                </div>

                <!-- Sign In Link -->
                <div class="text-center pt-4 border-t border-primary-border">
                    <p class="text-sm text-secondary-text">
                        Already have an account?
                        <Link :href="route('login')" class="text-secondary-brand hover:text-secondary-accent font-medium transition-colors">
                            Sign in here
                        </Link>
                    </p>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
