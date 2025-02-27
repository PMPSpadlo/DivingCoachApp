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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Treningi',
        href: route('trainings.index'),
    },
    {
        title: 'Edytuj trening',
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
}

interface Training {
    id: number;
    club_id: number;
    date: string;
    location: string;
    type: string;
    notes: string | null;
}

interface Props {
    training: Training;
    club: Club;
    athletes: Athlete[];
    selectedAthletes: number[];
}

const props = defineProps<Props>();

const form = useForm({
    date: props.training.date,
    location: props.training.location,
    type: props.training.type,
    notes: props.training.notes || '',
    athletes: props.selectedAthletes,
});

const submit = () => {
    form.put(route('trainings.update', props.training.id));
};
</script>

<template>
    <Head title="Edytuj trening" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Edytuj trening</CardTitle>
                    <CardDescription>Zaktualizuj dane treningu</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <FormLabel>Klub</FormLabel>
                                <div class="p-2 border rounded-md">{{ club.name }}</div>
                            </div>

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
                            <Link :href="route('trainings.show', training.id)">
                                <Button type="button" variant="outline">Anuluj</Button>
                            </Link>
                            <Button type="submit" :disabled="form.processing">Zapisz zmiany</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
