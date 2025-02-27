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
        title: 'Płatności',
        href: route('payments.index'),
    },
    {
        title: 'Szczegóły płatności',
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

interface Payment {
    id: number;
    club_id: number;
    athlete_id: number;
    amount: number;
    payment_date: string;
    transaction_id: string | null;
    currency: string;
    status: string;
    payment_method: string;
    club: Club;
    athlete: Athlete;
}

defineProps<{
    payment: Payment;
}>();

const { formatDate } = useDateFormat();

// Formatowanie statusu płatności
const formatStatus = (status: string): string => {
    switch (status) {
        case 'paid': return 'Opłacona';
        case 'pending': return 'Oczekująca';
        case 'overdue': return 'Zaległa';
        default: return status;
    }
};

// Formatowanie metody płatności
const formatPaymentMethod = (method: string): string => {
    switch (method) {
        case 'cash': return 'Gotówka';
        case 'bank_transfer': return 'Przelew bankowy';
        case 'card': return 'Karta';
        case 'paypal': return 'PayPal';
        default: return method;
    }
};

// Określenie klasy dla statusu
const getStatusClass = (status: string): string => {
    switch (status) {
        case 'paid': return 'bg-green-100 text-green-800';
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'overdue': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

// Usunięcie płatności
const deletePayment = () => {
    if (confirm('Czy na pewno chcesz usunąć tę płatność?')) {
        router.delete(route('payments.destroy', payment.id));
    }
};
</script>

<template>
    <Head title="Szczegóły płatności" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Nagłówek z akcjami -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Płatność #{{ payment.id }}</h1>
                    <p class="text-muted-foreground">
                        {{ payment.club.name }} | {{ payment.athlete.last_name }} {{ payment.athlete.first_name }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('payments.edit', payment.id)">
                        <Button>
                            <PenIcon class="h-4 w-4 mr-2" />
                            Edytuj
                        </Button>
                    </Link>
                    <Button variant="destructive" @click="deletePayment">
                        <TrashIcon class="h-4 w-4 mr-2" />
                        Usuń
                    </Button>
                </div>
            </div>

            <!-- Informacje o płatności -->
            <Card>
                <CardHeader>
                    <CardTitle>Informacje o płatności</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Zawodnik</div>
                            <div>{{ payment.athlete.last_name }} {{ payment.athlete.first_name }}</div>
                        </div>
                        <div class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Klub</div>
                            <div>{{ payment.club.name }}</div>
                        </div>
                        <div class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Kwota</div>
                            <div>{{ payment.amount }} {{ payment.currency }}</div>
                        </div>
                        <div class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Data płatności</div>
                            <div>{{ formatDate(payment.payment_date) }}</div>
                        </div>
                        <div class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Status</div>
                            <div>
                                <span
                                    class="px-2 py-1 text-xs rounded"
                                    :class="getStatusClass(payment.status)"
                                >
                                    {{ formatStatus(payment.status) }}
                                </span>
                            </div>
                        </div>
                        <div class="border-b pb-2">
                            <div class="text-sm font-medium text-muted-foreground">Metoda płatności</div>
                            <div>{{ formatPaymentMethod(payment.payment_method) }}</div>
                        </div>
                        <div v-if="payment.transaction_id" class="pb-2">
                            <div class="text-sm font-medium text-muted-foreground">ID transakcji</div>
                            <div>{{ payment.transaction_id }}</div>
                        </div>
                    </div>
                </CardContent>
                <CardFooter>
                    <div class="flex w-full justify-between">
                        <Link :href="route('athletes.payments', payment.athlete_id)">
                            <Button variant="outline">
                                Historia płatności zawodnika
                            </Button>
                        </Link>
                        <Link :href="route('payments.index')">
                            <Button variant="outline">
                                Powrót do listy płatności
                            </Button>
                        </Link>
                    </div>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template>
