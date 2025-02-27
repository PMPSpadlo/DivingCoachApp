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
import { ref, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zawodnicy',
        href: route('athletes.index'),
    },
    {
        title: 'Dodaj zawodnika',
        href: route('athletes.create'),
    },
];

interface Club {
    id: number;
    name: string;
}

interface Props {
    clubs: Club[];
    preselectedClubId: number | null;
}

const props = defineProps<Props>();

const form = useForm({
    first_name: '',
    last_name: '',
    birth_date: '',
    gender: 'male',
    club_id: props.preselectedClubId || '',
    email: '',
    phone: '',
    notes: '',
    age_category: 'junior',
});

// Ustawienie wartości początkowej club_id
onMounted(() => {
    if (props.preselectedClubId) {
        form.club_id = props.preselectedClubId;
    }
});

const submit = () => {
    form.post(route('athletes.store'));
};
</script>

<template>
    <Head title="Dodaj zawodnika" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Dodaj nowego zawodnika</CardTitle>
                    <CardDescription>Uzupełnij dane zawodnika</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <FormField name="first_name">
                                <FormItem>
                                    <FormLabel>Imię *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.first_name" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.first_name">{{ form.errors.first_name }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="last_name">
                                <FormItem>
                                    <FormLabel>Nazwisko *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.last_name" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.last_name">{{ form.errors.last_name }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="birth_date">
                                <FormItem>
                                    <FormLabel>Data urodzenia *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.birth_date" type="date" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.birth_date">{{ form.errors.birth_date }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="gender">
                                <FormItem>
                                    <FormLabel>Płeć *</FormLabel>
                                    <Select v-model="form.gender">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz płeć" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="male">Mężczyzna</SelectItem>
                                            <SelectItem value="female">Kobieta</SelectItem>
                                            <SelectItem value="other">Inne</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.gender">{{ form.errors.gender }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="age_category">
                                <FormItem>
                                    <FormLabel>Kategoria wiekowa *</FormLabel>
                                    <Select v-model="form.age_category">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz kategorię" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="junior">Junior</SelectItem>
                                            <SelectItem value="senior">Senior</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.age_category">{{ form.errors.age_category }}</FormMessage>
                                </FormItem>
                            </FormField>

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

                            <FormField name="email">
                                <FormItem>
                                    <FormLabel>Email</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.email" type="email" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.email">{{ form.errors.email }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="phone">
                                <FormItem>
                                    <FormLabel>Telefon</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.phone" type="tel" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.phone">{{ form.errors.phone }}</FormMessage>
                                </FormItem>
                            </FormField>
                        </div>

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
                            <Link :href="route('athletes.index')">
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
