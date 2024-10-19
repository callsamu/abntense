<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    title: '',
});
</script>
<template>
    <Head title="Novo Documento" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-bold leading-tight text-neutral-800 dark:text-neutral-200"
            >
                Novo Documento
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-neutral-800"
                >
                    <form
                        @submit.prevent="form.post(route('document.create'))"
                        class="p-10 text-neutral-900 dark:text-neutral-100 w-full flex flex-col gap-4"
                    >
                        <div>
                            <h2 class="text-lg font-medium"> Título do Documento </h2>
                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Será usado para se referir ao documento por você e os colaboradores.</p>
                            <div class="my-4 flex flex-col gap-4">
                                <div>
                                    <InputLabel for="title" value="Título" />
                                    <TextInput
                                        v-model="form.title"
                                        name="title"
                                        id="title"
                                        type="text"
                                        required
                                        autofocus
                                        class="mt-1 block w-96"
                                    />
                                </div>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-lg font-medium"> Colaboradores </h2>
                            <span class="font-mono my-4 opacity-60"> não-implementado </span>
                        </div>
                        <PrimaryButton class="w-fit mt-7 px-2"> Criar </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
