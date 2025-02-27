<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Wyniki zawodów',
        href: route('competition-results.index'),
    },
    {
        title: 'Dodaj wynik',
        href: route('competition-results.create'),
    },
];

interface Competition {
    id: number;
    name: string;
    date: string;
    location: string;
}

interface Athlete {
    id: number;
    first_name: string;
    last_name: string;
}

interface Props {
    competitions: Competition[];
    selectedCompetition: Competition | null;
    clubAthletes: Athlete[];
    selectedAthlete: Athlete | null;
}

const props = defineProps<Props>();

const form = useForm({
    competition_id: props.selectedCompetition?.id || '',
    athlete_id: props.selectedAthlete?.id || '',
    score: '',
    dive_type: '',
    difficulty_level: '',
    rank: '',
    notes: '',
});

const submit = () => {
    form.post(route('competition-results.store'));
};
</script>

<template>
    <Head title="Dodaj wynik zawodów" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Dodaj nowy wynik zawodów</CardTitle>
                    <CardDescription>Uzupełnij informacje o wyniku zawodnika</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <FormField name="competition_id">
                                <FormItem>
                                    <FormLabel>Zawody *</FormLabel>
                                    <Select v-model="form.competition_id">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz zawody" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="competition in competitions"
                                                :key="competition.id"
                                                :value="competition.id"
                                            >
                                                {{ competition.name }} ({{ competition.date }})
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.competition_id">{{ form.errors.competition_id }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="athlete_id">
                                <FormItem>
                                    <FormLabel>Zawodnik *</FormLabel>
                                    <Select v-model="form.athlete_id">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz zawodnika" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="athlete in clubAthletes"
                                                :key="athlete.id"
                                                :value="athlete.id"
                                            >
                                                {{ athlete.last_name }} {{ athlete.first_name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.athlete_id">{{ form.errors.athlete_id }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="score">
                                <FormItem>
                                    <FormLabel>Wynik *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.score" type="number" step="0.01" min="0" max="100" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.score">{{ form.errors.score }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="rank">
                                <FormItem>
                                    <FormLabel>Miejsce</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.rank" type="number" min="1" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.rank">{{ form.errors.rank }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="dive_type">
                                <FormItem>
                                    <FormLabel>Rodzaj skoku</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.dive_type" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.dive_type">{{ form.errors.dive_type }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="difficulty_level">
                                <FormItem>
                                    <FormLabel>Poziom trudności</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.difficulty_level" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.difficulty_level">{{ form.errors.difficulty_level }}</FormMessage>
                                </FormItem>
                            </FormField>
                        </div>

                        <FormField name="notes">
                            <FormItem>
                                <FormLabel>Uwagi</FormLabel>
                                <FormControl>
                                    <Textarea v-model="form.notes" rows="4" />
                                </FormControl>
                                <FormMessage v-if="form.errors.notes">{{ form.errors.notes }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <div class="flex justify-end gap-2">
                            <Link :href="route('competition-results.index')">
                                <Button type="button" variant="outline">Anuluj</Button>
                            </Link>
                            <Button type="submit" :disabled="form.processing">Zapisz</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
