<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Icon from '@/Components/Icon.vue'
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-[#f8f8fc] px-4 dark:bg-[#0a0a14]">
        <div class="pointer-events-none fixed inset-0 bg-dot opacity-[0.035] dark:opacity-[0.06]" />

        <div class="pointer-events-none fixed -top-48 -right-48 h-[500px] w-[500px] animate-float rounded-full bg-brand-500/[0.07] blur-[120px] dark:bg-brand-500/[0.08]" style="animation-delay: -2s" />
        <div class="pointer-events-none fixed -bottom-48 -left-48 h-[500px] w-[500px] animate-float rounded-full bg-purple-500/[0.05] blur-[120px] dark:bg-purple-500/[0.06]" style="animation-delay: -5s" />
        <div class="pointer-events-none fixed -top-20 left-1/3 h-[300px] w-[300px] animate-float rounded-full bg-emerald-500/[0.04] blur-[80px] dark:bg-emerald-500/[0.05]" style="animation-delay: -8s" />

        <div class="pointer-events-none fixed left-10 top-1/4 opacity-20 dark:opacity-10">
            <svg width="60" height="60" viewBox="0 0 60 60" fill="none" class="animate-float" style="animation-delay: -3s">
                <rect x="5" y="5" width="50" height="50" rx="12" stroke="currentColor" stroke-width="1.5" class="text-brand-600" />
                <path d="M20 30 L25 35 L40 20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-brand-500" />
            </svg>
        </div>
        <div class="pointer-events-none fixed right-16 bottom-1/3 opacity-20 dark:opacity-10">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" class="animate-float" style="animation-delay: -6s">
                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="1.5" class="text-purple-600" />
                <circle cx="24" cy="24" r="8" fill="currentColor" class="text-purple-500" />
            </svg>
        </div>
        <div class="pointer-events-none fixed right-1/4 top-1/3 opacity-15 dark:opacity-8">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" class="animate-float" style="animation-delay: -9s">
                <path d="M20 2 L25 15 L38 15 L28 24 L31 38 L20 30 L9 38 L12 24 L2 15 L15 15 Z" stroke="currentColor" stroke-width="1.2" class="text-amber-500" />
            </svg>
        </div>

        <div class="relative w-full max-w-md">
            <div class="mb-8 text-center animate-fade-in-down">
                <Link href="/" class="group inline-flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-600 text-lg font-bold text-white shadow-lg shadow-brand-600/30 transition-all duration-300 group-hover:scale-105 group-hover:shadow-xl group-hover:shadow-brand-600/40">
                        S
                    </div>
                    <div class="text-left">
                        <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">SkillCraft</span>
                        <p class="text-[11px] font-medium uppercase tracking-[0.15em] text-brand-600 dark:text-brand-400">Aprende haciendo</p>
                    </div>
                </Link>
            </div>

            <div class="animate-fade-in-up">
                <div class="relative overflow-hidden rounded-2xl border border-gray-200/60 bg-white/70 p-8 shadow-xl shadow-gray-900/5 backdrop-blur-2xl transition-all duration-500 dark:border-gray-800/40 dark:bg-gray-900/70">
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-brand-500/[0.02] to-transparent opacity-60" />
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
