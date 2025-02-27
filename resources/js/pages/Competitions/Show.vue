<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useDateFormat } from '@/composables/useDateFormat';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import {
    PenIcon,
    TrashIcon,
    PlusIcon
} from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zawody',
        href: route('competitions.index'),
    },
    {
        title: 'Szczegóły zawodów',
        href: '#',
    },
];

interface Club {
    id: number;
    name: string;
}

interface Athlete {
    id: number;
    first_name: string;
    last_name: string;
    birth_date: string;
    gender: string;
    age_category: string;
}

interface CompetitionResult {
    id: number;
    athlete: Athlete;
    score: number;
    dive_type: string | null;
    difficulty_level: string | null;
    rank: number | null;
    notes: string | null;
}

interface Competition {
    id: number;
    name: string;
    location: string;
    date: string;
    judge_panel: string | null;
    type: string;
    level: string;
    club: Club;
    results: CompetitionResult[];
}

interface Props {
    competition: Competition;
    clubAthletes: Athlete[];
}

defineProps<Props>();

const { formatDate } = useDateFormat();

// Formatowanie typu zawodów
const formatType = (type: string): string => {
    switch (type) {
        case 'platform': return 'Wieża';
        case 'springboard': return 'Trampolina';
        case 'synchro': return 'Skoki synchroniczne';
        default: return type;
    }
};

// Formatowanie poziomu zawodów
const formatLevel = (level: string): string => {
    switch (level) {
        case 'regional': return 'Regionalny';
        case 'national': return 'Krajowy';
        case 'international': return 'Międzynarodowy';
        default: return level;
    }
};

// Usuwanie zawodów
const deleteCompetition = () => {
    if (confirm('Czy na pewno chcesz usunąć te zawody?')) {
        router.delete(route('competitions.destroy', competition.id));
    }
};
</script>

<template>
    <Head :title="competition.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Nagłówek z akcjami -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ competition.name }}</h1>
                    <p class="text-muted-foreground">{{ competition.location }} | {{ formatDate(competition.date) }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('competitions.edit', competition.id)">
                        <Button>
                            <PenIcon class="h-4 w-4 mr-2" />
                            Edytuj
                        </Button>
                    </Link>
                    <Button variant="destructive" @click="deleteCompetition">
                        <TrashIcon class="h-4 w-4 mr-2" />
                        Usuń
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Informacje o zawodach -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informacje</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Data</div>
                                <div>{{ formatDate(competition.date) }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Lokalizacja</div>
                                <div>{{ competition.location }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Typ zawodów</div>
                                <div>{{ formatType(competition.type) }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Poziom</div>
                                <div>{{ formatLevel(competition.level) }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Klub organizujący</div>
                                <div>{{ competition.club.name }}</div>
                            </div>
                            <div v-if="competition.judge_panel" class="pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Panel sędziowski</div>
                                <div>{{ competition.judge_panel }}</div>
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
                                    {{ competition.results.length }}
                                </div>
                                <div class="text-center text-sm font-medium text-muted-foreground">
                                    Zawodników
                                </div>
                            </div>
                            <!-- Dodatkowe statystyki można dodać później -->
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Wyniki zawodów -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Wyniki zawodów</CardTitle>
                        <CardDescription>Lista wyników zawodników</CardDescription>
                    </div>
                    <Link :href="route('competition-results.create', { competition_id: competition.id })">
                        <Button>
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Dodaj wynik
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div v-if="competition.results.length === 0" class="text-center py-4 text-muted-foreground">
                        Brak wyników dla tych zawodów
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Pozycja</TableHead>
                                <TableHead>Zawodnik</TableHead>
                                <TableHead>Wynik</TableHead>
                                <TableHead>Rodzaj skoku</TableHead>
                                <TableHead>Trudność</TableHead>
                                <TableHead class="text-right">Akcje</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="result in competition.results" :key="result.id">
                                <TableCell>
                                    <span v-if="result.rank" class="font-semibold">{{ result.rank }}</span>
                                    <span v-else>-</span>
                                </TableCell>
                                <TableCell class="font-medium">
                                    {{ result.athlete.last_name }} {{ result.athlete.first_name }}
                                </TableCell>
                                <TableCell>{{ result.score }}</TableCell>
                                <TableCell>{{ result.dive_type || '-' }}</TableCell>
                                <TableCell>{{ result.difficulty_level || '-' }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('competition-results.show', result.id)">
                                            <Button variant="ghost" size="icon">
                                                <span class="sr-only">Szczegóły</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </Button>
                                        </Link>
                                        <Link :href="route('competition-results.edit', result.id)">
                                            <Button variant="ghost" size="icon">
                                                <span class="sr-only">Edytuj</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            </Button>
                                        </Link>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
                <CardFooter>
                    <Link :href="route('competitions.results', competition.id)" class="ml-auto">
                        <Button variant="outline">
                            Zobacz wszystkie wyniki
                        </Button>
                    </Link>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template>
