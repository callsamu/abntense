<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import { ChipInput } from '@monorepo/ui';

const form = useForm({
    title: '',
    metadata: {
        description: '',
        location: '',
        institution: '',
        authors: [],
    },
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
                                <div class="mx-2">
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
                            <h2 class="text-lg font-medium"> Autores </h2>
                            <ChipInput v-model="form.metadata.authors" />

                        </div>
                        <div>
                            <h2 class="text-lg font-medium m-0"> Metadados </h2>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                Informações utilizadas na capa e na folha de rosto do documento
                            </p>
                            <div class="my-6 mx-2 grid grid-cols-2 gap-8">
                                <div class="row-span-2">
                                    <InputLabel for="location" value="Descrição" />
                                    <TextArea
                                        id="description"
                                        v-model="form.metadata.description"
                                        spellcheck="false"
                                        rows="5"
                                        class="mt-1 block w-full bg resize-none"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="location" value="Local" />
                                    <TextInput
                                        id="location"
                                        v-model="form.metadata.location"
                                        class="mt-1 block w-full"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="institution" value="Instituição" />
                                    <TextInput
                                        id="Institution"
                                        v-model="form.metadata.institution"
                                        class="mt-1 block w-full"
                                    />
                                </div>
                            </div>
                        </div>
                        <PrimaryButton class="w-fit mt-7 px-2"> Criar </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
