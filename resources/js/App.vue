<template>
  <div id="app">
    <DashboardOverview :data="data" />
  </div>
</template>

<script setup lang="ts">
import DashboardOverview from './Components/Dashboard/DashboardOverview.vue'

// Props for receiving data from Inertia
defineProps<{
  data: any
}>()
</script>

<style scoped>
/* Additional component-specific styles can go here */
</style>