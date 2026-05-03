import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const loading = ref(false);

    const isAdmin = computed(() => user.value?.role === 'admin');
    const isModerator = computed(() => user.value?.role === 'moderator' || isAdmin.value);
    const isSubscriber = computed(() => user.value?.is_subscriber ?? false);
    const isVip = computed(() => user.value?.is_vip ?? false);

    async function fetchUser() {
        loading.value = true;
        try {
            const { data } = await axios.get('/api/me');
            user.value = data;
        } catch {
            user.value = null;
        } finally {
            loading.value = false;
        }
    }

    async function logout() {
        await axios.post('/logout');
        user.value = null;
        window.location.href = '/';
    }

    return { user, loading, isAdmin, isModerator, isSubscriber, isVip, fetchUser, logout };
});
