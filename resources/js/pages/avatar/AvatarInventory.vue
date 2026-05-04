<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-gradient">Inventory</h1>
                <p class="text-gray-500 text-sm mt-1">{{ store.inventory.length }} item{{ store.inventory.length !== 1 ? 's' : '' }} unlocked</p>
            </div>
            <select v-model="filterType" class="glass rounded-lg px-3 py-1.5 text-sm text-gray-300 bg-transparent border-0 outline-none">
                <option value="">All types</option>
                <option v-for="type in types" :key="type" :value="type" class="bg-gray-900 capitalize">{{ type }}</option>
            </select>
        </div>

        <div v-if="filteredItems.length" class="grid grid-cols-5 gap-3">
            <div
                v-for="item in filteredItems"
                :key="item.id"
                class="glass rounded-xl overflow-hidden group"
            >
                <div class="aspect-square relative">
                    <img
                        v-if="item.image_path"
                        :src="item.image_path"
                        :alt="item.name"
                        class="w-full h-full object-contain p-2 bg-white/5"
                        @error="e => e.target.style.display = 'none'"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center text-xs text-gray-600 p-2" :style="{ background: rarityBg(item.rarity) }">
                        {{ item.name }}
                    </div>
                    <span class="absolute top-1 left-1 w-2 h-2 rounded-full" :style="{ background: rarityColor(item.rarity) }"></span>
                </div>
                <div class="px-2 pb-2">
                    <p class="text-[10px] text-white truncate">{{ item.name }}</p>
                    <p class="text-[9px] text-gray-500 capitalize">{{ item.type }} · {{ item.rarity }}</p>
                </div>
            </div>
        </div>

        <div v-else class="glass rounded-2xl p-12 flex flex-col items-center gap-3">
            <span class="text-4xl">🎒</span>
            <p class="text-gray-600 text-sm">No items yet. Watch streams and earn rewards!</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAvatarStore } from '../../stores/avatar';

const store = useAvatarStore();
const filterType = ref('');

const types = computed(() => [...new Set(store.inventory.map(i => i.type))].sort());
const filteredItems = computed(() =>
    filterType.value
        ? store.inventory.filter(i => i.type === filterType.value)
        : store.inventory
);

const rarityColors = {
    common: '#9ca3af', uncommon: '#22c55e', rare: '#3b82f6',
    epic: '#a855f7', legendary: '#f59e0b', mythic: '#ef4444',
    event: '#06b6d4', subscriber: '#ec4899', vip: '#f97316',
};
const rarityColor = (r) => rarityColors[r] ?? '#9ca3af';
const rarityBg = (r) => `${rarityColors[r] ?? '#374151'}22`;

onMounted(() => {
    store.fetchInventory();
    store.fetchItems();
});
</script>
