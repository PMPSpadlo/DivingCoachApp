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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zawody',
        href: route('competitions.index'),
    },
    {
        title: 'Edytuj zawody',
        href: '#',
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
    club_id: number;
    judge_panel: string | null;
    type: string;
    level: string;
}

interface Props {
    competition: Competition;
    club: Club;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.competition.name,
    location: props.competition.location,
    date: props.competition.date,
    judge_panel: props.competition.judge_panel || '',
    type: props.competition.type,
    level: props.competition.level,
});

const submit = () => {
    form.put(route('competitions.update', props.competition.id));
};
</script>

<template>
    <Head title="Edytuj zawody" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Edytuj zawody</CardTitle>
                    <CardDescription>Zaktualizuj dane zawodów</CardDescription>
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

                            <div>
                                <FormLabel>Klub organizujący</FormLabel>
                                <div class="p-2 border rounded-md">{{ club.name }}</div>
                            </div>

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
                            <Link :href="route('competitions.show', competition.id)">
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
