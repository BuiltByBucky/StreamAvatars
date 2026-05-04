<template>
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-black text-gradient">Your Avatar</h1>
            <p class="text-gray-500 text-sm mt-1">Customize how you appear on stream.</p>
        </div>

        <div class="flex gap-6">
            <!-- Avatar card -->
            <div class="glass rounded-2xl p-6 flex flex-col items-center gap-5 w-52 shrink-0">
                <AvatarRenderer :avatar="store.avatar" :size="100" />
                <div class="text-center">
                    <p class="font-semibold text-sm">{{ auth.user?.twitch_display_name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Level {{ store.progress?.level ?? 1 }}</p>
                </div>
                <RouterLink to="/avatar/editor" class="btn-primary w-full text-center py-2.5 rounded-xl text-sm font-semibold text-white">
                    Edit Avatar
                </RouterLink>
            </div>

            <!-- Quick nav cards -->
            <div class="flex-1 grid grid-cols-2 gap-4">
                <RouterLink
                    v-for="card in cards"
                    :key="card.to"
                    :to="card.to"
                    class="glass glass-hover rounded-2xl p-5 flex flex-col gap-2 transition cursor-pointer group"
                >
                    <span class="text-2xl">{{ card.icon }}</span>
                    <span class="font-semibold text-white group-hover:text-cyan-400 transition">{{ card.label }}</span>
                    <span class="text-xs text-gray-500">{{ card.desc }}</span>
                </RouterLink>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAvatarStore } from '../../stores/avatar';
import { useAuthStore } from '../../stores/auth';
import AvatarRenderer from '../../components/AvatarRenderer.vue';

const store = useAvatarStore();
const auth = useAuthStore();

const cards = [
    { to: '/avatar/shop',      icon: '🛒', label: 'Shop',      desc: 'Buy items with coins' },
    { to: '/avatar/inventory', icon: '🎒', label: 'Inventory',  desc: 'Your unlocked items' },
    { to: '/avatar/progress',  icon: '📈', label: 'Progress',   desc: 'XP, level & watchtime' },
    { to: '/avatar/settings',  icon: '⚙️', label: 'Settings',   desc: 'Visibility & preferences' },
];

onMounted(async () => {
    await Promise.all([store.fetchAvatar(), store.fetchProgress()]);
});
</script>
