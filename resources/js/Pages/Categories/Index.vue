<template>
  <AuthenticatedLayout :user="data.user">
    <Head title="Categories" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="mb-6">
              <div class="flex justify-between items-center">
                <div>
                  <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
                  <p class="text-gray-600 mt-2">Manage your transaction categories for better budgeting</p>
                </div>
                <Link 
                  href="/categories/create"
                  class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
                >
                  Add Category
                </Link>
              </div>
            </div>

            <!-- Category Groups -->
            <div class="space-y-8">
              <!-- Income Categories -->
              <div v-if="data.groupedCategories && data.groupedCategories.income && data.groupedCategories.income.length > 0">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                  <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                  Income Categories
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="category in data.groupedCategories.income" 
                    :key="category.id"
                    class="bg-green-50 border border-green-200 rounded-lg p-4"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                        <div 
                          class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium"
                          :style="`background-color: ${category.color}`"
                        >
                          {{ category.icon }}
                        </div>
                        <div>
                          <h3 class="font-medium text-gray-900">{{ category.name }}</h3>
                          <p class="text-sm text-gray-600">{{ category.type }}</p>
                        </div>
                      </div>
                      <div v-if="!category.is_default" class="flex space-x-2">
                        <Link 
                          :href="`/categories/${category.id}/edit`"
                          class="text-blue-600 hover:text-blue-800 text-sm"
                        >
                          Edit
                        </Link>
                        <button 
                          @click="deleteCategory(category)"
                          class="text-red-600 hover:text-red-800 text-sm"
                        >
                          Delete
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Needs Categories (50%) -->
              <div v-if="data.groupedCategories && data.groupedCategories.needs && data.groupedCategories.needs.length > 0">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                  <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                  Needs Categories (50% of budget)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="category in data.groupedCategories.needs" 
                    :key="category.id"
                    class="bg-red-50 border border-red-200 rounded-lg p-4"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                        <div 
                          class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium"
                          :style="`background-color: ${category.color}`"
                        >
                          {{ category.icon }}
                        </div>
                        <div>
                          <h3 class="font-medium text-gray-900">{{ category.name }}</h3>
                          <p class="text-sm text-gray-600">{{ category.type }}</p>
                        </div>
                      </div>
                      <div v-if="!category.is_default" class="flex space-x-2">
                        <Link 
                          :href="`/categories/${category.id}/edit`"
                          class="text-blue-600 hover:text-blue-800 text-sm"
                        >
                          Edit
                        </Link>
                        <button 
                          @click="deleteCategory(category)"
                          class="text-red-600 hover:text-red-800 text-sm"
                        >
                          Delete
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Wants Categories (30%) -->
              <div v-if="data.groupedCategories && data.groupedCategories.wants && data.groupedCategories.wants.length > 0">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                  <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                  Wants Categories (30% of budget)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="category in data.groupedCategories.wants" 
                    :key="category.id"
                    class="bg-yellow-50 border border-yellow-200 rounded-lg p-4"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                        <div 
                          class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium"
                          :style="`background-color: ${category.color}`"
                        >
                          {{ category.icon }}
                        </div>
                        <div>
                          <h3 class="font-medium text-gray-900">{{ category.name }}</h3>
                          <p class="text-sm text-gray-600">{{ category.type }}</p>
                        </div>
                      </div>
                      <div v-if="!category.is_default" class="flex space-x-2">
                        <Link 
                          :href="`/categories/${category.id}/edit`"
                          class="text-blue-600 hover:text-blue-800 text-sm"
                        >
                          Edit
                        </Link>
                        <button 
                          @click="deleteCategory(category)"
                          class="text-red-600 hover:text-red-800 text-sm"
                        >
                          Delete
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Savings Categories (20%) -->
              <div v-if="data.groupedCategories && data.groupedCategories.savings && data.groupedCategories.savings.length > 0">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                  <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                  Savings Categories (20% of budget)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div 
                    v-for="category in data.groupedCategories.savings" 
                    :key="category.id"
                    class="bg-green-50 border border-green-200 rounded-lg p-4"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                        <div 
                          class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium"
                          :style="`background-color: ${category.color}`"
                        >
                          {{ category.icon }}
                        </div>
                        <div>
                          <h3 class="font-medium text-gray-900">{{ category.name }}</h3>
                          <p class="text-sm text-gray-600">{{ category.type }}</p>
                        </div>
                      </div>
                      <div v-if="!category.is_default" class="flex space-x-2">
                        <Link 
                          :href="`/categories/${category.id}/edit`"
                          class="text-blue-600 hover:text-blue-800 text-sm"
                        >
                          Edit
                        </Link>
                        <button 
                          @click="deleteCategory(category)"
                          class="text-red-600 hover:text-red-800 text-sm"
                        >
                          Delete
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
  data: {
    user: Object,
    categories: Array,
    groupedCategories: Object
  }
})

// Methods
const deleteCategory = (category) => {
  if (confirm(`Are you sure you want to delete "${category.name}"?`)) {
    router.delete(`/categories/${category.id}`, {
      onSuccess: () => {
        // Category deleted successfully
      },
      onError: (errors) => {
        alert(errors.message || 'Error deleting category')
      }
    })
  }
}
</script>