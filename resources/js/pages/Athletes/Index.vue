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
        title: 'Zawodnicy',
        href: route('athletes.index'),
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
    club: {
        id: number;
        name: string;
    };
}

interface Props {
    athletes: Athlete[];
    clubs: Club[];
    filters: {
        club_id: number | null;
        age_category: string | null;
        gender: string | null;
    };
    selectedClub: Club | null;
}

const props = defineProps<Props>();

// Filtry
const clubId = ref(props.filters.club_id);
const ageCategory = ref(props.filters.age_category);
const gender = ref(props.filters.gender);

const { formatDate } = useDateFormat();

// Aktualizacja filtrów
watch([clubId, ageCategory, gender], () => {
    router.get(route('athletes.index'), {
        club_id: clubId.value || undefined,
        age_category: ageCategory.value || undefined,
        gender: gender.value || undefined,
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
    ageCategory.value = null;
    gender.value = null;
};
</script>

<template>
    <Head title="Zawodnicy" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        Zawodnicy
                        <span v-if="selectedClub">- {{ selectedClub.name }}</span>
                    </h1>
                    <p class="text-muted-foreground">
                        Łącznie zawodników: {{ athletes.length }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('athletes.create', { club_id: clubId })">
                        <Button>
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Dodaj zawodnika
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
                            <label class="text-sm font-medium mb-1 block">Kategoria wiekowa</label>
                            <Select v-model="ageCategory">
                                <SelectTrigger>
                                    <SelectValue placeholder="Wszystkie kategorie" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">Wszystkie kategorie</SelectItem>
                                    <SelectItem value="junior">Junior</SelectItem>
                                    <SelectItem value="senior">Senior</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div>
                            <label class="text-sm font-medium mb-1 block">Płeć</label>
                            <Select v-model="gender">
                                <SelectTrigger>
                                    <SelectValue placeholder="Wszystkie" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">Wszystkie</SelectItem>
                                    <SelectItem value="male">Mężczyzna</SelectItem>
                                    <SelectItem value="female">Kobieta</SelectItem>
                                    <SelectItem value="other">Inne</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="flex items-end">
                            <Button variant="outline" @click="resetFilters">
                                Resetuj filtry
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Lista zawodników -->
            <Card>
                <CardContent class="pt-6">
                    <div v-if="athletes.length === 0" class="text-center py-8 text-muted-foreground">
                        Brak zawodników spełniających kryteria
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nazwisko i imię</TableHead>
                                <TableHead>Data urodzenia</TableHead>
                                <TableHead>Klub</TableHead>
                                <TableHead>Kategoria</TableHead>
                                <TableHead>Płeć</TableHead>
                                <TableHead class="text-right">Akcje</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="athlete in athletes" :key="athlete.id">
                                <TableCell class="font-medium">
                                    {{ athlete.last_name }} {{ athlete.first_name }}
                                </TableCell>
                                <TableCell>{{ formatDate(athlete.birth_date) }}</TableCell>
                                <TableCell>{{ athlete.club.name }}</TableCell>
                                <TableCell>
                                    <span
                                        class="px-2 py-1 text-xs rounded"
                                        :class="athlete.age_category === 'junior' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'"
                                    >
                                        {{ athlete.age_category === 'junior' ? 'Junior' : 'Senior' }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    {{ athlete.gender === 'male' ? 'M' : (athlete.gender === 'female' ? 'K' : 'Inne') }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('athletes.show', athlete.id)">
                                            <Button variant="ghost" size="icon">
                                                <span class="sr-only">Szczegóły</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </Button>
                                        </Link>
                                        <Link :href="route('athletes.edit', athlete.id)">
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
