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
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Płatności',
        href: route('payments.index'),
    },
    {
        title: 'Dodaj płatność',
        href: route('payments.create'),
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
    selectedClub: Club | null;
    clubAthletes: Athlete[];
    selectedAthlete: Athlete | null;
}

const props = defineProps<Props>();

const form = useForm({
    club_id: props.selectedClub?.id || '',
    athlete_id: props.selectedAthlete?.id || '',
    amount: '',
    payment_date: new Date().toISOString().split('T')[0], // Dzisiejsza data
    transaction_id: '',
    currency: 'PLN',
    status: 'paid',
    payment_method: 'cash',
});

// Zmiana klubu - pobranie nowej listy zawodników
const clubId = ref(props.selectedClub?.id || '');
watch(clubId, (newValue) => {
    if (newValue) {
        router.get(route('payments.create'), { club_id: newValue }, {
            preserveState: true,
            replace: true,
        });
        form.club_id = newValue;
        form.athlete_id = '';
    }
});

const submit = () => {
    form.post(route('payments.store'));
};
</script>

<template>
    <Head title="Dodaj płatność" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Dodaj nową płatność</CardTitle>
                    <CardDescription>Uzupełnij informacje o płatności</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <FormField name="club_id">
                                <FormItem>
                                    <FormLabel>Klub *</FormLabel>
                                    <Select v-model="clubId">
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

                            <FormField name="amount">
                                <FormItem>
                                    <FormLabel>Kwota *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.amount" type="number" step="0.01" min="0" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.amount">{{ form.errors.amount }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="currency">
                                <FormItem>
                                    <FormLabel>Waluta *</FormLabel>
                                    <Select v-model="form.currency">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz walutę" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="PLN">PLN</SelectItem>
                                            <SelectItem value="EUR">EUR</SelectItem>
                                            <SelectItem value="USD">USD</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.currency">{{ form.errors.currency }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="payment_date">
                                <FormItem>
                                    <FormLabel>Data płatności *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.payment_date" type="date" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.payment_date">{{ form.errors.payment_date }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="payment_method">
                                <FormItem>
                                    <FormLabel>Metoda płatności *</FormLabel>
                                    <Select v-model="form.payment_method">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz metodę płatności" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="cash">Gotówka</SelectItem>
                                            <SelectItem value="bank_transfer">Przelew bankowy</SelectItem>
                                            <SelectItem value="card">Karta</SelectItem>
                                            <SelectItem value="paypal">PayPal</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.payment_method">{{ form.errors.payment_method }}</FormMessage>
                                </FormItem>
                            </FormField>
                            <FormField name="status">
                                <FormItem>
                                    <FormLabel>Status *</FormLabel>
                                    <Select v-model="form.status">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Wybierz status" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="paid">Opłacona</SelectItem>
                                            <SelectItem value="pending">Oczekująca</SelectItem>
                                            <SelectItem value="overdue">Zaległa</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage v-if="form.errors.status">{{ form.errors.status }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="transaction_id">
                                <FormItem>
                                    <FormLabel>ID transakcji</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.transaction_id" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.transaction_id">{{ form.errors.transaction_id }}</FormMessage>
                                </FormItem>
                            </FormField>
                        </div>

                        <div class="flex justify-end gap-2">
                            <Link :href="route('payments.index')">
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
