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
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { ref, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Treningi',
        href: route('trainings.index'),
    },
    {
        title: 'Dodaj trening',
        href: route('trainings.create'),
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
}

interface Props {
    clubs: Club[];
    preselectedClubId: number | null;
    athletes: Athlete[];
}

const props = defineProps<Props>();

const form = useForm({
    club_id: props.preselectedClubId || '',
    date: '',
    location: '',
    type: 'general',
    notes: '',
    athletes: [] as number[],
});

// Ustawienie wartości początkowej club_id
onMounted(() => {
    if (props.preselectedClubId) {
        form.club_id = props.preselectedClubId;
    }
});

const submit = () => {
    form.post(route('trainings.store'));
};
</script>

<template>
    <Head title="Dodaj trening" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Dodaj nowy trening</CardTitle>
                    <CardDescription>Uzupełnij dane treningu</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <FormField name="club_id">
                                <FormItem>
                                    <FormLabel>Klub *</FormLabel>
                                    <Select v-model="form.club_id">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz klub" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="club in clubs"
                                                :key="club.id"
                                                :value="club.id"
                                            >
                                                {{ club.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.club_id">{{ form.errors.club_id }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="date">
                                <FormItem>
                                    <FormLabel>Data i godzina *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.date" type="datetime-local" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.date">{{ form.errors.date }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="location">
                                <FormItem>
                                    <FormLabel>Lokalizacja *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.location" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.location">{{ form.errors.location }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="type">
                                <FormItem>
                                    <FormLabel>Typ treningu *</FormLabel>
                                    <Select v-model="form.type">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz typ treningu" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="general">Ogólny</SelectItem>
                                            <SelectItem value="technical">Techniczny</SelectItem>
                                            <SelectItem value="strength">Siłowy</SelectItem>
                                            <SelectItem value="conditioning">Kondycyjny</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.type">{{ form.errors.type }}</FormMessage>
                                </FormItem>
                            </FormField>
                        </div>

                        <FormField name="athletes">
                            <FormItem>
                                <FormLabel>Zawodnicy *</FormLabel>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 border rounded-md p-4">
                                    <div v-for="athlete in athletes" :key="athlete.id" class="flex items-center space-x-2">
                                        <Checkbox
                                            :id="`athlete-${athlete.id}`"
                                            :value="athlete.id"
                                            v-model="form.athletes"
                                        />
                                        <Label :for="`athlete-${athlete.id}`">
                                            {{ athlete.last_name }} {{ athlete.first_name }}
                                        </Label>
                                    </div>
                                    <div v-if="athletes.length === 0" class="text-muted-foreground col-span-full">
                                        Brak zawodników w tym klubie. Najpierw wybierz klub.
                                    </div>
                                </div>
                                <FormMessage v-if="form.errors.athletes">{{ form.errors.athletes }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <FormField name="notes">
                            <FormItem>
                                <FormLabel>Notatki</FormLabel>
                                <FormControl>
                                    <Textarea v-model="form.notes" rows="4" />
                                </FormControl>
                                <FormMessage v-if="form.errors.notes">{{ form.errors.notes }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <div class="flex justify-end gap-2">
                            <Link :href="route('trainings.index')">
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
