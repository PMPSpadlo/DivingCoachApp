<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout title="Nie pamiętasz hasła?" description="Podaj swój email, aby otrzymać link do resetowania hasła">
        <Head title="Przypomnienie hasła" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 dark:text-gray-300">Adres email</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="off"
                        v-model="form.email"
                        autofocus
                        placeholder="email@przyklad.pl"
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button
                        class="w-full bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                        Wyślij link do resetowania hasła
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-gray-600 dark:text-gray-300">
                <span>Lub wróć do</span>
                <TextLink :href="route('login')" class="text-blue-600 dark:text-blue-400">logowania</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
