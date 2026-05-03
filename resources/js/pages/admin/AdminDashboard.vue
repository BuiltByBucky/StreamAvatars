<template>
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-black text-gradient">Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your stream overlay.</p>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div v-for="stat in stats" :key="stat.label" class="glass rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wider">{{ stat.label }}</p>
                <p class="text-2xl font-black text-gradient mt-1">{{ stat.value }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
const stats = [
    { label: 'Active viewers', value: '0' },
    { label: 'Total users', value: '0' },
    { label: 'Items', value: '0' },
];
</script>
