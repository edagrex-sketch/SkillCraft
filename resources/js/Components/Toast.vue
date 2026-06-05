<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

export type ToastType = 'success' | 'error' | 'info' | 'warning'

interface Toast {
    id: number
    message: string
    type: ToastType
    leaving: boolean
}

const toasts = ref<Toast[]>([])
let nextId = 0

function add(message: string, type: ToastType = 'info', duration = 4000) {
    const id = nextId++
    toasts.value.push({ id, message, type, leaving: false })
    setTimeout(() => remove(id), duration)
}

function remove(id: number) {
    const toast = toasts.value.find(t => t.id === id)
    if (toast) toast.leaving = true
    setTimeout(() => {
        toasts.value = toasts.value.filter(t => t.id !== id)
    }, 250)
}

const iconMap: Record<ToastType, string> = {
    success: 'check-circle',
    error: 'alert-circle',
    info: 'info',
    warning: 'alert-circle',
}

const colorMap: Record<ToastType, string> = {
    success: 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-950/40 dark:border-emerald-900/50 dark:text-emerald-200',
    error: 'bg-red-50 border-red-200 text-red-800 dark:bg-red-950/40 dark:border-red-900/50 dark:text-red-200',
    info: 'bg-brand-50 border-brand-200 text-brand-800 dark:bg-brand-950/40 dark:border-brand-900/50 dark:text-brand-200',
    warning: 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-950/40 dark:border-amber-900/50 dark:text-amber-200',
}

function handleFlash() {
    const flash = (window as any).__inertia_flash
    if (flash?.success) add(flash.success, 'success')
    if (flash?.error) add(flash.error, 'error')
    if (flash?.info) add(flash.info, 'info')
    if (flash?.warning) add(flash.warning, 'warning')
}

onMounted(() => {
    handleFlash()
    document.addEventListener('inertia:success', handleFlash)
})

onUnmounted(() => {
    document.removeEventListener('inertia:success', handleFlash)
})

defineExpose({ add, remove })
</script>

<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 max-w-sm w-full pointer-events-none">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                :class="['pointer-events-auto flex items-start gap-3 rounded-xl border p-4 shadow-lg shadow-gray-900/10 backdrop-blur-xl transition-all duration-300',
                    toast.leaving ? 'opacity-0 -translate-y-2 scale-95' : 'opacity-100 translate-y-0 scale-100',
                    colorMap[toast.type]
                ]"
            >
                <div class="flex h-5 w-5 items-center justify-center shrink-0 mt-0.5">
                    <svg v-if="toast.type === 'success'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    <svg v-else-if="toast.type === 'error'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="16" x2="12" y2="12" />
                        <line x1="12" y1="8" x2="12.01" y2="8" />
                    </svg>
                </div>
                <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
                <button @click="remove(toast.id)" class="shrink-0 opacity-60 hover:opacity-100 transition-opacity">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
        </div>
    </Teleport>
</template>
