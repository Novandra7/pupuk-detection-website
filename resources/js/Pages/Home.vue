<template>
    <Head title="Dashboard" />
    <MainLayout title="Dashboard">
        <Welcome class="mb-3" />
        <div class = "mb-3">
            <Tabs/>
        </div>
    </MainLayout>
</template>
<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import Welcome from '@/Widgets/Home/Welcome.vue';
import Tabs from '@/Components/Tabs.vue';

</script>