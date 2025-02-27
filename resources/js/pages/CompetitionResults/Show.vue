<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useDateFormat } from '@/composables/useDateFormat';
import { PenIcon, TrashIcon } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Wyniki zawodów',
        href: route('competition-results.index'),
    },
    {
        title: 'Szczegóły wyniku',
        href: '#',
    },
];

interface Competition {
    id: number;
    name: string;
    date: string;
    location: string;
    type: string;
    level: string;
}

interface Athlete {
    id: number;
    first_name: string;
    last_name: string;
    birth_date: string;
    gender: string;
    age_category: string;
}

interface Result {
    id: number;
    competition_id: number;
    athlete_id: number;
    score: number;
    dive_type: string | null;
    difficulty_level: string | null;
    rank: number | null;
    notes: string | null;
    competition: Competition;
    athlete: Athlete;
}

defineProps<{
    result: Result;
}>();

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

// Usunięcie wyniku
const deleteResult = () => {
    if (confirm('Czy na pewno chcesz usunąć ten wynik?')) {
        router.delete(route('competition-results.destroy', result.id));
    }
};
</script>

<template>
    <Head title="Szczegóły wyniku zawodów" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Nagłówek z akcjami -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Wynik zawodów</h1>
                    <p class="text-muted-foreground">
                        {{ result.athlete.last_name }} {{ result.athlete.first_name }} | {{ result.competition.name }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('competition-results.edit', result.id)">
                        <Button>
                            <PenIcon class="h-4 w-4 mr-2" />
                            Edytuj
                        </Button>
                    </Link>
                    <Button variant="destructive" @click="deleteResult">
                        <TrashIcon class="h-4 w-4 mr-2" />
                        Usuń
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Informacje o zawodniku -->
                <Card>
                    <CardHeader>
                        <CardTitle>Zawodnik</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Nazwisko i imię</div>
                                <div>{{ result.athlete.last_name }} {{ result.athlete.first_name }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Data urodzenia</div>
                                <div>{{ formatDate(result.athlete.birth_date) }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Kategoria</div>
                                <div>
                                    <span
                                        class="px-2 py-1 text-xs rounded"
                                        :class="result.athlete.age_category === 'junior' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'"
                                    >
                                        {{ result.athlete.age_category === 'junior' ? 'Junior' : 'Senior' }}
                                    </span>
                                </div>
                            </div>
                            <div class="pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Płeć</div>
                                <div>{{ result.athlete.gender === 'male' ? 'Mężczyzna' : (result.athlete.gender === 'female' ? 'Kobieta' : 'Inne') }}</div>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Link :href="route('athletes.show', result.athlete_id)">
                            <Button variant="outline">
                                Przejdź do profilu zawodnika
                            </Button>
                        </Link>
                    </CardFooter>
                </Card>

                <!-- Informacje o zawodach -->
                <Card>
                    <CardHeader>
                        <CardTitle>Zawody</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Nazwa</div>
                                <div>{{ result.competition.name }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Data</div>
                                <div>{{ formatDate(result.competition.date) }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Lokalizacja</div>
                                <div>{{ result.competition.location }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Typ</div>
                                <div>{{ formatType(result.competition.type) }}</div>
                            </div>
                            <div class="pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Poziom</div>
                                <div>{{ formatLevel(result.competition.level) }}</div>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Link :href="route('competitions.show', result.competition_id)">
                            <Button variant="outline">
                                Przejdź do zawodów
                            </Button>
                        </Link>
                    </CardFooter>
                </Card>
            </div>

            <!-- Informacje o wyniku -->
            <Card>
                <CardHeader>
                    <CardTitle>Szczegóły wyniku</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Wynik</div>
                            <div class="text-xl font-bold">{{ result.score }}</div>
                        </div>
                        <div v-if="result.rank" class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Miejsce</div>
                            <div class="text-xl font-bold">{{ result.rank }}</div>
                        </div>
                        <div v-if="result.dive_type" class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Rodzaj skoku</div>
                            <div>{{ result.dive_type }}</div>
                        </div>
                        <div v-if="result.difficulty_level" class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Poziom trudności</div>
                            <div>{{ result.difficulty_level }}</div>
                        </div>
                        <div v-if="result.notes" class="pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Uwagi</div>
                            <div>{{ result.notes }}</div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
