<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-black text-gradient">Settings</h1>
            <p class="text-gray-500 text-sm mt-1">Control your avatar visibility.</p>
        </div>
        <div class="glass rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white">Show on overlay</p>
                    <p class="text-xs text-gray-500 mt-0.5">Appear on stream when active</p>
                </div>
                <button class="w-11 h-6 rounded-full bg-blue-600 relative transition">
                    <span class="absolute right-1 top-1 w-4 h-4 bg-white rounded-full"></span>
                </button>
            </div>
        </div>
    </div>
</template>
