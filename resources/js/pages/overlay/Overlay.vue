<template>
    <div class="overlay-stage">
        <div
            v-for="avatarData in avatars"
            :key="avatarData.user_id"
            class="avatar-slot"
            :style="{ left: avatarData.position + 'px' }"
        >
            <!-- AvatarRenderer will go here -->
            <span class="text-xs text-white">{{ avatarData.username }}</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const avatars = ref([]);
let ws = null;

onMounted(() => {
    fetchState();
    connectWebSocket();
});

onUnmounted(() => {
    ws?.close();
});

async function fetchState() {
    const res = await fetch(`/api/overlay/${route.params.channel}/${route.params.token}/state`);
    if (res.ok) {
        avatars.value = await res.json();
    }
}

function connectWebSocket() {
    // Laravel Echo / Reverb connection will be wired up here
}
</script>

<style scoped>
.overlay-stage {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 120px;
    background: transparent;
    pointer-events: none;
}
.avatar-slot {
    position: absolute;
    bottom: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}
</style>
