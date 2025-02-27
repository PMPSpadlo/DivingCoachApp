<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useDateFormat } from '@/composables/useDateFormat';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { PlusIcon } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zawody',
        href: route('competitions.index'),
    },
    {
        title: 'Wyniki zawodów',
        href: '#',
    },
];

interface Competition {
    id: number;
    name: string;
    location: string;
    date: string;
    type: string;
    level: string;
}

interface Athlete {
    id: number;
    first_name: string;
    last_name: string;
}

interface Result {
    id: number;
    athlete: Athlete;
    score: number;
    dive_type: string | null;
    difficulty_level: string | null;
    rank: number | null;
}

interface Props {
    competition: Competition;
    results: {
        data: Result[];
        links: any;
    };
}

defineProps<Props>();

const { formatDate } = useDateFormat();
</script>

<template>
    <Head :title="`Wyniki - ${competition.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ competition.name }} - Wyniki</h1>
                    <p class="text-muted-foreground">
                        {{ competition.location }} | {{ formatDate(competition.date) }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('competition-results.create', { competition_id: competition.id })">
                        <Button>
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Dodaj wynik
                        </Button>
                    </Link>
                    <Link :href="route('competitions.show', competition.id)">
                        <Button variant="outline">
                            Powrót do szczegółów zawodów
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Lista wyników -->
            <Card>
                <CardHeader>
                    <CardTitle>Wyniki zawodów</CardTitle>
                    <CardDescription>Pełna lista wyników zawodników</CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="results.data.length === 0" class="text-center py-4 text-muted-foreground">
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
                            <TableRow v-for="result in results.data" :key="result.id">
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
            </Card>
        </div>
    </AppLayout>
</template>
