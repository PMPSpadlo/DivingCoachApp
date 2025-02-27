<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useDateFormat } from '@/composables/useDateFormat';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import {
    Select, SelectContent, SelectItem, SelectTrigger, SelectValue
} from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { PlusIcon, FilterIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Treningi',
        href: route('trainings.index'),
    },
];

// Definicje typów
interface Club {
    id: number;
    name: string;
}

interface Training {
    id: number;
    date: string;
    location: string;
    type: string;
    club: {
        id: number;
        name: string;
    };
    trainer: {
        id: number;
        first_name: string;
        last_name: string;
    };
}

interface Props {
    trainings: {
        data: Training[];
        links: any;
    };
    clubs: Club[];
    filters: {
        club_id: number | null;
        type: string | null;
        date_from: string | null;
        date_to: string | null;
    };
    selectedClub: Club | null;
}

const props = defineProps<Props>();

// Filtry
const clubId = ref(props.filters.club_id);
const type = ref(props.filters.type);
const dateFrom = ref(props.filters.date_from);
const dateTo = ref(props.filters.date_to);

const { formatDate, formatDateTime } = useDateFormat();

// Aktualizacja filtrów
watch([clubId, type, dateFrom, dateTo], () => {
    router.get(route('trainings.index'), {
            club_id: clubId.value || undefined,
            type: type.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });
});

// Reset filtrów
const resetFilters = () => {
    clubId.value = null;
    type.value = null;
    dateFrom.value = null;
    dateTo.value = null;
};

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
</script>

<template>
    <Head title="Treningi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        Treningi
                        <span v-if="selectedClub">- {{ selectedClub.name }}</span>
                    </h1>
                    <p class="text-muted-foreground">
                        Zarządzaj treningami swoich zawodników
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('trainings.create', { club_id: clubId })">
                        <Button>
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Dodaj trening
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Filtry -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center">
                        <FilterIcon class="h-4 w-4 mr-2" />
                        Filtry
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-4">
                        <div>
                            <label class="text-sm font-medium mb-1 block">Klub</label>
                            <Select v-model="clubId">
                                <SelectTrigger>
                                    <SelectValue placeholder="Wszystkie kluby" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">Wszystkie kluby</SelectItem>
                                    <SelectItem
                                        v-for="club in clubs"
                                        :key="club.id"
                                        :value="club.id"
                                    >
                                        {{ club.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div>
                            <label class="text-sm font-medium mb-1 block">Typ treningu</label>
                            <Select v-model="type">
                                <SelectTrigger>
                                    <SelectValue placeholder="Wszystkie typy" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">Wszystkie typy</SelectItem>
                                    <SelectItem value="general">Ogólny</SelectItem>
                                    <SelectItem value="technical">Techniczny</SelectItem>
                                    <SelectItem value="strength">Siłowy</SelectItem>
                                    <SelectItem value="conditioning">Kondycyjny</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div>
                            <label class="text-sm font-medium mb-1 block">Data od</label>
                            <Input v-model="dateFrom" type="date" />
                        </div>

                        <div>
                            <label class="text-sm font-medium mb-1 block">Data do</label>
                            <Input v-model="dateTo" type="date" />
                        </div>

                        <div class="flex items-end md:col-span-4">
                            <Button variant="outline" @click="resetFilters">
                                Resetuj filtry
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Lista treningów -->
            <Card>
                <CardContent class="pt-6">
                    <div v-if="trainings.data.length === 0" class="text-center py-8 text-muted-foreground">
                        Brak treningów spełniających kryteria
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Data</TableHead>
                                <TableHead>Lokalizacja</TableHead>
                                <TableHead>Klub</TableHead>
                                <TableHead>Typ</TableHead>
                                <TableHead>Trener</TableHead>
                                <TableHead class="text-right">Akcje</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="training in trainings.data" :key="training.id">
                                <TableCell>{{ formatDateTime(training.date) }}</TableCell>
                                <TableCell>{{ training.location }}</TableCell>
                                <TableCell>{{ training.club.name }}</TableCell>
                                <TableCell>{{ formatType(training.type) }}</TableCell>
                                <TableCell>{{ training.trainer.first_name }} {{ training.trainer.last_name }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('trainings.show', training.id)">
                                            <Button variant="ghost" size="icon">
                                                <span class="sr-only">Szczegóły</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </Button>
                                        </Link>
                                        <Link :href="route('trainings.edit', training.id)">
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
