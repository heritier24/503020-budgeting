<template>
  <Head>
    <!-- Primary Meta Tags -->
    <title>{{ seoTitle }}</title>
    <meta name="title" :content="seoTitle" />
    <meta name="description" :content="description" />
    <meta name="keywords" :content="keywords" />
    <meta name="author" content="Tamba Group" />
    <meta name="robots" :content="robots" />
    <link rel="canonical" :href="canonicalUrl" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" :content="canonicalUrl" />
    <meta property="og:title" :content="seoTitle" />
    <meta property="og:description" :content="description" />
    <meta property="og:image" :content="ogImage" />
    <meta property="og:site_name" content="Tamba Group Budget Tracker" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" :content="canonicalUrl" />
    <meta name="twitter:title" :content="seoTitle" />
    <meta name="twitter:description" :content="description" />
    <meta name="twitter:image" :content="ogImage" />

    <!-- Additional Meta Tags -->
    <meta name="theme-color" content="#1f2937" />
    <meta name="application-name" content="Tamba Group Budget Tracker" />
  </Head>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

interface Props {
  title?: string
  description?: string
  keywords?: string
  image?: string
  url?: string
  robots?: string
  noindex?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Tamba Group - Budget Tracker',
  description: 'Master the 50/30/20 budgeting rule with our intuitive platform. Track expenses, manage income, and achieve your financial goals with ease.',
  keywords: 'budget tracker, 50 30 20 rule, personal finance, expense tracking, financial planning, budgeting app, money management, savings goals, income tracking, financial analytics, Tamba Group',
  image: '/tambagroup.png',
  robots: 'index, follow',
  noindex: false
})

const baseUrl = typeof window !== 'undefined' ? window.location.origin : 'https://tambagroup.com'

const seoTitle = computed(() => {
  return props.title.includes('Tamba Group') || props.title.includes('Budget Tracker')
    ? props.title
    : `${props.title} - Tamba Group Budget Tracker`
})

const canonicalUrl = computed(() => {
  if (props.url) {
    return props.url.startsWith('http') ? props.url : `${baseUrl}${props.url}`
  }
  return typeof window !== 'undefined' ? window.location.href : baseUrl
})

const ogImage = computed(() => {
  if (props.image) {
    return props.image.startsWith('http') ? props.image : `${baseUrl}${props.image}`
  }
  return `${baseUrl}/tambagroup.png`
})

const robots = computed(() => {
  if (props.noindex) {
    return 'noindex, nofollow'
  }
  return props.robots
})
</script>

