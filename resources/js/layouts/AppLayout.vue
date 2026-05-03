<template>
    <div class="min-h-screen flex flex-col">
        <!-- Nav -->
        <nav class="glass border-b border-white/[0.06] sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
                <RouterLink to="/" class="flex items-center gap-2">
                    <span class="text-lg font-black text-gradient">Stream</span><span class="text-lg font-black text-white">Avatars</span>
                </RouterLink>

                <div class="flex items-center gap-1">
                    <RouterLink
                        v-for="link in navLinks"
                        :key="link.to"
                        :to="link.to"
                        class="px-3 py-1.5 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-white/5 transition"
                        active-class="text-cyan-400 bg-white/5"
                    >
                        {{ link.label }}
                    </RouterLink>
                </div>

                <div class="flex items-center gap-3">
                    <img
                        v-if="auth.user?.twitch_profile_image"
                        :src="auth.user.twitch_profile_image"
                        class="w-8 h-8 rounded-full ring-2 ring-blue-500/40"
                        :alt="auth.user.twitch_display_name"
                    />
                    <span class="text-sm text-gray-300">{{ auth.user?.twitch_display_name }}</span>
                    <button
                        @click="auth.logout"
                        class="text-xs text-gray-500 hover:text-gray-300 transition"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </nav>

        <main class="flex-1 max-w-7xl mx-auto w-full px-6 py-8">
            <RouterView />
        </main>
    </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const navLinks = [
    { to: '/avatar', label: 'Avatar' },
    { to: '/avatar/editor', label: 'Editor' },
    { to: '/avatar/shop', label: 'Shop' },
    { to: '/avatar/inventory', label: 'Inventory' },
    { to: '/avatar/progress', label: 'Progress' },
];
</script>
