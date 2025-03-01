<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

// Walidacja hasła w czasie rzeczywistym
const passwordTouched = ref(false);
const confirmPasswordTouched = ref(false);

// Sprawdzanie zgodności haseł
const passwordsMatch = computed(() => {
    if (!form.password || !form.password_confirmation) return true;
    return form.password === form.password_confirmation;
});

// Komunikat błędu dla niezgodnych haseł
const passwordMatchError = computed(() => {
    if (!passwordTouched.value || !confirmPasswordTouched.value) return '';
    return !passwordsMatch.value ? 'Hasła nie są identyczne' : '';
});

// Monitorowanie zmian w hasłach
watch(() => form.password, () => {
    if (form.password) passwordTouched.value = true;
});

watch(() => form.password_confirmation, () => {
    if (form.password_confirmation) confirmPasswordTouched.value = true;
});

const submit = () => {
    // Nie pozwól na wysłanie formularza, jeśli hasła nie pasują
    if (!passwordsMatch.value) {
        return;
    }

    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthLayout title="Resetowanie hasła" description="Wprowadź nowe hasło poniżej">
        <Head title="Resetowanie hasła" />

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 dark:text-gray-300">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="form.email"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                        readonly
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-gray-700 dark:text-gray-300">Hasło</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        v-model="form.password"
                        :class="[
                            'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800',
                            {'border-red-500 focus:border-red-500 focus:ring-red-500': passwordMatchError && passwordTouched && confirmPasswordTouched}
                        ]"
                        autofocus
                        placeholder="Hasło"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-gray-700 dark:text-gray-300"> Potwierdź hasło </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        :class="[
                            'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800',
                            {'border-red-500 focus:border-red-500 focus:ring-red-500': passwordMatchError && confirmPasswordTouched}
                        ]"
                        placeholder="Potwierdź hasło"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                    <!-- Własny komunikat błędu dla niezgodnych haseł -->
                    <div v-if="passwordMatchError" class="mt-1 text-sm text-red-600">{{ passwordMatchError }}</div>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Zresetuj hasło
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
