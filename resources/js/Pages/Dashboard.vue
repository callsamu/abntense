<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { DocumentData } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    documents: DocumentData[];
};

function collaborators(doc: DocumentData) {
    return doc.users.map(user => user.name).join(', ');
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
                    <div class="p-6 text-neutral-900 dark:text-neutral-100 w-full flex justify-between flex-wrap gap-10">
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
                            v-for="doc in props.documents"
                        >
                            <h3 class="text-xl font-bold mb-2"> {{ doc.title }}</h3>
                            <p class="text-sm">{{ collaborators(doc) }}</p>
                            <div class="flex justify-between items-center my-3">
                                <p class="text-sm opacity-60">Última Alteração <br> {{ doc.updated_at }}</p>
                                <div>
                                    <Link :href="`/document/${doc.id}`" class="text-sm text-neutral-600 underline hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100">
                                    Editar
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
