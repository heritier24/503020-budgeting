<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">
        Budgeting App
      </h1>
      
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Welcome to your Budgeting Application
        </h2>
        <p class="text-gray-600">
          This is a Laravel + Vue.js 3 + TypeScript + Tailwind CSS application.
        </p>
        
        <div class="mt-6">
          <button 
            @click="incrementCounter"
            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200"
          >
            Click me! ({{ counter }})
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const counter = ref(0)

const incrementCounter = () => {
  counter.value++
}
</script>

<style scoped>
/* Additional component-specific styles can go here */
</style>