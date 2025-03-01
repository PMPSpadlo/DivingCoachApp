<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <Head title="Dive Manager - System zarządzania klubem sportowym">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        </Head>

        <div class="mb-6 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <Link href="/" class="flex items-center space-x-2">
                    <div class="h-10 w-10 rounded-lg bg-blue-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22c-4.97 0-9-2.582-9-7v-.088C3 10.903 6.167 9 12 9s9 1.903 9 4.912V15c0 4.418-4.03 7-9 7z"></path>
                            <path d="M12 2a3 3 0 0 0-3 3v4c0 1.657 1.343 3 3 3s3-1.343 3-3V5a3 3 0 0 0-3-3z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Dive Manager</span>
                </Link>
            </div>
        </div>

        <div class="mx-auto w-full max-w-md">
            <div class="relative">
                <div class="absolute h-full w-full rounded-2xl bg-gradient-to-r from-blue-400 to-blue-600 opacity-50 blur-xl dark:from-blue-700 dark:to-purple-800"></div>
                <div class="relative rounded-2xl bg-white dark:bg-gray-800 shadow-xl overflow-hidden">
                    <div class="px-6 py-8 sm:px-10">
                        <div class="mb-6 text-center">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ description }}</p>
                        </div>

                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
