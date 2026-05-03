<template>
    <div class="min-h-screen flex">
        <aside class="w-56 glass border-r border-white/[0.06] p-4 flex flex-col gap-1 shrink-0">
            <RouterLink to="/" class="mb-4 block">
                <span class="text-sm font-black text-gradient">StreamAvatars</span>
                <span class="block text-[10px] text-gray-500 uppercase tracking-widest">Admin</span>
            </RouterLink>
            <RouterLink
                v-for="link in links"
                :key="link.to"
                :to="link.to"
                class="px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-white/5 transition"
                active-class="text-cyan-400 bg-white/5"
            >
                {{ link.label }}
            </RouterLink>
        </aside>
        <main class="flex-1 p-8 overflow-auto">
            <RouterView />
        </main>
    </div>
</template>

<script setup>
const links = [
    { to: '/admin', label: 'Dashboard' },
];
</script>
