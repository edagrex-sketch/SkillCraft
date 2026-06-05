<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps<{
    plans: any[]
}>()

const statusStyles: Record<string, string> = {
    active: 'badge-green',
    generating: 'badge-yellow',
    paused: 'badge-gray',
    completed: 'badge-blue',
}

const levelIcons: Record<string, string> = {
    beginner: 'target',
    intermediate: 'trending-up',
    advanced: 'award',
}

function progressPercent(plan: any) {
    if (!plan.topics_count || plan.topics_count === 0) return 0
    return Math.round((plan.completed_topics_count / plan.topics_count) * 100)
}
</script>

<template>
    <Head title="Mis Planes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Mis Planes de Estudio</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ plans.length }} {{ plans.length === 1 ? 'plan activo' : 'planes activos' }}
                    </p>
                </div>
                <Link
                    :href="route('study-plans.create')"
                    class="btn-primary px-5 py-2.5 text-sm"
                >
                    <Icon name="plus" :size="16" />
                    Nuevo Plan
                </Link>
            </div>
        </template>

        <div v-if="plans.length === 0" class="flex items-center justify-center py-20">
            <div class="text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 dark:bg-brand-950">
                    <Icon name="book" :size="28" class="text-brand-600 dark:text-brand-400" />
                </div>
                <h3 class="mt-5 text-xl font-semibold text-gray-900 dark:text-white">Aun no tienes planes</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Crea tu primer plan de estudio con IA y empieza a aprender</p>
                <Link
                    :href="route('study-plans.create')"
                    class="btn-primary mx-auto mt-8 inline-flex px-6 py-3 text-sm"
                >
                    <Icon name="sparkle" :size="16" />
                    Crear mi primer plan
                </Link>
            </div>
        </div>

        <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="plan in plans"
                :key="plan.id"
                class="group"
            >
                <Link
                    :href="route('study-plans.show', plan.id)"
                    class="relative block overflow-hidden rounded-xl border border-gray-200/60 bg-white/60 p-6 shadow-sm transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-0.5 hover:border-gray-200 hover:shadow-lg dark:border-gray-800/40 dark:bg-gray-900/40 dark:hover:border-gray-700/50"
                >
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-brand-500/[0.02] to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100" />

                    <div class="relative">
                        <div class="flex items-start justify-between">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-600 text-base font-bold text-white shadow-sm">
                                {{ plan.title.charAt(0).toUpperCase() }}
                            </div>
                            <span
                                :class="['badge', statusStyles[plan.status] || 'badge-gray']"
                            >
                                <Icon v-if="plan.status === 'generating'" name="loader" :size="10" class="animate-spin" />
                                {{ plan.status === 'generating' ? 'Generando...' : plan.status }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-[15px] font-semibold text-gray-900 transition-colors duration-200 group-hover:text-brand-600 dark:text-white dark:group-hover:text-brand-400">
                                {{ plan.title }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ plan.skill }}
                            </p>
                        </div>

                        <div v-if="plan.topics_count > 0" class="mt-5">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>Progreso</span>
                                <span>{{ progressPercent(plan) }}%</span>
                            </div>
                            <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600 transition-all duration-500"
                                    :style="{ width: progressPercent(plan) + '%' }"
                                />
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span :class="['badge', statusStyles[plan.status] || 'badge-gray']" class="!gap-1">
                                <Icon :name="levelIcons[plan.level] || 'target'" :size="10" />
                                <span class="capitalize">{{ plan.level }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <Icon name="calendar" :size="12" />
                                {{ plan.duration_weeks }} sem
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <Icon name="clock" :size="12" />
                                {{ plan.hours_per_week }}h/sem
                            </span>
                        </div>

                        <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800/50">
                            <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                                <span>Creado {{ new Date(plan.created_at).toLocaleDateString('es', { day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
                                <span class="inline-flex items-center gap-0.5 text-brand-600 opacity-0 transition-all duration-200 group-hover:opacity-100 dark:text-brand-400">
                                    Ver plan
                                    <Icon name="arrow-right" :size="12" />
                                </span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
