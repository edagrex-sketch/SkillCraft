<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps<{
    activePlans: any[]
    stats: {
        completedTopics: number
        totalTopics: number
        totalStudyMinutes: number
        activePlansCount: number
    }
    recentSessions: any[]
}>()

const mounted = ref(false)
const loading = ref(true)

onMounted(() => {
    setTimeout(() => { mounted.value = true; loading.value = false }, 50)
})

function formatMinutes(minutes: number) {
    const h = Math.floor(minutes / 60)
    const m = minutes % 60
    return h > 0 ? `${h}h ${m}m` : `${m} min`
}

const statCards = [
    {
        key: 'activePlansCount',
        label: 'Planes activos',
        icon: 'book',
        value: (s: any) => s.activePlansCount,
    },
    {
        key: 'completedTopics',
        label: 'Temas completados',
        icon: 'check-circle',
        value: (s: any) => `${s.completedTopics} / ${s.totalTopics}`,
    },
    {
        key: 'totalStudyMinutes',
        label: 'Tiempo de estudio',
        icon: 'clock',
        value: (s: any) => formatMinutes(s.totalStudyMinutes),
    },
    {
        key: 'progress',
        label: 'Progreso general',
        icon: 'trending-up',
        value: (s: any) => s.totalTopics > 0 ? `${Math.round((s.completedTopics / s.totalTopics) * 100)}%` : '0%',
    },
]
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Panel de control</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Bienvenido de vuelta, {{ $page.props.auth.user.name }}
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-8">
            <div v-if="activePlans.length === 0" :class="['transition-all duration-700 ease-out-expo', mounted ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0']">
                <div class="relative overflow-hidden rounded-2xl bg-brand-600 px-10 py-12 shadow-xl">
                    <div class="pointer-events-none absolute inset-0 bg-dot opacity-[0.08]" />
                    <div class="pointer-events-none absolute -top-20 -right-20 h-48 w-48 rounded-full bg-white/[0.06] blur-3xl animate-float" />
                    <div class="pointer-events-none absolute -bottom-20 -left-20 h-48 w-48 rounded-full bg-white/[0.04] blur-3xl animate-float" style="animation-delay: -3s" />

                    <div class="relative text-center text-white">
                        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm animate-bounce-gentle">
                            <Icon name="book-open" :size="28" class="text-white" />
                        </div>
                        <h2 class="text-2xl font-bold">Comienza tu viaje de aprendizaje</h2>
                        <p class="mx-auto mt-2 max-w-md text-[15px] text-brand-100">Crea tu primer plan de estudio con IA y empieza a aprender hoy</p>
                        <div class="mt-7 flex justify-center gap-4">
                            <Link
                                :href="route('study-plans.create')"
                                class="group inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-lg transition-all duration-300 ease-out-expo hover:bg-brand-50 hover:shadow-xl active:scale-[0.97]"
                            >
                                Crear mi primer plan
                                <Icon name="arrow-right" :size="16" class="transition-transform duration-300 group-hover:translate-x-0.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div :class="['grid gap-5 sm:grid-cols-2 lg:grid-cols-4', 'transition-all duration-700 delay-100 ease-out-expo', mounted ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0']">
                <template v-if="loading">
                    <div v-for="i in 4" :key="'skeleton-'+i" class="rounded-xl border border-gray-200/60 bg-white/60 p-6 dark:border-gray-800/40 dark:bg-gray-900/40">
                        <div class="skeleton h-4 w-20 mb-4" />
                        <div class="skeleton h-8 w-16" />
                    </div>
                </template>
                <div
                    v-for="(card, i) in statCards"
                    :key="card.key"
                    class="rounded-xl border border-gray-200/60 bg-white/60 p-6 shadow-sm backdrop-blur-sm transition-all duration-500 ease-out-expo hover:-translate-y-0.5 hover:border-gray-200 hover:shadow-lg dark:border-gray-800/40 dark:bg-gray-900/40 dark:hover:border-gray-700/50"
                    :style="{ animationDelay: `${i * 100}ms` }"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ card.label }}</p>
                            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ card.value(stats) }}</p>
                        </div>
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600 transition-transform duration-300 group-hover:scale-110 dark:bg-brand-950 dark:text-brand-400">
                            <Icon :name="card.icon" :size="18" />
                        </div>
                    </div>
                </div>
            </div>

            <div :class="['grid gap-8 lg:grid-cols-2', 'transition-all duration-700 delay-200 ease-out-expo', mounted ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0']">
                <template v-if="loading">
                    <div v-for="i in 2" :key="'skeleton-card-'+i" class="rounded-xl border border-gray-200/60 bg-white/60 p-6 dark:border-gray-800/40 dark:bg-gray-900/40">
                        <div class="skeleton h-5 w-32 mb-5" />
                        <div v-for="j in 3" :key="'skeleton-item-'+j" class="skeleton h-12 w-full mb-3" />
                    </div>
                </template>
                <div v-if="!loading" class="rounded-xl border border-gray-200/60 bg-white/60 shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-800/40 dark:bg-gray-900/40">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 dark:border-gray-800/50">
                        <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white">Planes de estudio</h3>
                        <Link :href="route('study-plans.index')" class="group flex items-center gap-1 text-sm font-medium text-brand-600 transition-colors hover:text-brand-500 dark:text-brand-400">
                            Ver todos
                            <Icon name="chevron-right" :size="14" class="transition-transform duration-200 group-hover:translate-x-0.5" />
                        </Link>
                    </div>

                    <div class="p-6">
                        <div v-if="activePlans.length === 0" class="py-10 text-center">
                            <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                <Icon name="book" :size="20" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aun no tienes planes</p>
                            <Link :href="route('study-plans.create')" class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-500">
                                Crear un plan ahora
                                <Icon name="arrow-right" :size="14" />
                            </Link>
                        </div>

                        <div v-for="(plan, i) in activePlans" :key="plan.id" class="group mb-3 last:mb-0 animate-fade-in-up" :style="{ animationDelay: `${i * 80}ms` }">
                            <Link
                                :href="route('study-plans.show', plan.id)"
                                class="flex items-center justify-between rounded-xl border border-transparent px-4 py-3.5 transition-all duration-200 hover:border-gray-200/80 hover:bg-white/50 hover:shadow-sm dark:hover:border-gray-700/40 dark:hover:bg-gray-800/30"
                            >
                                <div class="flex items-center gap-3.5">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white shadow-sm transition-transform duration-200 group-hover:scale-105">
                                        {{ plan.title.charAt(0) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ plan.title }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ plan.weeks_count || 0 }} semanas &middot; {{ plan.sessions_count || 0 }} sesiones</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span :class="['badge',
                                        plan.status === 'active' ? 'badge-green' :
                                        plan.status === 'generating' ? 'badge-yellow' :
                                        'badge-gray'
                                    ]">{{ plan.status === 'generating' ? 'Generando...' : plan.status }}</span>
                                    <Icon name="chevron-right" :size="16" class="text-gray-300 transition-all duration-200 group-hover:text-gray-400 group-hover:translate-x-0.5 dark:text-gray-600" />
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-if="!loading" class="rounded-xl border border-gray-200/60 bg-white/60 shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-800/40 dark:bg-gray-900/40">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 dark:border-gray-800/50">
                        <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white">Sesiones recientes</h3>
                    </div>

                    <div class="p-6">
                        <div v-if="recentSessions.length === 0" class="py-10 text-center">
                            <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                <Icon name="clock" :size="20" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No hay sesiones aun</p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Empieza un plan para registrar sesiones</p>
                        </div>

                        <div v-for="(session, i) in recentSessions" :key="session.id" class="group mb-2 flex items-center gap-3.5 rounded-xl px-4 py-3 transition-all last:mb-0 hover:bg-white/50 dark:hover:bg-gray-800/30 animate-fade-in-up" :style="{ animationDelay: `${i * 80}ms` }">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600 transition-transform duration-200 group-hover:scale-105 dark:bg-brand-950 dark:text-brand-400">
                                <Icon name="book-open" :size="16" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                    {{ session.topic?.title || 'Sesion de estudio' }}
                                </p>
                                <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                    {{ session.study_plan?.title }} &middot; {{ formatMinutes(session.duration_minutes) }}
                                </p>
                            </div>
                            <div v-if="session.confidence_level" class="flex shrink-0 items-center gap-1 rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700 transition-all group-hover:bg-brand-100 dark:bg-brand-950 dark:text-brand-300 dark:group-hover:bg-brand-900/50">
                                <span>{{ session.confidence_level }}</span>
                                <span class="text-[10px] opacity-60">/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
