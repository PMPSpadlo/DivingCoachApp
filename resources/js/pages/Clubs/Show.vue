<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useDateFormat } from '@/composables/useDateFormat';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { PenIcon, TrashIcon, UserPlusIcon, CalendarIcon, TrophyIcon } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kluby',
        href: route('clubs.index'),
    },
    {
        title: 'Szczegóły klubu',
        href: '#',
    },
];

interface Club {
    id: number;
    name: string;
    city: string;
    country: string;
    address: string | null;
    phone: string | null;
    email: string | null;
    description: string | null;
    website: string | null;
    active: boolean;
    owner_id: number;
    owner: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
    };
    athletes: Array<{
        id: number;
        first_name: string;
        last_name: string;
        birth_date: string;
        gender: string;
        age_category: string;
    }>;
    trainings: Array<{
        id: number;
        date: string;
        location: string;
        trainer_id: number;
        type: string;
    }>;
    competitions: Array<{
        id: number;
        name: string;
        date: string;
        location: string;
        type: string;
        level: string;
    }>;
}

defineProps<{
    club: Club;
}>();

const { formatDate } = useDateFormat();
</script>

<template>
    <Head :title="club.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Nagłówek z akcjami -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ club.name }}</h1>
                    <p class="text-muted-foreground">{{ club.city }}, {{ club.country }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('clubs.edit', club.id)">
                        <Button>
                            <PenIcon class="h-4 w-4 mr-2" />
                            Edytuj
                        </Button>
                    </Link>
                    <Link :href="route('athletes.create')" :data="{ club_id: club.id }">
                        <Button variant="outline">
                            <UserPlusIcon class="h-4 w-4 mr-2" />
                            Dodaj zawodnika
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Informacje o klubie -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informacje</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-if="club.description" class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Opis</div>
                                <div>{{ club.description }}</div>
                            </div>
                            <div v-if="club.address" class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Adres</div>
                                <div>{{ club.address }}</div>
                            </div>
                            <div v-if="club.phone" class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Telefon</div>
                                <div>{{ club.phone }}</div>
                            </div>
                            <div v-if="club.email" class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Email</div>
                                <div>{{ club.email }}</div>
                            </div>
                            <div v-if="club.website" class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Strona WWW</div>
                                <div>
                                    <a :href="club.website" target="_blank" class="text-blue-600 hover:underline">
                                        {{ club.website }}
                                    </a>
                                </div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Właściciel</div>
                                <div>{{ club.owner.first_name }} {{ club.owner.last_name }}</div>
                            </div>
                            <div class="pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Status</div>
                                <div>
                                    <span
                                        class="px-2 py-1 text-xs rounded"
                                        :class="club.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    >
                                        {{ club.active ? 'Aktywny' : 'Nieaktywny' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Statystyki -->
                <Card>
                    <CardHeader>
                        <CardTitle>Statystyki</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2 rounded-lg border p-4">
                                <div class="flex items-center justify-center text-3xl font-bold">
                                    {{ club.athletes.length }}
                                </div>
                                <div class="text-center text-sm font-medium text-muted-foreground">
                                    Zawodników
                                </div>
                            </div>
                            <div class="space-y-2 rounded-lg border p-4">
                                <div class="flex items-center justify-center text-3xl font-bold">
                                    {{ club.trainings.length }}
                                </div>
                                <div class="text-center text-sm font-medium text-muted-foreground">
                                    Treningów
                                </div>
                            </div>
                            <div class="space-y-2 rounded-lg border p-4">
                                <div class="flex items-center justify-center text-3xl font-bold">
                                    {{ club.competitions.length }}
                                </div>
                                <div class="text-center text-sm font-medium text-muted-foreground">
                                    Zawodów
                                </div>
                            </div>
                            <div class="space-y-2 rounded-lg border p-4">
                                <div class="flex items-center justify-center text-3xl font-bold">
                                    {{ club.athletes.filter(a => a.age_category === 'junior').length }}
                                </div>
                                <div class="text-center text-sm font-medium text-muted-foreground">
                                    Juniorów
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Zawodnicy -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Zawodnicy</CardTitle>
                        <CardDescription>Lista zawodników w klubie</CardDescription>
                    </div>
                    <Link :href="route('athletes.index')" :data="{ club_id: club.id }">
                        <Button variant="outline" size="sm">
                            Zobacz wszystkich
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div v-if="club.athletes.length === 0" class="text-center py-4 text-muted-foreground">
                        Brak zawodników w klubie
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nazwisko i imię</TableHead>
                                <TableHead>Data urodzenia</TableHead>
                                <TableHead>Kategoria</TableHead>
                                <TableHead>Płeć</TableHead>
                                <TableHead class="text-right">Akcje</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="athlete in club.athletes" :key="athlete.id">
                                <TableCell class="font-medium">
                                    {{ athlete.last_name }} {{ athlete.first_name }}
                                </TableCell>
                                <TableCell>{{ formatDate(athlete.birth_date) }}</TableCell>
                                <TableCell>
                                    <span
                                        class="px-2 py-1 text-xs rounded"
                                        :class="athlete.age_category === 'junior' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'"
                                    >
                                        {{ athlete.age_category === 'junior' ? 'Junior' : 'Senior' }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    {{ athlete.gender === 'male' ? 'M' : (athlete.gender === 'female' ? 'K' : '-') }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('athletes.show', athlete.id)">
                                            <Button variant="ghost" size="icon">
                                                <span class="sr-only">Szczegóły</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </Button>
                                        </Link>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Ostatnie treningi -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Ostatnie treningi</CardTitle>
                        <CardDescription>Historia treningów klubu</CardDescription>
                    </div>
                    <Link :href="route('trainings.index')" :data="{ club_id: club.id }">
                        <Button variant="outline" size="sm">
                            Zobacz wszystkie
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div v-if="club.trainings.length === 0" class="text-center py-4 text-muted-foreground">
                        Brak zarejestrowanych treningów
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Data</TableHead>
                                <TableHead>Lokalizacja</TableHead>
                                <TableHead>Typ</TableHead>
                                <TableHead class="text-right">Akcje</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="training in club.trainings" :key="training.id">
                                <TableCell>{{ formatDate(training.date) }}</TableCell>
                                <TableCell>{{ training.location }}</TableCell>
                                <TableCell>{{ training.type }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('trainings.show', training.id)">
                                            <Button variant="ghost" size="icon">
                                                <span class="sr-only">Szczegóły</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </Button>
                                        </Link>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
