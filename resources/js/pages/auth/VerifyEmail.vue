<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout title="Weryfikacja adresu email" description="Prosimy o weryfikację adresu email, klikając w link, który wysłaliśmy na podany adres.">
        <Head title="Weryfikacja email" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600 dark:text-green-400">
            Nowy link weryfikacyjny został wysłany na adres e-mail podany podczas rejestracji.
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button
                :disabled="form.processing"
                variant="secondary"
                class="bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600"
            >
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                Wyślij ponownie email weryfikacyjny
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                Wyloguj się
            </TextLink>
        </form>
    </AuthLayout>
</template>
