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
    TrashIcon
} from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Treningi',
        href: route('trainings.index'),
    },
    {
        title: 'Szczegóły treningu',
        href: '#',
    },
];

interface Club {
    id: number;
    name: string;
}

interface Trainer {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
}

interface Athlete {
    id: number;
    first_name: string;
    last_name: string;
    birth_date: string;
    gender: string;
    age_category: string;
}

interface Training {
    id: number;
    date: string;
    location: string;
    type: string;
    notes: string | null;
    club: Club;
    trainer: Trainer;
    athletes: Athlete[];
}

defineProps<{
    training: Training;
}>();

const { formatDate, formatDateTime } = useDateFormat();

// Formatowanie typu treningu
const formatType = (type: string): string => {
    switch (type) {
        case 'general': return 'Ogólny';
        case 'technical': return 'Techniczny';
        case 'strength': return 'Siłowy';
        case 'conditioning': return 'Kondycyjny';
        default: return type;
    }
};

// Usuwanie treningu
const deleteTraining = () => {
    if (confirm('Czy na pewno chcesz usunąć ten trening?')) {
        router.delete(route('trainings.destroy', training.id));
    }
};
</script>

<template>
    <Head title="Szczegóły treningu" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Nagłówek z akcjami -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Trening - {{ formatDateTime(training.date) }}</h1>
                    <p class="text-muted-foreground">{{ training.club.name }} | {{ training.location }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('trainings.edit', training.id)">
                        <Button>
                            <PenIcon class="h-4 w-4 mr-2" />
                            Edytuj
                        </Button>
                    </Link>
                    <Button variant="destructive" @click="deleteTraining">
                        <TrashIcon class="h-4 w-4 mr-2" />
                        Usuń
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Informacje o treningu -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informacje</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Data i godzina</div>
                                <div>{{ formatDateTime(training.date) }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Lokalizacja</div>
                                <div>{{ training.location }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Typ treningu</div>
                                <div>{{ formatType(training.type) }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Klub</div>
                                <div>{{ training.club.name }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Trener</div>
                                <div>{{ training.trainer.first_name }} {{ training.trainer.last_name }}</div>
                            </div>
                            <div v-if="training.notes" class="pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Notatki</div>
                                <div>{{ training.notes }}</div>
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
                                    {{ training.athletes.length }}
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

            <!-- Lista zawodników -->
            <Card>
                <CardHeader>
                    <CardTitle>Zawodnicy na treningu</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="training.athletes.length === 0" class="text-center py-4 text-muted-foreground">
                        Brak zawodników przypisanych do tego treningu
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
                            <TableRow v-for="athlete in training.athletes" :key="athlete.id">
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
        </div>
    </AppLayout>
</template>
