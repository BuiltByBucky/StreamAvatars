<template>
    <div class="flex gap-6 h-full">
        <!-- Left: item browser -->
        <div class="flex-1 space-y-4 min-w-0">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-black text-gradient">Avatar Editor</h1>
                <div class="flex gap-2">
                    <button @click="store.randomize()" class="glass glass-hover px-3 py-1.5 rounded-lg text-sm transition">
                        🎲 Random
                    </button>
                    <button @click="store.reset()" class="glass glass-hover px-3 py-1.5 rounded-lg text-sm transition">
                        ↺ Reset
                    </button>
                </div>
            </div>

            <!-- Slot tabs -->
            <div class="flex gap-1 flex-wrap">
                <button
                    v-for="slot in slots"
                    :key="slot"
                    @click="activeSlot = slot"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition capitalize"
                    :class="activeSlot === slot
                        ? 'bg-blue-600 text-white'
                        : 'glass glass-hover text-gray-400'"
                >
                    {{ slotLabel(slot) }}
                    <span
                        v-if="store.previewAvatar?.[`${slot}_item_id`]"
                        class="ml-1 w-1.5 h-1.5 bg-cyan-400 rounded-full inline-block"
                    ></span>
                </button>
            </div>

            <!-- Item grid -->
            <div v-if="slotItems.length" class="grid grid-cols-4 gap-3">
                <!-- Unequip option -->
                <button
                    @click="unequip(activeSlot)"
                    class="aspect-square rounded-xl border-2 border-dashed border-white/10 hover:border-white/30 flex items-center justify-center text-gray-600 hover:text-gray-400 transition text-xs"
                    :class="!store.previewAvatar?.[`${activeSlot}_item_id`] ? 'border-cyan-400/50' : ''"
                >
                    None
                </button>

                <div
                    v-for="item in slotItems"
                    :key="item.id"
                    @click="store.equipItem(activeSlot, item)"
                    class="relative aspect-square rounded-xl overflow-hidden border-2 transition cursor-pointer group"
                    :class="[
                        store.isEquipped(activeSlot, item.id)
                            ? 'border-cyan-400 glow-cyan'
                            : 'border-white/10 hover:border-white/30',
                        !store.isUnlocked(item.id) ? 'cursor-not-allowed' : '',
                    ]"
                >
                    <!-- Item image -->
                    <img
                        v-if="item.image_path"
                        :src="item.image_path"
                        :alt="item.name"
                        class="w-full h-full object-contain bg-white/5 p-1"
                        @error="e => e.target.style.display = 'none'"
                    />
                    <div
                        v-else
                        class="w-full h-full flex items-center justify-center text-center p-2 text-[10px] text-gray-500"
                        :style="{ background: rarityBg(item.rarity) }"
                    >
                        {{ item.name }}
                    </div>

                    <!-- Rarity pip -->
                    <span
                        class="absolute top-1 left-1 w-2 h-2 rounded-full"
                        :style="{ background: rarityColor(item.rarity) }"
                        :title="item.rarity"
                    ></span>

                    <!-- Lock overlay -->
                    <div
                        v-if="!store.isUnlocked(item.id)"
                        class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center gap-1"
                    >
                        <span class="text-xl">🔒</span>
                        <span v-if="item.price" class="text-[10px] text-yellow-400">{{ item.price }} coins</span>
                    </div>

                    <!-- Equipped tick -->
                    <div
                        v-if="store.isEquipped(activeSlot, item.id)"
                        class="absolute bottom-1 right-1 w-4 h-4 bg-cyan-400 rounded-full flex items-center justify-center text-[8px] text-black font-bold"
                    >✓</div>

                    <!-- Name tooltip on hover -->
                    <div class="absolute bottom-0 left-0 right-0 bg-black/70 text-[9px] text-center py-0.5 opacity-0 group-hover:opacity-100 transition truncate px-1">
                        {{ item.name }}
                    </div>
                </div>
            </div>

            <p v-else class="text-gray-600 text-sm">No items for this slot yet.</p>
        </div>

        <!-- Right: live preview -->
        <div class="w-52 shrink-0 space-y-4">
            <div class="glass rounded-2xl p-4">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Live Preview</p>
                <div class="flex justify-center">
                    <AvatarRenderer :avatar="store.previewAvatar" :size="100" />
                </div>
                <div class="mt-3 space-y-1 text-[10px] text-gray-500">
                    <div
                        v-for="slot in slots"
                        :key="slot"
                        class="flex justify-between"
                        :class="activeSlot === slot ? 'text-cyan-400' : ''"
                    >
                        <span class="capitalize">{{ slotLabel(slot) }}</span>
                        <span class="text-right truncate ml-2 max-w-[80px]">
                            {{ store.previewAvatar?.[slot]?.name ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAvatarStore } from '../../stores/avatar';
import AvatarRenderer from '../../components/AvatarRenderer.vue';

const store = useAvatarStore();
const slots = ['base','skin','eyes','mouth','hair','shirt','pants','shoes','hat','glasses','accessory','back','pet','effect','badge'];
const activeSlot = ref('shirt');

const slotItems = computed(() => store.itemsByType[activeSlot.value] ?? []);

const slotLabels = {
    base: 'Body', skin: 'Skin', eyes: 'Eyes', mouth: 'Mouth', hair: 'Hair',
    shirt: 'Shirt', pants: 'Pants', shoes: 'Shoes', hat: 'Hat', glasses: 'Glasses',
    accessory: 'Acc.', back: 'Back', pet: 'Pet', effect: 'Effect', badge: 'Badge',
};
const slotLabel = (s) => slotLabels[s] ?? s;

function unequip(slot) {
    store.previewAvatar[`${slot}_item_id`] = null;
    store.previewAvatar[slot] = null;
    store.saveAvatar();
}

const rarityColors = {
    common: '#9ca3af', uncommon: '#22c55e', rare: '#3b82f6',
    epic: '#a855f7', legendary: '#f59e0b', mythic: '#ef4444',
    event: '#06b6d4', subscriber: '#ec4899', vip: '#f97316',
};
const rarityColor = (r) => rarityColors[r] ?? '#9ca3af';
const rarityBg = (r) => `${rarityColors[r] ?? '#374151'}22`;

onMounted(async () => {
    await Promise.all([
        store.fetchAvatar(),
        store.fetchItems(),
        store.fetchInventory(),
    ]);
});
</script>
