<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import Icon from '@/Components/Icon.vue'

const showingUserMenu = ref(false)
const showingMobileMenu = ref(false)

const navLinks = [
    { name: 'Dashboard', route: 'dashboard', icon: 'dashboard' },
    { name: 'Mis Planes', route: 'study-plans.index', icon: 'book' },
]
</script>

<template>
    <div class="min-h-screen bg-[#f8f8fc] dark:bg-[#0a0a14]">
        <nav class="fixed top-0 z-50 w-full border-b border-gray-200/50 bg-white/60 backdrop-blur-2xl dark:border-gray-800/30 dark:bg-[#0a0a14]/60">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <Link :href="route('dashboard')" class="flex items-center gap-2.5 transition-opacity hover:opacity-80">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white shadow-sm shadow-brand-600/20">
                            S
                        </div>
                        <span class="text-[15px] font-semibold tracking-tight text-gray-900 dark:text-white">SkillCraft</span>
                    </Link>

                    <div class="hidden items-center sm:flex">
                        <div class="flex rounded-lg bg-gray-100/50 p-0.5 dark:bg-gray-800/50">
                            <Link
                                v-for="link in navLinks"
                                :key="link.name"
                                :href="route(link.route)"
                                :class="[
                                    'relative flex items-center gap-1.5 rounded-[7px] px-3.5 py-1.5 text-sm font-medium transition-all duration-200',
                                    route().current(link.route)
                                        ? 'bg-white text-brand-700 shadow-sm dark:bg-gray-800 dark:text-brand-300'
                                        : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
                                ]"
                            >
                                <Icon :name="link.icon" :size="16" />
                                {{ link.name }}
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="relative">
                        <button
                            @click="showingUserMenu = !showingUserMenu"
                            class="flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-sm font-medium text-gray-600 transition-all hover:bg-gray-100/80 dark:text-gray-400 dark:hover:bg-gray-800/80"
                        >
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-600 text-xs font-semibold text-white ring-2 ring-white dark:ring-gray-900">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                            <span class="hidden sm:inline">{{ $page.props.auth.user.name }}</span>
                            <Icon name="chevron-down" :size="14" class="text-gray-400 transition-transform duration-200" :class="showingUserMenu ? 'rotate-180' : ''" />
                        </button>

                        <div
                            v-show="showingUserMenu"
                            @click.away="showingUserMenu = false"
                            class="absolute right-0 mt-2 w-56 animate-scale-in rounded-xl border border-gray-200/60 bg-white p-1.5 shadow-xl shadow-gray-900/10 dark:border-gray-800/40 dark:bg-gray-900 origin-top-right"
                        >
                            <div class="border-b border-gray-100 px-3 py-2.5 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.auth.user.name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $page.props.auth.user.email }}</p>
                            </div>
                            <div class="mt-1 space-y-0.5">
                                <Link
                                    :href="route('profile.edit')"
                                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 transition-colors hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800"
                                    @click="showingUserMenu = false"
                                >
                                    <Icon name="user" :size="16" />
                                    Perfil
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950"
                                    @click="showingUserMenu = false"
                                >
                                    <Icon name="logout" :size="16" />
                                    Cerrar sesion
                                </Link>
                            </div>
                        </div>
                    </div>

                    <button
                        @click="showingMobileMenu = !showingMobileMenu"
                        class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100/80 sm:hidden dark:text-gray-400 dark:hover:bg-gray-800/80"
                    >
                        <Icon v-if="!showingMobileMenu" name="menu" :size="20" />
                        <Icon v-else name="x" :size="20" />
                    </button>
                </div>
            </div>

            <div
                v-show="showingMobileMenu"
                class="border-t border-gray-200/50 px-4 py-3 sm:hidden dark:border-gray-800/30"
            >
                <div class="space-y-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="route(link.route)"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                            route().current(link.route)
                                ? 'bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300'
                                : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800'
                        ]"
                        @click="showingMobileMenu = false"
                    >
                        <Icon :name="link.icon" :size="16" />
                        {{ link.name }}
                    </Link>
                </div>
            </div>
        </nav>

        <header v-if="$slots.header" class="relative pt-16">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="animate-fade-in-down">
                    <slot name="header" />
                </div>
            </div>
        </header>

        <main :class="['mx-auto max-w-7xl px-4 sm:px-6 lg:px-8', $slots.header ? '' : 'pt-24']">
            <div class="py-6">
                <div class="animate-fade-in-up">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
