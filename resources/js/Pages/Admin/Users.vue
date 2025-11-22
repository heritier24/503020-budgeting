<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

// Declare global route function
declare const route: any

// Props
const props = defineProps<{
  users: {
    data: Array<{
      id: number
      name: string
      email: string
      is_admin: boolean
      created_at: string
      updated_at: string
      transactions_count: number
      goals_count: number
      loans_count: number
    }>
    links: any[]
    meta: any
  }
  filters: {
    search?: string
    admin_only?: boolean
  }
}>()

const search = ref(props.filters.search || '')
const adminOnly = ref(props.filters.admin_only || false)

const submitSearch = () => {
  const params = new URLSearchParams()
  if (search.value) params.set('search', search.value)
  if (adminOnly.value) params.set('admin_only', '1')
  
  window.location.href = `/admin/users?${params.toString()}`
}
</script>

<template>
  <Head title="Admin - Users" />

  <AdminLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          User Management
        </h2>
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-600">
            Total: {{ users.meta.total }} users
          </span>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <form @submit.prevent="submitSearch" class="flex flex-col sm:flex-row gap-4">
              <div class="flex-1">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search users by name or email..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
              <div class="flex items-center">
                <label class="flex items-center">
                  <input
                    v-model="adminOnly"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  />
                  <span class="ml-2 text-sm text-gray-600">Admin only</span>
                </label>
              </div>
              <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
              >
                Search
              </button>
            </form>
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    User
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Role
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Activity
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Joined
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Last Active
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                          <span class="text-sm font-medium text-gray-700">
                            {{ user.name.charAt(0).toUpperCase() }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="user.is_admin" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                      Admin
                    </span>
                    <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                      User
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="space-y-1">
                      <div>{{ user.transactions_count }} transactions</div>
                      <div>{{ user.goals_count }} goals</div>
                      <div>{{ user.loans_count }} loans</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ new Date(user.created_at).toLocaleDateString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ new Date(user.updated_at).toLocaleDateString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <Link :href="route('admin.user.details', user.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                      View Details
                    </Link>
                    <button class="text-yellow-600 hover:text-yellow-900 mr-3">
                      Edit
                    </button>
                    <button class="text-red-600 hover:text-red-900">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="users.links.length > 3" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link
                v-if="users.links[0].url"
                :href="users.links[0].url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Previous
              </Link>
              <Link
                v-if="users.links[users.links.length - 1].url"
                :href="users.links[users.links.length - 1].url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ users.meta.from }}</span>
                  to
                  <span class="font-medium">{{ users.meta.to }}</span>
                  of
                  <span class="font-medium">{{ users.meta.total }}</span>
                  results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <Link
                    v-for="(link, index) in users.links"
                    :key="index"
                    :href="link.url"
                    v-html="link.label"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                      link.active
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      index === 0 ? 'rounded-l-md' : '',
                      index === users.links.length - 1 ? 'rounded-r-md' : ''
                    ]"
                  />
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>