<template>
    <div class="overlay-stage" ref="stageRef">
        <div
            v-for="entry in avatars"
            :key="entry.id"
            class="avatar-slot"
            :ref="el => registerEl(el, entry.id)"
        >
            <div
                class="avatar-anim"
                :class="stateClass(entry.id)"
                :style="flipStyle(entry.id)"
            >
                <AvatarRenderer :avatar="entry" :size="avatarSize" />
            </div>
            <span v-if="showNames" class="name-tag">
                {{ entry.user?.twitch_display_name }}
            </span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import echo from '../../echo';
import AvatarRenderer from '../../components/AvatarRenderer.vue';

const route    = useRoute();
const stageRef = ref(null);
const avatars  = ref([]);

const avatarSize = 80;
const showNames  = true;

// --- Animation state ---
// positions / velocities (plain JS — updated every frame, no reactivity)
const pos = {};          // { [id]: { x, dx } }
// state (reactive — changes every few seconds, triggers CSS class swap)
const states = ref({});  // { [id]: { anim: 'walk'|'bounce'|'sway'|'wiggle', flipped: bool } }
// DOM element refs
const els = {};

const DANCE_TYPES = ['bounce', 'sway', 'wiggle'];
const WALK_SPEEDS = [38, 50, 60, 72]; // px/s

function rnd(min, max) { return min + Math.random() * (max - min); }
function pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; }

function initAnim(id) {
    const stageW = stageRef.value?.clientWidth || 1920;
    const speed  = pick(WALK_SPEEDS);
    const goLeft = Math.random() > 0.5;

    pos[id] = {
        x:  rnd(avatarSize, stageW - avatarSize * 2),
        dx: goLeft ? -speed : speed,
        nextSwitch: Date.now() + rnd(4000, 14000),
    };

    states.value[id] = {
        anim:    Math.random() > 0.35 ? 'walk' : pick(DANCE_TYPES),
        flipped: goLeft,
    };
}

function stateClass(id) {
    const s = states.value[id];
    return s ? `anim-${s.anim}` : 'anim-walk';
}

function flipStyle(id) {
    const s = states.value[id];
    return s?.flipped ? { transform: 'scaleX(-1)' } : {};
}

// --- Game loop ---
let raf = null;
let lastTs = 0;

function tick(ts) {
    const dt = Math.min((ts - lastTs) / 1000, 0.1);
    lastTs = ts;

    const stageW = stageRef.value?.clientWidth || 1920;
    const now    = Date.now();

    for (const id in pos) {
        const p = pos[id];
        const el = els[id];
        if (!el) continue;

        // State switch
        if (now >= p.nextSwitch) {
            const s = states.value[id];
            if (s.anim === 'walk') {
                s.anim = pick(DANCE_TYPES);
            } else {
                s.anim = 'walk';
            }
            p.nextSwitch = now + rnd(4000, 14000);
        }

        const s = states.value[id];

        // Move only when walking
        if (s.anim === 'walk') {
            p.x += p.dx * dt;

            if (p.x <= 0) {
                p.x = 0;
                p.dx = Math.abs(p.dx);
                s.flipped = false;
            } else if (p.x >= stageW - avatarSize) {
                p.x = stageW - avatarSize;
                p.dx = -Math.abs(p.dx);
                s.flipped = true;
            }
        }

        // Direct DOM update for position (no Vue re-render per frame)
        el.style.left = `${Math.round(p.x)}px`;
    }

    raf = requestAnimationFrame(tick);
}

// --- Avatar list management ---
function registerEl(el, id) {
    if (el) {
        els[id] = el;
        if (!pos[id]) initAnim(id);
    } else {
        delete els[id];
    }
}

function mergeAvatar(avatar) {
    const idx = avatars.value.findIndex(a => a.id === avatar.id);
    if (idx >= 0) {
        avatars.value[idx] = avatar;
    } else {
        avatars.value.push(avatar);
        // initAnim called by registerEl when el mounts
    }
}

// --- Lifecycle ---
let pollTimer = null;

onMounted(async () => {
    await fetchState();

    raf = requestAnimationFrame(tick);

    const ch = echo.channel(`overlay.${route.params.channel}`);
    console.log('[Overlay] subscribed to channel:', `overlay.${route.params.channel}`);
    ch.listen('.AvatarUpdated', ({ avatar }) => {
        console.log('[Overlay] AvatarUpdated received:', avatar?.id);
        mergeAvatar(avatar);
    });

    pollTimer = setInterval(fetchState, 5_000);
});

onUnmounted(() => {
    cancelAnimationFrame(raf);
    clearInterval(pollTimer);
    echo.leave(`overlay.${route.params.channel}`);
});

async function fetchState() {
    try {
        const res = await fetch(`/api/overlay/${route.params.channel}/${route.params.token}/state`);
        if (res.ok) {
            const data = await res.json();
            data.forEach(mergeAvatar);
        }
    } catch {}
}
</script>

<style scoped>
.overlay-stage {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 240px;
    background: transparent;
    pointer-events: none;
    overflow: hidden;
}

.avatar-slot {
    position: absolute;
    bottom: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    /* no CSS transition — position driven by rAF */
}

.avatar-anim {
    transform-origin: center bottom;
    transition: transform 0.15s ease; /* smooth flip */
}

.name-tag {
    color: white;
    font-size: 10px;
    font-weight: 600;
    background: rgba(0,0,0,0.45);
    border-radius: 4px;
    padding: 1px 6px;
    margin-top: 2px;
    backdrop-filter: blur(4px);
    white-space: nowrap;
}

/* ── Walking ── */
.anim-walk {
    animation: walk-bob 0.38s ease-in-out infinite;
}

/* ── Dance: bounce ── */
.anim-bounce {
    animation: dance-bounce 0.46s ease-in-out infinite;
}

/* ── Dance: sway ── */
.anim-sway {
    animation: dance-sway 0.6s ease-in-out infinite;
}

/* ── Dance: wiggle ── */
.anim-wiggle {
    animation: dance-wiggle 0.35s ease-in-out infinite;
}

@keyframes walk-bob {
    0%, 100% { transform: translateY(0px); }
    50%       { transform: translateY(-4px); }
}

@keyframes dance-bounce {
    0%, 100% { transform: translateY(0px) scaleX(1); }
    25%       { transform: translateY(-14px) scaleX(0.95) rotate(-4deg); }
    75%       { transform: translateY(-14px) scaleX(0.95) rotate(4deg); }
}

@keyframes dance-sway {
    0%, 100% { transform: rotate(0deg) translateY(0); }
    20%       { transform: rotate(-12deg) translateY(-3px); }
    50%       { transform: rotate(0deg) translateY(-5px); }
    80%       { transform: rotate(12deg) translateY(-3px); }
}

@keyframes dance-wiggle {
    0%, 100% { transform: scaleX(1) scaleY(1); }
    15%       { transform: scaleX(0.88) scaleY(1.08) rotate(-6deg); }
    35%       { transform: scaleX(1.08) scaleY(0.94) rotate(5deg); }
    55%       { transform: scaleX(0.92) scaleY(1.06) rotate(-4deg); }
    75%       { transform: scaleX(1.05) scaleY(0.96) rotate(3deg); }
}
</style>
