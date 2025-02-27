<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
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
        title: 'Zawody',
        href: route('competitions.index'),
    },
    {
        title: 'Dodaj zawody',
        href: route('competitions.create'),
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
    name: '',
    location: '',
    date: '',
    club_id: props.preselectedClubId || '',
    judge_panel: '',
    type: 'springboard',
    level: 'regional',
});

// Ustawienie wartości początkowej club_id
onMounted(() => {
    if (props.preselectedClubId) {
        form.club_id = props.preselectedClubId;
    }
});

const submit = () => {
    form.post(route('competitions.store'));
};
</script>

<template>
    <Head title="Dodaj zawody" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Dodaj nowe zawody</CardTitle>
                    <CardDescription>Uzupełnij dane zawodów</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <FormField name="name">
                                <FormItem>
                                    <FormLabel>Nazwa zawodów *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.name" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.name">{{ form.errors.name }}</FormMessage>
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

                            <FormField name="date">
                                <FormItem>
                                    <FormLabel>Data *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.date" type="date" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.date">{{ form.errors.date }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="club_id">
                                <FormItem>
                                    <FormLabel>Klub organizujący *</FormLabel>
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

                            <FormField name="judge_panel">
                                <FormItem>
                                    <FormLabel>Panel sędziowski</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.judge_panel" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.judge_panel">{{ form.errors.judge_panel }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="type">
                                <FormItem>
                                    <FormLabel>Typ zawodów *</FormLabel>
                                    <Select v-model="form.type">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz typ zawodów" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="platform">Wieża</SelectItem>
                                            <SelectItem value="springboard">Trampolina</SelectItem>
                                            <SelectItem value="synchro">Skoki synchroniczne</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.type">{{ form.errors.type }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="level">
                                <FormItem>
                                    <FormLabel>Poziom zawodów *</FormLabel>
                                    <Select v-model="form.level">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz poziom zawodów" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="regional">Regionalny</SelectItem>
                                            <SelectItem value="national">Krajowy</SelectItem>
                                            <SelectItem value="international">Międzynarodowy</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.level">{{ form.errors.level }}</FormMessage>
                                </FormItem>
                            </FormField>
                        </div>

                        <div class="flex justify-end gap-2">
                            <Link :href="route('competitions.index')">
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
