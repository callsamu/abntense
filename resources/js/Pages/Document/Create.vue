<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

<template>
    <Head title="Novo Documento" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl text-white"
            >
                Novo Documento
            </h2>
        </template>
        <div>
        </div>
    </AuthenticatedLayout>
</template>
