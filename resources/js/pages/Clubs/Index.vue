<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { PlusIcon } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kluby',
        href: route('clubs.index'),
    },
];

interface Club {
    id: number;
    name: string;
    city: string;
    country: string;
    address: string | null;
    phone: string | null;
    email: string | null;
    description: string | null;
    website: string | null;
    active: boolean;
    owner_id: number;
}

defineProps<{
    clubs: Club[];
}>();
</script>

<template>
    <Head title="Kluby" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Kluby</h1>
                <Link :href="route('clubs.create')">
                    <Button>
                        <PlusIcon class="h-4 w-4 mr-2" />
                        Dodaj klub
                    </Button>
                </Link>
            </div>

            <div v-if="clubs.length === 0" class="flex items-center justify-center h-64 border rounded-lg">
                <div class="text-center">
                    <p class="text-gray-500 mb-4">Nie masz jeszcze żadnych klubów</p>
                    <Link :href="route('clubs.create')">
                        <Button>
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Dodaj pierwszy klub
                        </Button>
                    </Link>
                </div>
            </div>

            <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="club in clubs" :key="club.id" class="overflow-hidden">
                    <CardHeader>
                        <CardTitle>{{ club.name }}</CardTitle>
                        <div class="text-sm text-muted-foreground">{{ club.city }}, {{ club.country }}</div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="club.description" class="text-sm line-clamp-3">
                            {{ club.description }}
                        </div>
                        <div v-else class="text-sm text-muted-foreground italic">
                            Brak opisu
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-between">
                        <Link :href="route('clubs.show', club.id)">
                            <Button variant="outline">Szczegóły</Button>
                        </Link>
                        <Link :href="route('clubs.edit', club.id)">
                            <Button variant="outline">Edytuj</Button>
                        </Link>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
