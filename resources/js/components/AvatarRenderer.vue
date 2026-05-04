<template>
    <div
        class="relative overflow-hidden"
        :style="{ width: size + 'px', height: size * 2 + 'px' }"
    >
        <template v-for="slot in renderOrder" :key="slot">
            <img
                v-if="avatar?.[slot]?.image_path"
                :src="avatar[slot].image_path"
                :alt="avatar[slot].name"
                class="absolute inset-0 w-full h-full object-contain pointer-events-none select-none"
                :style="{ zIndex: renderOrder.indexOf(slot) }"
                @error="onImgError($event)"
            />
        </template>

        <!-- Empty state -->
        <div
            v-if="isEmpty"
            class="absolute inset-0 flex items-center justify-center text-gray-600 text-xs"
        >
            No avatar
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    avatar: { type: Object, default: null },
    size: { type: Number, default: 80 },
});

// Render back-to-front
const renderOrder = [
    'back', 'effect',
    'base', 'skin',
    'shoes', 'pants', 'shirt',
    'mouth', 'eyes',
    'hair', 'hat', 'glasses',
    'accessory', 'pet', 'badge',
];

const isEmpty = computed(() =>
    !props.avatar || renderOrder.every(slot => !props.avatar[slot]?.image_path)
);

function onImgError(e) {
    e.target.style.display = 'none';
}
</script>
