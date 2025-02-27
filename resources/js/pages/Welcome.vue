<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dive Manager - System zarządzania klubem sportowym">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <header class="py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="h-10 w-10 rounded-lg bg-blue-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22c-4.97 0-9-2.582-9-7v-.088C3 10.903 6.167 9 12 9s9 1.903 9 4.912V15c0 4.418-4.03 7-9 7z"></path>
                                <path d="M12 2a3 3 0 0 0-3 3v4c0 1.657 1.343 3 3 3s3-1.343 3-3V5a3 3 0 0 0-3-3z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Dive Manager</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div v-if="$page.props.auth.user" class="flex items-center space-x-2">
                            <Link :href="route('dashboard')" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Dashboard
                            </Link>
                        </div>
                        <div v-else class="flex items-center space-x-2">
                            <Link :href="route('login')" class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                Logowanie
                            </Link>
                            <Link :href="route('register')" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Rejestracja
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Hero Section -->
            <div class="py-12 md:py-20">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 md:gap-12">
                    <div class="flex flex-col justify-center">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl dark:text-white">
                            <span class="block">Zarządzaj klubem</span>
                            <span class="block text-blue-600 dark:text-blue-400">sportowym prościej</span>
                        </h1>
                        <p class="mt-6 max-w-xl text-lg text-gray-600 dark:text-gray-300">
                            Kompletny system do zarządzania zawodnikami, treningami, zawodami, wynikami i płatnościami dla klubów skoków do wody.
                        </p>
                        <div class="mt-8 flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                            <Link :href="$page.props.auth.user ? route('dashboard') : route('login')" class="rounded-md bg-blue-600 px-8 py-3 text-center text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                {{ $page.props.auth.user ? 'Przejdź do panelu' : 'Zaloguj się' }}
                            </Link>
                            <a href="#features" class="rounded-md bg-white px-8 py-3 text-center text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                Dowiedz się więcej
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center justify-center">
                        <div class="relative h-64 w-full max-w-lg sm:h-80 lg:h-96">
                            <div class="absolute h-full w-full rounded-2xl bg-gradient-to-r from-blue-400 to-blue-600 opacity-50 blur-2xl dark:from-blue-700 dark:to-purple-800"></div>
                            <div class="relative h-full w-full rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
                                <div class="h-full w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                                    <div class="h-8 w-full bg-gray-100 px-4 flex items-center dark:bg-gray-900">
                                        <div class="flex space-x-2">
                                            <div class="h-3 w-3 rounded-full bg-red-500"></div>
                                            <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                                            <div class="h-3 w-3 rounded-full bg-green-500"></div>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center space-x-4 mb-4">
                                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                </svg>
                                            </div>
                                            <div class="h-4 w-32 rounded bg-gray-300 dark:bg-gray-600"></div>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="h-4 w-full rounded bg-gray-300 dark:bg-gray-600"></div>
                                            <div class="h-4 w-5/6 rounded bg-gray-300 dark:bg-gray-600"></div>
                                            <div class="h-4 w-4/6 rounded bg-gray-300 dark:bg-gray-600"></div>
                                        </div>
                                        <div class="mt-6 grid grid-cols-3 gap-4">
                                            <div class="h-20 rounded bg-blue-100 dark:bg-blue-900"></div>
                                            <div class="h-20 rounded bg-purple-100 dark:bg-purple-900"></div>
                                            <div class="h-20 rounded bg-green-100 dark:bg-green-900"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div id="features" class="py-12 md:py-20">
                <div class="text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                        Kompletny system zarządzania
                    </h2>
                    <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                        Wszystko, czego potrzebujesz do efektywnego zarządzania Twoim klubem sportowym.
                    </p>
                </div>
                <div class="mt-16">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Feature 1 -->
                        <div class="rounded-xl bg-white p-6 shadow-sm transition-all hover:shadow-md dark:bg-gray-800">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Zarządzanie zawodnikami</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Kompletne profile zawodników z historią treningów, wynikami zawodów i płatnościami.
                            </p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="rounded-xl bg-white p-6 shadow-sm transition-all hover:shadow-md dark:bg-gray-800">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Harmonogram treningów</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Planowanie treningów, zarządzanie obecnościami i monitorowanie postępów zawodników.
                            </p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="rounded-xl bg-white p-6 shadow-sm transition-all hover:shadow-md dark:bg-gray-800">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 2l3 3-3 3-3-3 3-3z"></path>
                                    <path d="M6 15l3 3-3 3-3-3 3-3z"></path>
                                    <path d="M17 8l-9 9"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Wyniki zawodów</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Śledzenie wyników zawodów, rankingów i postępów zawodników w czasie.
                            </p>
                        </div>

                        <!-- Feature 4 -->
                        <div class="rounded-xl bg-white p-6 shadow-sm transition-all hover:shadow-md dark:bg-gray-800">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                    <line x1="1" y1="10" x2="23" y2="10"></line>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Zarządzanie płatnościami</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Śledzenie płatności za treningi, zawody i składki członkowskie.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="relative my-12 overflow-hidden rounded-2xl bg-blue-600 dark:bg-blue-700">
                <div class="absolute inset-0 opacity-20">
                    <svg width="100%" height="100%" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_4_37)">
                            <path d="M400 0H0V400H400V0Z" fill="black" fill-opacity="0" />
                            <path d="M-2.5 226.004L43.7466 226.004L-26.4003 147.851L101.345 75.1932L15.5431 75.19L-42.525 175.767L43.2765 175.768L-2.5 226.004Z" stroke="white" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M277.836 227.536C264.033 238.82 253.708 247.904 246.862 254.789L188.794 355.366L219.768 328.113L277.836 227.536Z" stroke="white" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M246.866 254.784L246.534 254.784L188.466 355.361L188.798 355.361L246.866 254.784Z" stroke="white" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M246.539 254.781L275.362 304.701L217.294 405.277L188.471 355.358L246.539 254.781Z" stroke="white" stroke-width="1" stroke-linejoin="bevel" />
                        </g>
                        <defs>
                            <clipPath id="clip0_4_37">
                                <rect width="400" height="400" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
                <div class="relative px-8 py-16 sm:px-16 sm:py-20 md:px-20">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Zacznij zarządzać Twoim klubem już dziś!
                    </h2>
                    <p class="mt-4 max-w-xl text-lg text-blue-100">
                        Dołącz do setek klubów sportowych, które usprawniły swoje zarządzanie dzięki naszemu systemowi.
                    </p>
                    <div class="mt-8">
                        <Link :href="$page.props.auth.user ? route('dashboard') : route('register')" class="rounded-md bg-white px-8 py-3 text-base font-medium text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white">
                            {{ $page.props.auth.user ? 'Przejdź do panelu' : 'Zarejestruj się za darmo' }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="py-12">
                <div class="flex flex-col items-center justify-between space-y-6 md:flex-row md:space-y-0">
                    <div class="flex items-center space-x-2">
                        <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22c-4.97 0-9-2.582-9-7v-.088C3 10.903 6.167 9 12 9s9 1.903 9 4.912V15c0 4.418-4.03 7-9 7z"></path>
                                <path d="M12 2a3 3 0 0 0-3 3v4c0 1.657 1.343 3 3 3s3-1.343 3-3V5a3 3 0 0 0-3-3z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">© 2025 Dive Manager. Wszelkie prawa zastrzeżone.</span>
                    </div>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            <span class="sr-only">GitHub</span>
                            <svg class="h-6 w-6" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>
