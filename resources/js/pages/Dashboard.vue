<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { UsersIcon, TrophyIcon, GraduationCapIcon, CreditCardIcon } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface Props {
    stats: {
        athletes: {
            total: number;
            junior: number;
            senior: number;
        };
        trainings: {
            total: number;
            upcoming: number;
        };
        competitions: {
            total: number;
            upcoming: number;
        };
        payments: {
            total: number;
            pending: number;
            overdue: number;
        };
    };
    upcomingTrainings: any[];
    upcomingCompetitions: any[];
}

defineProps<Props>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Quick Navigation -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Link :href="route('clubs.index')">
                    <Card class="hover:bg-gray-50 transition-colors cursor-pointer">
                        <CardHeader class="flex flex-row items-center justify-between pb-2">
                            <CardTitle class="text-sm font-medium">Kluby</CardTitle>
                            <UsersIcon class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">Zarządzaj</div>
                            <p class="text-xs text-muted-foreground">Twoje kluby i członkowie</p>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="route('athletes.index')">
                    <Card class="hover:bg-gray-50 transition-colors cursor-pointer">
                        <CardHeader class="flex flex-row items-center justify-between pb-2">
                            <CardTitle class="text-sm font-medium">Zawodnicy</CardTitle>
                            <GraduationCapIcon class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">Zarządzaj</div>
                            <p class="text-xs text-muted-foreground">Lista wszystkich zawodników</p>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="route('trainings.index')">
                    <Card class="hover:bg-gray-50 transition-colors cursor-pointer">
                        <CardHeader class="flex flex-row items-center justify-between pb-2">
                            <CardTitle class="text-sm font-medium">Treningi</CardTitle>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">Zarządzaj</div>
                            <p class="text-xs text-muted-foreground">Harmonogram i historia</p>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="route('competitions.index')">
                    <Card class="hover:bg-gray-50 transition-colors cursor-pointer">
                        <CardHeader class="flex flex-row items-center justify-between pb-2">
                            <CardTitle class="text-sm font-medium">Zawody</CardTitle>
                            <TrophyIcon class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">Zarządzaj</div>
                            <p class="text-xs text-muted-foreground">Wydarzenia i wyniki</p>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <!-- Statystyki -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <!-- Zawodnicy -->
                <Card class="overflow-hidden">
                    <CardHeader>
                        <CardTitle>Zawodnicy</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Ogółem:</span>
                                <span class="font-medium">{{ stats.athletes.total }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Junior:</span>
                                <span class="font-medium">{{ stats.athletes.junior }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Senior:</span>
                                <span class="font-medium">{{ stats.athletes.senior }}</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Link :href="route('athletes.index')" class="text-sm text-blue-600 hover:underline">
                            Zobacz wszystkich
                        </Link>
                    </CardFooter>
                </Card>

                <!-- Treningi -->
                <Card class="overflow-hidden">
                    <CardHeader>
                        <CardTitle>Treningi</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Ogółem:</span>
                                <span class="font-medium">{{ stats.trainings.total }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Nadchodzące:</span>
                                <span class="font-medium">{{ stats.trainings.upcoming }}</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Link :href="route('trainings.index')" class="text-sm text-blue-600 hover:underline">
                            Zobacz wszystkie
                        </Link>
                    </CardFooter>
                </Card>

                <!-- Zawody -->
                <Card class="overflow-hidden">
                    <CardHeader>
                        <CardTitle>Zawody</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Ogółem:</span>
                                <span class="font-medium">{{ stats.competitions.total }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Nadchodzące:</span>
                                <span class="font-medium">{{ stats.competitions.upcoming }}</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Link :href="route('competitions.index')" class="text-sm text-blue-600 hover:underline">
                            Zobacz wszystkie
                        </Link>
                    </CardFooter>
                </Card>

                <!-- Finanse -->
                <Card class="overflow-hidden">
                    <CardHeader>
                        <CardTitle>Finanse</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Wpłacone:</span>
                                <span class="font-medium">{{ stats.payments.total }} PLN</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Oczekujące:</span>
                                <span class="font-medium">{{ stats.payments.pending }} PLN</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Zaległe:</span>
                                <span class="font-medium text-red-500">{{ stats.payments.overdue }} PLN</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Link :href="route('payments.index')" class="text-sm text-blue-600 hover:underline">
                            Zobacz wszystkie
                        </Link>
                    </CardFooter>
                </Card>
            </div>

            <!-- Nadchodzące wydarzenia -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Nadchodzące treningi -->
                <Card class="overflow-hidden">
                    <CardHeader>
                        <CardTitle>Nadchodzące treningi</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="upcomingTrainings.length === 0" class="text-center py-4 text-gray-500">
                            Brak nadchodzących treningów
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="training in upcomingTrainings" :key="training.id" class="border-b pb-3 last:border-0">
                                <div class="font-medium">{{ new Date(training.date).toLocaleDateString() }} - {{ training.location }}</div>
                                <div class="text-sm text-gray-600">
                                    <div>Klub: {{ training.club.name }}</div>
                                    <div>Trener: {{ training.trainer.first_name }} {{ training.trainer.last_name }}</div>
                                    <div>Typ: {{ training.type }}</div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Nadchodzące zawody -->
                <Card class="overflow-hidden">
                    <CardHeader>
                        <CardTitle>Nadchodzące zawody</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="upcomingCompetitions.length === 0" class="text-center py-4 text-gray-500">
                            Brak nadchodzących zawodów
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="competition in upcomingCompetitions" :key="competition.id" class="border-b pb-3 last:border-0">
                                <div class="font-medium">{{ competition.name }}</div>
                                <div class="text-sm text-gray-600">
                                    <div>Data: {{ new Date(competition.date).toLocaleDateString() }}</div>
                                    <div>Miejsce: {{ competition.location }}</div>
                                    <div>Klub: {{ competition.club.name }}</div>
                                    <div>Typ: {{ competition.type }} | Poziom: {{ competition.level }}</div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
