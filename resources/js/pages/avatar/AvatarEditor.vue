<template>
    <div class="flex gap-6">
        <div class="flex-1 space-y-6">
            <div>
                <h1 class="text-3xl font-black text-gradient">Avatar Editor</h1>
                <p class="text-gray-500 text-sm mt-1">Choose items for each slot.</p>
            </div>
            <div class="glass rounded-2xl p-6">
                <p class="text-gray-500 text-sm">Item slots coming soon.</p>
            </div>
        </div>

        <!-- Live preview -->
        <div class="w-60 shrink-0 space-y-4">
            <div class="glass rounded-2xl p-4">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Preview</p>
                <div class="w-full aspect-square rounded-xl bg-white/5 border border-white/10 flex items-center justify-center">
                    <span class="text-gray-600 text-sm">Preview</span>
                </div>
            </div>
            <button class="btn-primary w-full py-2.5 rounded-xl text-sm font-semibold text-white">
                Save Avatar
            </button>
        </div>
    </div>
</template>
