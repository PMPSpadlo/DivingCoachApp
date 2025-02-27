<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Label } from '@/components/ui/label';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kluby',
        href: route('clubs.index'),
    },
    {
        title: 'Edytuj klub',
        href: '#',
    },
];

interface Props {
    club: {
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
    };
}

const props = defineProps<Props>();

const form = useForm({
    name: props.club.name,
    city: props.club.city,
    country: props.club.country || 'Poland',
    address: props.club.address || '',
    phone: props.club.phone || '',
    email: props.club.email || '',
    description: props.club.description || '',
    website: props.club.website || '',
    active: props.club.active,
});

const submit = () => {
    form.put(route('clubs.update', props.club.id));
};
</script>

<template>
    <Head title="Edytuj klub" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Edytuj klub</CardTitle>
                    <CardDescription>Zaktualizuj dane klubu</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <FormField name="name">
                                <FormItem>
                                    <FormLabel>Nazwa klubu *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.name" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.name">{{ form.errors.name }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="city">
                                <FormItem>
                                    <FormLabel>Miasto *</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.city" required />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.city">{{ form.errors.city }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="country">
                                <FormItem>
                                    <FormLabel>Kraj</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.country" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.country">{{ form.errors.country }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="address">
                                <FormItem>
                                    <FormLabel>Adres</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.address" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.address">{{ form.errors.address }}</FormMessage>
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

                            <FormField name="email">
                                <FormItem>
                                    <FormLabel>Email</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.email" type="email" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.email">{{ form.errors.email }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <FormField name="website">
                                <FormItem>
                                    <FormLabel>Strona WWW</FormLabel>
                                    <FormControl>
                                        <Input v-model="form.website" type="url" placeholder="https://" />
                                    </FormControl>
                                    <FormMessage v-if="form.errors.website">{{ form.errors.website }}</FormMessage>
                                </FormItem>
                            </FormField>

                            <div class="flex items-center space-x-2">
                                <Switch v-model:checked="form.active" id="active" />
                                <Label for="active">Aktywny</Label>
                            </div>
                        </div>

                        <FormField name="description">
                            <FormItem>
                                <FormLabel>Opis</FormLabel>
                                <FormControl>
                                    <Textarea v-model="form.description" rows="4" />
                                </FormControl>
                                <FormMessage v-if="form.errors.description">{{ form.errors.description }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <div class="flex justify-end gap-2">
                            <Link :href="route('clubs.show', club.id)">
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
