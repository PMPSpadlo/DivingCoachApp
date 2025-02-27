<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useDateFormat } from '@/composables/useDateFormat';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select';
import { PlusIcon, FilterIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zawody',
        href: route('competitions.index'),
    },
];

interface Club {
    id: number;
    name: string;
}

interface Competition {
    id: number;
    name: string;
    location: string;
    date: string;
    type: string;
    level: string;
    club: {
        id: number;
        name: string;
    };
}

interface Props {
    competitions: {
        data: Competition[];
        links: any;
    };
    clubs: Club[];
    filters: {
        club_id: number | null;
        type: string | null;
        level: string | null;
        status: string | null;
    };
    selectedClub: Club | null;
}

const props = defineProps<Props>();

// Filtry
const clubId = ref(props.filters.club_id);
const type = ref(props.filters.type);
const level = ref(props.filters.level);
const status = ref(props.filters.status);

const { formatDate } = useDateFormat();

// Aktualizacja filtrów
watch([clubId, type, level, status], () => {
    router.get(route('competitions.index'), {
            club_id: clubId.value || undefined,
            type: type.value || undefined,
            level: level.value || undefined,
            status: status.value || undefined,
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
    level.value = null;
    status.value = null;
};

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
</script>

<template>
    <Head title="Zawody" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        Zawody
                        <span v-if="selectedClub">- {{ selectedClub.name }}</span>
                    </h1>
                    <p class="text-muted-foreground">
                        Zarządzaj zawodami dla swoich klubów
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('competitions.create', { club_id: clubId })">
                        <Button>
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Dodaj zawody
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
                            <label class="text-sm font-medium mb-1 block">Typ zawodów</label>
                            <Select v-model="type">
                                <SelectTrigger>
                                    <SelectValue placeholder="Wszystkie typy" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">Wszystkie typy</SelectItem>
                                    <SelectItem value="platform">Wieża</SelectItem>
                                    <SelectItem value="springboard">Trampolina</SelectItem>
                                    <SelectItem value="synchro">Skoki synchroniczne</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div>
                            <label class="text-sm font-medium mb-1 block">Poziom</label>
                            <Select v-model="level">
                                <SelectTrigger>
                                    <SelectValue placeholder="Wszystkie poziomy" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">Wszystkie poziomy</SelectItem>
                                    <SelectItem value="regional">Regionalny</SelectItem>
                                    <SelectItem value="national">Krajowy</SelectItem>
                                    <SelectItem value="international">Międzynarodowy</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div>
                            <label class="text-sm font-medium mb-1 block">Status</label>
                            <Select v-model="status">
                                <SelectTrigger>
                                    <SelectValue placeholder="Wszystkie" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">Wszystkie</SelectItem>
                                    <SelectItem value="upcoming">Nadchodzące</SelectItem>
                                    <SelectItem value="past">Zakończone</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="flex items-end md:col-span-4">
                            <Button variant="outline" @click="resetFilters">
                                Resetuj filtry
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Lista zawodów -->
            <Card>
                <CardContent class="pt-6">
                    <div v-if="competitions.data.length === 0" class="text-center py-8 text-muted-foreground">
                        Brak zawodów spełniających kryteria
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nazwa</TableHead>
                                <TableHead>Data</TableHead>
                                <TableHead>Lokalizacja</TableHead>
                                <TableHead>Klub</TableHead>
                                <TableHead>Typ</TableHead>
                                <TableHead>Poziom</TableHead>
                                <TableHead class="text-right">Akcje</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="competition in competitions.data" :key="competition.id">
                                <TableCell class="font-medium">{{ competition.name }}</TableCell>
                                <TableCell>{{ formatDate(competition.date) }}</TableCell>
                                <TableCell>{{ competition.location }}</TableCell>
                                <TableCell>{{ competition.club.name }}</TableCell>
                                <TableCell>{{ formatType(competition.type) }}</TableCell>
                                <TableCell>{{ formatLevel(competition.level) }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('competitions.show', competition.id)">
                                            <Button variant="ghost" size="icon">
                                                <span class="sr-only">Szczegóły</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </Button>
                                        </Link>
                                        <Link :href="route('competitions.edit', competition.id)">
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
