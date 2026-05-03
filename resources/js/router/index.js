import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        component: () => import('../pages/HomePage.vue'),
    },
    {
        path: '/avatar',
        component: () => import('../layouts/AppLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            { path: '', component: () => import('../pages/avatar/AvatarPage.vue') },
            { path: 'editor', component: () => import('../pages/avatar/AvatarEditor.vue') },
            { path: 'shop', component: () => import('../pages/avatar/AvatarShop.vue') },
            { path: 'inventory', component: () => import('../pages/avatar/AvatarInventory.vue') },
            { path: 'progress', component: () => import('../pages/avatar/AvatarProgress.vue') },
            { path: 'settings', component: () => import('../pages/avatar/AvatarSettings.vue') },
        ],
    },
    {
        path: '/overlay/:channel/:token',
        component: () => import('../pages/overlay/Overlay.vue'),
    },
    {
        path: '/admin',
        component: () => import('../layouts/AdminLayout.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
        children: [
            { path: '', component: () => import('../pages/admin/AdminDashboard.vue') },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    if (!to.meta.requiresAuth) return true;

    const { useAuthStore } = await import('../stores/auth');
    const auth = useAuthStore();

    if (!auth.user) {
        await auth.fetchUser();
    }

    if (!auth.user) {
        window.location.href = '/auth/twitch/redirect';
        return false;
    }

    if (to.meta.requiresAdmin && !auth.isAdmin) {
        return { path: '/avatar' };
    }
});

export default router;
