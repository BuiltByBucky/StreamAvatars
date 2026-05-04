<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-black text-gradient">Progress</h1>
            <p class="text-gray-500 text-sm mt-1">Your XP, level, and watch stats.</p>
        </div>

        <div v-if="store.progress" class="grid grid-cols-3 gap-4">
            <div v-for="stat in stats" :key="stat.label" class="glass rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wider">{{ stat.label }}</p>
                <p class="text-3xl font-black text-gradient mt-1">{{ stat.value }}</p>
            </div>
        </div>

        <!-- XP progress bar -->
        <div v-if="store.progress" class="glass rounded-2xl p-5 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Level {{ store.progress.level }}</span>
                <span class="text-gray-500">{{ store.progress.xp }} / {{ xpForNextLevel }} XP</span>
            </div>
            <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                <div
                    class="h-full rounded-full transition-all duration-500"
                    style="background: linear-gradient(90deg, #3b82f6, #06b6d4)"
                    :style="{ width: xpPercent + '%' }"
                ></div>
            </div>
        </div>

        <div v-else class="glass rounded-2xl p-8 flex items-center justify-center">
            <p class="text-gray-600 text-sm">Loading...</p>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useAvatarStore } from '../../stores/avatar';

const store = useAvatarStore();

const levelThresholds = [0, 100, 250, 500, 1000, 2000, 4000, 7500, 12500, 20000, 30000];
const xpForNextLevel = computed(() => levelThresholds[store.progress?.level] ?? 50000);
const xpPercent = computed(() => {
    if (!store.progress) return 0;
    const prev = levelThresholds[(store.progress.level - 1)] ?? 0;
    const next = xpForNextLevel.value;
    return Math.min(100, ((store.progress.xp - prev) / (next - prev)) * 100);
});

const stats = computed(() => store.progress ? [
    { label: 'Level',     value: store.progress.level },
    { label: 'XP',        value: store.progress.xp.toLocaleString() },
    { label: 'Coins',     value: store.progress.coins.toLocaleString() },
    { label: 'Watchtime', value: `${store.progress.watchtime_minutes}m` },
    { label: 'Messages',  value: store.progress.chat_messages_count },
] : []);

onMounted(() => store.fetchProgress());
</script>
