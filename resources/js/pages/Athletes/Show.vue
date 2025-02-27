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
    CalendarIcon,
    TrophyIcon,
    CreditCardIcon
} from 'lucide-vue-next';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zawodnicy',
        href: route('athletes.index'),
    },
    {
        title: 'Profil zawodnika',
        href: '#',
    },
];

// Definicja interfejsów zgodnie z rzeczywistą strukturą modeli
interface Club {
    id: number;
    name: string;
}

// Trening z relacji belongsToMany może zawierać dodatkowe pola z tabeli pośredniej
interface Training {
    id: number;
    date: string;
    location: string;
    type: string;
    notes: string | null;
    // Potencjalne pola relacji pivot, jeśli są używane
    pivot?: {
        athlete_id: number;
        training_id: number;
        created_at?: string;
        updated_at?: string;
    };
}

interface Competition {
    id: number;
    name: string;
    date: string;
    location: string;
    type: string;
    level: string;
    // Pivot dla relacji belongsToMany
    pivot?: {
        athlete_id: number;
        competition_id: number;
    };
}

interface CompetitionResult {
    id: number;
    score: number;
    dive_type: string | null;
    difficulty_level: string | null;
    rank: number | null;
    competition?: Competition;
}

interface Payment {
    id: number;
    amount: number;
    payment_date: string;
    status: string;
    currency: string;
}

interface Athlete {
    id: number;
    first_name: string;
    last_name: string;
    birth_date: string;
    gender: string;
    age_category: string;
    email: string | null;
    phone: string | null;
    notes: string | null;
    club?: Club;
    trainings?: Training[];
    competitions?: Competition[]; // Nowa relacja bezpośrednia do zawodów
    competitionResults?: CompetitionResult[];
    payments?: Payment[];
}

const props = defineProps<{
    athlete: Athlete;
    directResults?: any[];
}>();

// Bezpieczne computed properties
const trainings = computed(() => props.athlete.trainings || []);
const competitions = computed(() => props.athlete.competitions || []); // Nowa computed property
const competitionResults = computed(() => props.directResults || []);
const payments = computed(() => props.athlete.payments || []);

const { formatDate } = useDateFormat();

// Formatowanie typu treningu
const formatTrainingType = (type: string): string => {
    switch (type) {
        case 'general': return 'Ogólny';
        case 'technical': return 'Techniczny';
        case 'strength': return 'Siłowy';
        case 'conditioning': return 'Kondycyjny';
        default: return type;
    }
};

// Formatowanie typu zawodów
const formatCompetitionType = (type: string): string => {
    switch (type) {
        case 'platform': return 'Wieża';
        case 'springboard': return 'Trampolina';
        case 'synchro': return 'Skoki synchroniczne';
        default: return type;
    }
};

// Formatowanie poziomu zawodów
const formatCompetitionLevel = (level: string): string => {
    switch (level) {
        case 'regional': return 'Regionalny';
        case 'national': return 'Krajowy';
        case 'international': return 'Międzynarodowy';
        default: return level;
    }
};

// Funkcja do formatowania płci
const formatGender = (gender: string): string => {
    switch (gender) {
        case 'male': return 'Mężczyzna';
        case 'female': return 'Kobieta';
        default: return 'Inne';
    }
};

// Funkcja do formatowania statusu płatności
const formatPaymentStatus = (status: string): string => {
    switch (status) {
        case 'paid': return 'Opłacona';
        case 'pending': return 'Oczekująca';
        case 'overdue': return 'Zaległa';
        default: return status;
    }
};

// Funkcja do określenia klasy CSS dla statusu płatności
const getPaymentStatusClass = (status: string): string => {
    switch (status) {
        case 'paid': return 'bg-green-100 text-green-800';
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'overdue': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head :title="`${athlete.first_name} ${athlete.last_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Nagłówek z akcjami -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ athlete.last_name }} {{ athlete.first_name }}</h1>
                    <p class="text-muted-foreground">
                        {{ athlete.club?.name }} |
                        <span
                            class="px-2 py-1 text-xs rounded"
                            :class="athlete.age_category === 'junior' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'"
                        >
                           {{ athlete.age_category === 'junior' ? 'Junior' : 'Senior' }}
                       </span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('athletes.edit', athlete.id)">
                        <Button>
                            <PenIcon class="h-4 w-4 mr-2" />
                            Edytuj
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Informacje o zawodniku -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informacje</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Data urodzenia</div>
                                <div>{{ formatDate(athlete.birth_date) }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Płeć</div>
                                <div>{{ formatGender(athlete.gender) }}</div>
                            </div>
                            <div v-if="athlete.email" class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Email</div>
                                <div>{{ athlete.email }}</div>
                            </div>
                            <div v-if="athlete.phone" class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Telefon</div>
                                <div>{{ athlete.phone }}</div>
                            </div>
                            <div class="border-b pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Klub</div>
                                <div>{{ athlete.club?.name }}</div>
                            </div>
                            <div v-if="athlete.notes" class="pb-2">
                                <div class="text-sm font-medium text-muted-foreground">Notatki</div>
                                <div>{{ athlete.notes }}</div>
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
                                    {{ trainings.length }}
                                </div>
                                <div class="text-center text-sm font-medium text-muted-foreground">
                                    Treningów
                                </div>
                            </div>
                            <div class="space-y-2 rounded-lg border p-4">
                                <div class="flex items-center justify-center text-3xl font-bold">
                                    {{ competitions.length }}
                                </div>
                                <div class="text-center text-sm font-medium text-muted-foreground">
                                    Zawodów
                                </div>
                            </div>
                            <!-- Dodatkowe statystyki można dodać później -->
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Treningi -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Treningi</CardTitle>
                        <CardDescription>Historia treningów zawodnika</CardDescription>
                    </div>
                    <Link :href="route('trainings.index')" :data="{ athlete_id: athlete.id }">
                        <Button variant="outline" size="sm">
                            Zobacz wszystkie
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div v-if="trainings.length === 0" class="text-center py-4 text-muted-foreground">
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
                            <TableRow v-for="training in trainings" :key="training.id">
                                <TableCell>{{ formatDate(training.date) }}</TableCell>
                                <TableCell>{{ training.location }}</TableCell>
                                <TableCell>{{ formatTrainingType(training.type) }}</TableCell>
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

            <!-- Zawody (używając nowej relacji competitions) -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Zawody</CardTitle>
                        <CardDescription>Historia udziału w zawodach</CardDescription>
                    </div>
                    <Link :href="route('athletes.results', athlete.id)">
                        <Button variant="outline" size="sm">
                            Zobacz wyniki
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div v-if="competitions.length === 0" class="text-center py-4 text-muted-foreground">
                        Brak udziału w zawodach
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nazwa</TableHead>
                                <TableHead>Data</TableHead>
                                <TableHead>Lokalizacja</TableHead>
                                <TableHead>Typ</TableHead>
                                <TableHead>Poziom</TableHead>
                                <TableHead class="text-right">Akcje</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="competition in competitions" :key="competition.id">
                                <TableCell>{{ competition.name }}</TableCell>
                                <TableCell>{{ formatDate(competition.date) }}</TableCell>
                                <TableCell>{{ competition.location }}</TableCell>
                                <TableCell>{{ formatCompetitionType(competition.type) }}</TableCell>
                                <TableCell>{{ formatCompetitionLevel(competition.level) }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('competitions.show', competition.id)">
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

            <!-- Wyniki zawodów -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Wyniki zawodów</CardTitle>
                        <CardDescription>Historia wyników</CardDescription>
                    </div>
                    <Link :href="route('athletes.results', athlete.id)">
                        <Button variant="outline" size="sm">
                            Zobacz wszystkie
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div v-if="competitionResults.length === 0" class="text-center py-4 text-muted-foreground">
                        Brak wyników z zawodów
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Zawody</TableHead>
                                <TableHead>Wynik</TableHead>
                                <TableHead>Rodzaj skoku</TableHead>
                                <TableHead>Trudność</TableHead>
                                <TableHead>Miejsce</TableHead>
                                <TableHead class="text-right">Akcje</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="result in competitionResults" :key="result.id">
                                <TableCell>
                                    <div>{{ result.competition?.name || '-' }}</div>
                                    <div class="text-xs text-muted-foreground">{{ result.competition?.date ? formatDate(result.competition.date) : '-' }}</div>
                                </TableCell>
                                <TableCell>{{ result.score }}</TableCell>
                                <TableCell>{{ result.dive_type || '-' }}</TableCell>
                                <TableCell>{{ result.difficulty_level || '-' }}</TableCell>
                                <TableCell>
                                    <span v-if="result.rank" class="font-semibold">{{ result.rank }}</span>
                                    <span v-else>-</span>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('competition-results.show', result.id)">
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

            <!-- Płatności -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Płatności</CardTitle>
                        <CardDescription>Historia płatności za treningi</CardDescription>
                    </div>
                    <Link :href="route('athletes.payments', athlete.id)">
                        <Button variant="outline" size="sm">
                            Zobacz wszystkie
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div v-if="payments.length === 0" class="text-center py-4 text-muted-foreground">
                        Brak zarejestrowanych płatności
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Data</TableHead>
                                <TableHead>Kwota</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Akcje</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="payment in payments" :key="payment.id">
                                <TableCell>{{ formatDate(payment.payment_date) }}</TableCell>
                                <TableCell>{{ payment.amount }} {{ payment.currency }}</TableCell>
                                <TableCell>
                                   <span
                                       class="px-2 py-1 text-xs rounded"
                                       :class="getPaymentStatusClass(payment.status)"
                                   >
                                       {{ formatPaymentStatus(payment.status) }}
                                   </span>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('payments.show', payment.id)">
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
