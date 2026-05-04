import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAvatarStore = defineStore('avatar', () => {
    const avatar = ref(null);
    const items = ref([]);
    const inventory = ref([]);
    const progress = ref(null);
    const loading = ref(false);
    const pending = ref({});  // slot_item_id → item object (local preview before save)

    let saveTimer = null;

    // Merge saved avatar with local pending overrides for live preview
    const previewAvatar = computed(() => {
        if (!avatar.value) return null;
        return { ...avatar.value, ...pending.value };
    });

    // Items grouped by type
    const itemsByType = computed(() => {
        const map = {};
        for (const item of items.value) {
            if (!map[item.type]) map[item.type] = [];
            map[item.type].push(item);
        }
        return map;
    });

    // IDs the user has unlocked (includes is_default items)
    const unlockedIds = computed(() => {
        const defaults = items.value.filter(i => i.is_default).map(i => i.id);
        const owned = inventory.value.map(i => i.id);
        return new Set([...defaults, ...owned]);
    });

    function isUnlocked(itemId) {
        return unlockedIds.value.has(itemId);
    }

    function isEquipped(slot, itemId) {
        return previewAvatar.value?.[`${slot}_item_id`] === itemId;
    }

    function equipItem(slot, item) {
        if (!isUnlocked(item.id)) return;

        // Toggle off if already equipped
        if (isEquipped(slot, item.id)) {
            pending.value[slot] = null;
            pending.value = { ...pending.value, [`${slot}_item_id`]: null };
        } else {
            pending.value = { ...pending.value, [slot]: item, [`${slot}_item_id`]: item.id };
        }

        debouncedSave();
    }

    function debouncedSave() {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(saveAvatar, 600);
    }

    async function saveAvatar() {
        const slots = ['base','skin','eyes','mouth','hair','shirt','pants','shoes',
                       'hat','glasses','accessory','back','pet','effect','badge'];
        const payload = {};
        for (const slot of slots) {
            payload[`${slot}_item_id`] = previewAvatar.value?.[`${slot}_item_id`] ?? null;
        }

        const { data } = await axios.put('/api/avatar', payload);
        avatar.value = data;
        pending.value = {};
    }

    async function fetchAvatar() {
        const { data } = await axios.get('/api/avatar');
        avatar.value = data;
    }

    async function fetchItems() {
        const { data } = await axios.get('/api/avatar/items');
        items.value = data;
    }

    async function fetchInventory() {
        const { data } = await axios.get('/api/avatar/inventory');
        inventory.value = data;
    }

    async function fetchProgress() {
        const { data } = await axios.get('/api/avatar/progress');
        progress.value = data;
    }

    async function randomize() {
        const { data } = await axios.post('/api/avatar/randomize');
        avatar.value = data;
        pending.value = {};
    }

    async function reset() {
        const { data } = await axios.post('/api/avatar/reset');
        avatar.value = data;
        pending.value = {};
    }

    return {
        avatar, items, inventory, progress, loading, previewAvatar,
        itemsByType, unlockedIds,
        isUnlocked, isEquipped, equipItem,
        fetchAvatar, fetchItems, fetchInventory, fetchProgress,
        saveAvatar, randomize, reset,
    };
});
