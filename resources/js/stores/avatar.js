import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useAvatarStore = defineStore('avatar', () => {
    const avatar = ref(null);
    const items = ref([]);
    const inventory = ref([]);
    const progress = ref(null);
    const loading = ref(false);

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

    async function updateAvatar(payload) {
        const { data } = await axios.put('/api/avatar', payload);
        avatar.value = data;
    }

    async function randomize() {
        const { data } = await axios.post('/api/avatar/randomize');
        avatar.value = data;
    }

    async function reset() {
        const { data } = await axios.post('/api/avatar/reset');
        avatar.value = data;
    }

    return {
        avatar, items, inventory, progress, loading,
        fetchAvatar, fetchItems, fetchInventory, fetchProgress,
        updateAvatar, randomize, reset,
    };
});
