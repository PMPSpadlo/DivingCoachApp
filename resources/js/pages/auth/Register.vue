<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// Walidacja hasła w czasie rzeczywistym
const passwordError = ref('');
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

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Utwórz konto" description="Wprowadź swoje dane, aby założyć konto">
        <Head title="Rejestracja" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="first_name" class="text-gray-700 dark:text-gray-300">Imię</Label>
                    <Input
                        id="first_name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="given-name"
                        v-model="form.first_name"
                        placeholder="Imię"
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                    />
                    <InputError :message="form.errors.first_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="last_name" class="text-gray-700 dark:text-gray-300">Nazwisko</Label>
                    <Input
                        id="last_name"
                        type="text"
                        required
                        :tabindex="2"
                        autocomplete="family-name"
                        v-model="form.last_name"
                        placeholder="Nazwisko"
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                    />
                    <InputError :message="form.errors.last_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 dark:text-gray-300">Adres email</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="3"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@przyklad.pl"
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-gray-700 dark:text-gray-300">Hasło</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Hasło"
                        :class="[
                            'rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800',
                            {'border-red-500 focus:border-red-500 focus:ring-red-500': passwordMatchError && passwordTouched && confirmPasswordTouched}
                        ]"
                    />
                    <InputError :message="form.errors.password" />
                    <InputError :message="passwordError" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-gray-700 dark:text-gray-300">Potwierdź hasło</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Potwierdź hasło"
                        :class="[
                            'rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800',
                            {'border-red-500 focus:border-red-500 focus:ring-red-500': passwordMatchError && confirmPasswordTouched}
                        ]"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                    <!-- Własny komunikat błędu dla niezgodnych haseł -->
                    <div v-if="passwordMatchError" class="mt-1 text-sm text-red-600">{{ passwordMatchError }}</div>
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600"
                    tabindex="6"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Utwórz konto
                </Button>
            </div>

            <div class="text-center text-sm text-gray-600 dark:text-gray-300">
                Masz już konto?
                <TextLink :href="route('login')" class="text-blue-600 dark:text-blue-400" :tabindex="7">Zaloguj się</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
