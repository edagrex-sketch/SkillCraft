<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps<{
    plans: any[]
    filters: {
        search?: string
        status?: string
        level?: string
    }
}>()

const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')
const levelFilter = ref(props.filters?.level || '')

function applyFilters() {
    router.get(route('study-plans.index'), {
        search: search.value || undefined,
        status: statusFilter.value || undefined,
        level: levelFilter.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function clearFilters() {
    search.value = ''
    statusFilter.value = ''
    levelFilter.value = ''
    router.get(route('study-plans.index'), {}, { preserveState: true, replace: true })
}

const statusStyles: Record<string, string> = {
    active: 'badge-green',
    generating: 'badge-yellow',
    paused: 'badge-gray',
    completed: 'badge-blue',
    failed: 'badge-red',
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

const hasActiveFilters = () => search.value || statusFilter.value || levelFilter.value

function submitRetry(planId: number) {
    router.post(route('study-plans.retry', planId))
}
</script>

<template>
    <Head title="Mis Planes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Mis Planes de Estudio</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ plans.length }} {{ plans.length === 1 ? 'plan activo' : 'planes activos' }}
                    </p>
                </div>
                <Link
                    :href="route('study-plans.create')"
                    class="btn-primary px-5 py-2.5 text-sm shrink-0 group"
                >
                    <Icon name="plus" :size="16" class="transition-transform duration-200 group-hover:rotate-90" />
                    Nuevo Plan
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <div class="flex flex-wrap items-center gap-3 animate-fade-in-down">
                <div class="relative flex-1 min-w-[200px] max-w-xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <Icon name="search" :size="16" class="text-gray-400" />
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar planes..."
                        class="w-full rounded-xl border border-gray-200 bg-white/60 py-2.5 pl-9 pr-4 text-sm text-gray-900 transition-all placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white dark:placeholder:text-gray-500"
                        @input="applyFilters"
                    />
                </div>

                <select
                    v-model="statusFilter"
                    class="rounded-xl border border-gray-200 bg-white/60 px-3.5 py-2.5 text-sm text-gray-700 transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-300"
                    @change="applyFilters"
                >
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="generating">Generando</option>
                    <option value="completed">Completado</option>
                    <option value="failed">Fallido</option>
                </select>

                <select
                    v-model="levelFilter"
                    class="rounded-xl border border-gray-200 bg-white/60 px-3.5 py-2.5 text-sm text-gray-700 transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-300"
                    @change="applyFilters"
                >
                    <option value="">Todos los niveles</option>
                    <option value="beginner">Principiante</option>
                    <option value="intermediate">Intermedio</option>
                    <option value="advanced">Avanzado</option>
                </select>

                <button
                    v-if="hasActiveFilters()"
                    @click="clearFilters"
                    class="btn-ghost text-sm animate-scale-in"
                >
                    <Icon name="x" :size="14" />
                    Limpiar filtros
                </button>
            </div>

            <div v-if="plans.length === 0" class="flex items-center justify-center py-20">
                <div class="text-center animate-fade-in-up">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 dark:bg-brand-950">
                        <Icon name="book" :size="28" class="text-brand-600 dark:text-brand-400" />
                    </div>
                    <h3 class="mt-5 text-xl font-semibold text-gray-900 dark:text-white">
                        {{ hasActiveFilters() ? 'Sin resultados' : 'Aun no tienes planes' }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ hasActiveFilters() ? 'Prueba con otros filtros' : 'Crea tu primer plan de estudio con IA' }}
                    </p>
                    <Link
                        v-if="!hasActiveFilters()"
                        :href="route('study-plans.create')"
                        class="btn-primary mx-auto mt-8 inline-flex px-6 py-3 text-sm"
                    >
                        <Icon name="sparkle" :size="16" />
                        Crear mi primer plan
                    </Link>
                    <button
                        v-else
                        @click="clearFilters"
                        class="btn-secondary mx-auto mt-8 inline-flex px-6 py-3 text-sm"
                    >
                        <Icon name="x" :size="16" />
                        Limpiar filtros
                    </button>
                </div>
            </div>

            <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(plan, i) in plans"
                    :key="plan.id"
                    class="animate-fade-in-up"
                    :style="{ animationDelay: `${i * 80}ms` }"
                >
                    <Link
                        :href="route('study-plans.show', plan.id)"
                        class="relative block overflow-hidden rounded-xl border border-gray-200/60 bg-white/60 p-6 shadow-sm transition-all duration-500 ease-out-expo hover:-translate-y-1 hover:border-gray-200 hover:shadow-xl dark:border-gray-800/40 dark:bg-gray-900/40 dark:hover:border-gray-700/50 shine"
                    >
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-brand-500/[0.02] to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100" />

                        <div class="relative">
                            <div class="flex items-start justify-between">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-600 text-base font-bold text-white shadow-sm transition-transform duration-200 group-hover:scale-105">
                                    {{ plan.title.charAt(0).toUpperCase() }}
                                </div>
                                <span
                                    :class="['badge', statusStyles[plan.status] || 'badge-gray']"
                                >
                                    <Icon v-if="plan.status === 'generating'" name="loader" :size="10" class="animate-spin" />
                                    <Icon v-else-if="plan.status === 'failed'" name="alert-circle" :size="10" />
                                    <Icon v-else-if="plan.status === 'completed'" name="check" :size="10" />
                                    {{ plan.status === 'generating' ? 'Generando...' : plan.status === 'failed' ? 'Fallido' : plan.status }}
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
                                        class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600 transition-all duration-1000 ease-out-expo"
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

                            <div v-if="plan.status === 'failed'" class="mt-4">
                                <div
                                    class="flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-600 dark:bg-red-950/30 dark:text-red-400"
                                    @click.prevent
                                >
                                    <Icon name="alert-circle" :size="12" />
                                    <span class="flex-1">Error al generar</span>
                                    <button
                                        @click.prevent="submitRetry(plan.id)"
                                        class="font-medium hover:underline transition-colors"
                                    >
                                        Reintentar
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800/50">
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
        </div>
    </AuthenticatedLayout>
</template>
