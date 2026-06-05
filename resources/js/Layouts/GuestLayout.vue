<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Icon from '@/Components/Icon.vue'
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center justify-center bg-[#f8f8fc] px-4 dark:bg-[#0a0a14]">
        <div class="pointer-events-none fixed inset-0 bg-dot opacity-60" />

        <div class="pointer-events-none fixed -top-40 -right-40 h-[400px] w-[400px] rounded-full bg-brand-500/[0.06] blur-[100px]" />
        <div class="pointer-events-none fixed -bottom-40 -left-40 h-[400px] w-[400px] rounded-full bg-purple-500/[0.06] blur-[100px]" />

        <div class="relative w-full max-w-md animate-fade-in-up">
            <div class="mb-8 text-center">
                <Link href="/" class="inline-flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-lg font-bold text-white shadow-lg shadow-brand-600/30">
                        S
                    </div>
                    <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">SkillCraft</span>
                </Link>
            </div>

            <div class="rounded-2xl border border-gray-200/60 bg-white/70 p-8 shadow-xl shadow-gray-900/5 backdrop-blur-xl dark:border-gray-800/40 dark:bg-gray-900/70">
                <slot />
            </div>
        </div>
    </div>
</template>
