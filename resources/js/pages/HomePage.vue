<template>
    <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden bg-grid">
        <!-- Ambient glow blobs -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-1/4 left-1/3 w-[400px] h-[400px] bg-cyan-500/8 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center gap-8 text-center px-4">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-1.5 text-xs text-cyan-400 font-medium tracking-wider uppercase">
                <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full animate-pulse"></span>
                Twitch Avatar Overlay
            </div>

            <!-- Hero heading -->
            <h1 class="text-6xl sm:text-7xl font-black tracking-tight">
                <span class="text-gradient">Stream</span><span class="text-white">Avatars</span>
            </h1>

            <p class="text-gray-400 text-lg max-w-md leading-relaxed">
                Give your viewers an avatar on stream. The longer they watch, the more they unlock.
            </p>

            <!-- CTA -->
            <a
                href="/auth/twitch/redirect"
                class="btn-primary inline-flex items-center gap-3 px-8 py-3.5 rounded-xl font-semibold text-white text-base"
            >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/>
                </svg>
                Connect with Twitch
            </a>

            <!-- Feature pills -->
            <div class="flex flex-wrap gap-3 justify-center mt-4">
                <span v-for="feat in features" :key="feat" class="glass rounded-full px-3 py-1 text-xs text-gray-400">
                    {{ feat }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
const features = [
    'Layer-based avatars',
    'Realtime OBS overlay',
    'Watchtime rewards',
    'Twitch EventSub',
    'Chat commands',
    'Admin dashboard',
];
</script>
