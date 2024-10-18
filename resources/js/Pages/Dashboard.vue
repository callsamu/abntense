<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

interface Document {
    title: string;
    collaborators: string[];
    updated_at: string;
}

interface Props {
    documents: Document;
}

const props = defineProps<Props>();
</script>
<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-neutral-800 dark:text-neutral-200"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-neutral-800"
                >
                    <div class="p-6 text-neutral-900 dark:text-neutral-100 w-full flex flex-wrap gap-4">
                        <div
                            class="bg-neutral-900 w-80 h-60 flex justify-center items-center rounded-xl"

                        >
                            <h2 class="text-3xl font-bold">+ Novo</h2>
                        </div>
                        <div
                            class="
                                bg-neutral-900 w-80 p-6 rounded-xl
                                flex flex-col justify-center
                            "
                            v-for="document in props.documents"
                        >
                            <h3 class="text-xl font-bold mb-2"> {{ document.title }}</h3>
                            <p class="text-sm">{{ document.collaborators.join(', ') }}</p>
                            <div class="flex justify-between items-center my-3">
                                <p class="text-sm opacity-60">Última Alteração <br> {{ document.updated_at }}</p>
                                <div>
                                    Editar
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
