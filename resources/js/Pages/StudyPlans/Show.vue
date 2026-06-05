<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Icon from '@/Components/Icon.vue'
import { fireConfetti } from '@/Utils/confetti'

const props = defineProps<{
    plan: any
}>()

const activeWeek = ref<number | null>(null)
const sessionTopic = ref<number | null>(null)

function toggleWeek(weekId: number) {
    activeWeek.value = activeWeek.value === weekId ? null : weekId
}

const mounted = ref(false)

onMounted(() => {
    mounted.value = true
    if (progressPercent.value === 100) {
        setTimeout(() => fireConfetti(), 600)
    }
})

function toggleTopic(topicId: number) {
    router.patch(route('topics.complete', topicId), {}, {
        onSuccess: () => {
            setTimeout(() => fireConfetti(), 400)
        }
    })
}

function completedTopicsCount(week: any) {
    return week.topics.filter((t: any) => t.is_completed).length
}

const progressPercent = computed(() => {
    const total = props.plan.weeks.reduce((acc: number, w: any) => acc + w.topics.length, 0)
    const completed = props.plan.weeks.reduce((acc: number, w: any) => acc + w.topics.filter((t: any) => t.is_completed).length, 0)
    return total > 0 ? Math.round((completed / total) * 100) : 0
})

const difficultyColors: Record<string, string> = {
    beginner: 'badge-green',
    intermediate: 'badge-yellow',
    advanced: 'badge-red',
}

const contentTypeIcons: Record<string, string> = {
    exercise: 'code',
    project: 'layers',
    video: 'play',
    article: 'file-text',
    quiz: 'brain',
}

const contentTypeLabels: Record<string, string> = {
    exercise: 'Ejercicio',
    project: 'Proyecto',
    video: 'Video',
    article: 'Articulo',
    quiz: 'Cuestionario',
}

function scrollToWeek(weekId: number) {
    activeWeek.value = weekId
    setTimeout(() => {
        document.getElementById(`week-${weekId}`)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }, 100)
}

const totalSessions = computed(() => {
    return props.plan.weeks.reduce((acc: number, w: any) => {
        return acc + w.topics.reduce((a: number, t: any) => a + (t.sessions?.length || 0), 0)
    }, 0)
})

const totalMinutes = computed(() => {
    return props.plan.weeks.reduce((acc: number, w: any) => {
        return acc + w.topics.reduce((a: number, t: any) => a + (t.sessions?.reduce((s: number, sess: any) => s + sess.duration_minutes, 0) || 0), 0)
    }, 0)
})

// Session form
const sessionForm = useForm({
    duration_minutes: 30,
    confidence_level: 3,
    notes: '',
})

function openSessionForm(topicId: number) {
    sessionTopic.value = topicId
    sessionForm.reset()
    sessionForm.duration_minutes = 30
    sessionForm.confidence_level = 3
}

function closeSessionForm() {
    sessionTopic.value = null
}

function submitSession(topicId: number) {
    sessionForm.post(route('topics.sessions.store', topicId), {
        preserveScroll: true,
        onSuccess: () => closeSessionForm(),
    })
}

function deleteSession(sessionId: number) {
    router.delete(route('sessions.destroy', sessionId), {
        preserveScroll: true,
    })
}

function formatMinutes(minutes: number) {
    if (!minutes) return '0 min'
    const h = Math.floor(minutes / 60)
    const m = minutes % 60
    return h > 0 ? `${h}h ${m}m` : `${m} min`
}

function weekMinutes(week: any) {
    return week.topics.reduce((a: number, t: any) => a + (t.sessions?.reduce((s: number, sess: any) => s + sess.duration_minutes, 0) || 0), 0)
}
</script>

<template>
    <Head :title="plan.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Link :href="route('study-plans.index')" class="group inline-flex items-center gap-1 text-sm font-medium text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <Icon name="arrow-left" :size="14" class="transition-transform duration-200 group-hover:-translate-x-0.5" />
                        Mis Planes
                    </Link>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ plan.title }}</h1>
                    <p v-if="plan.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ plan.description }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span :class="['badge',
                        plan.level === 'beginner' ? 'badge-green' :
                        plan.level === 'intermediate' ? 'badge-yellow' :
                        'badge-red'
                    ]">
                        <Icon :name="plan.level === 'advanced' ? 'award' : plan.level === 'intermediate' ? 'trending-up' : 'target'" :size="12" />
                        {{ plan.level }}
                    </span>
                    <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                        <Icon name="calendar" :size="14" />
                        {{ plan.duration_weeks }} semanas
                    </span>
                    <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                        <Icon name="clock" :size="14" />
                        {{ plan.hours_per_week }}h/sem
                    </span>
                    <Link :href="route('study-plans.edit', plan.id)" class="btn-ghost">
                        <Icon name="edit" :size="16" />
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-8">
            <div :class="['rounded-xl border border-gray-200/60 bg-white/60 p-6 shadow-sm dark:border-gray-800/40 dark:bg-gray-900/40', 'transition-all duration-700 ease-out-expo', mounted ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0']">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 dark:bg-brand-950">
                            <Icon name="pie-chart" :size="26" class="text-brand-600 dark:text-brand-400" />
                        </div>
                        <div>
                            <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white">Progreso general</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ progressPercent }}% completado &middot;
                                {{ plan.weeks.reduce((a: number, w: any) => a + completedTopicsCount(w), 0) }} de
                                {{ plan.weeks.reduce((a: number, w: any) => a + w.topics.length, 0) }} temas
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="hidden text-right sm:block">
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ totalSessions }} sesiones</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ formatMinutes(totalMinutes) }} total</p>
                        </div>
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-950">
                            <span class="text-lg font-bold text-brand-600 dark:text-brand-400">{{ progressPercent }}%</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                    <div
                        class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600 transition-all duration-1000 ease-[cubic-bezier(0.16,1,0.3,1)]"
                        :style="{ width: progressPercent + '%' }"
                    />
                </div>

                <div v-if="plan.weeks.length > 1" class="mt-5 flex flex-wrap gap-2">
                    <button
                        v-for="week in plan.weeks"
                        :key="week.id"
                        @click="scrollToWeek(week.id)"
                        class="flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 transition-all hover:border-brand-200 hover:text-brand-600 dark:border-gray-700 dark:text-gray-400 dark:hover:border-brand-800 dark:hover:text-brand-400"
                    >
                        <Icon name="layers" :size="12" />
                        Semana {{ week.week_number }}
                    </button>
                </div>
            </div>

            <div v-if="plan.status === 'failed'" class="rounded-xl border border-red-200/60 bg-red-50/60 p-6 dark:border-red-900/30 dark:bg-red-950/20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                            <Icon name="alert-circle" :size="20" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-red-800 dark:text-red-200">Error al generar el plan</p>
                            <p class="text-xs text-red-600 dark:text-red-400">La generacion con IA fallo. Puedes intentarlo de nuevo.</p>
                        </div>
                    </div>
                    <button
                        @click="router.post(route('study-plans.retry', plan.id))"
                        class="btn-primary bg-red-600 hover:bg-red-700 px-5 py-2 text-sm"
                    >
                        <Icon name="refresh" :size="16" />
                        Reintentar
                    </button>
                </div>
            </div>

            <div v-if="plan.status === 'generating'" class="rounded-xl border border-amber-200/60 bg-amber-50/60 p-6 dark:border-amber-900/30 dark:bg-amber-950/20">
                <div class="flex items-center gap-3">
                    <Icon name="loader" :size="20" class="animate-spin text-amber-600" />
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Generando plan con IA...</p>
                </div>
            </div>

            <div :class="['space-y-4', 'transition-all duration-700 delay-150 ease-out-expo', mounted ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0']">
                <div
                    v-for="(week, i) in plan.weeks"
                    :key="week.id"
                    :id="`week-${week.id}`"
                    class="rounded-xl border border-gray-200/60 bg-white/60 shadow-sm transition-all duration-500 ease-out-expo dark:border-gray-800/40 dark:bg-gray-900/40 animate-fade-in-up"
                    :style="{ animationDelay: `${i * 80}ms` }"
                >
                    <button
                        @click="toggleWeek(week.id)"
                        class="flex w-full items-center justify-between p-6 text-left transition-all duration-200 hover:bg-gray-50/50 dark:hover:bg-gray-800/30"
                    >
                        <div class="flex items-center gap-4">
                            <div :class="[
                                'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-sm font-bold shadow-sm transition-all duration-300',
                                completedTopicsCount(week) === week.topics.length
                                    ? 'bg-emerald-500 text-white'
                                    : 'bg-brand-600 text-white'
                            ]">
                                <Icon v-if="completedTopicsCount(week) === week.topics.length" name="check" :size="18" />
                                <span v-else>{{ week.week_number }}</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white">{{ week.title }}</h3>
                                <p v-if="week.summary" class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ week.summary }}</p>
                                <div class="mt-1.5 flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <Icon name="list" :size="12" />
                                        {{ completedTopicsCount(week) }}/{{ week.topics.length }} temas
                                    </span>
                                    <span v-if="weekMinutes(week) > 0" class="flex items-center gap-1">
                                        <Icon name="clock" :size="12" />
                                        {{ formatMinutes(weekMinutes(week)) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="hidden h-1.5 w-20 overflow-hidden rounded-full bg-gray-100 sm:block dark:bg-gray-800">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600 transition-all duration-500"
                                    :style="{ width: week.topics.length > 0 ? (completedTopicsCount(week) / week.topics.length) * 100 + '%' : '0%' }"
                                />
                            </div>
                            <Icon
                                :name="activeWeek === week.id ? 'chevron-up' : 'chevron-down'"
                                :size="18"
                                class="text-gray-400 transition-all duration-300"
                            />
                        </div>
                    </button>

                    <transition name="accordion">
                        <div v-if="activeWeek === week.id" class="border-t border-gray-100 dark:border-gray-800/50">
                            <div
                                v-for="(topic, index) in week.topics"
                                :key="topic.id"
                                :class="[
                                    'border-b border-gray-50 p-5 transition-all last:border-0 hover:bg-gray-50/30 dark:border-gray-800/30 dark:hover:bg-gray-800/20',
                                    topic.is_completed ? 'opacity-60' : ''
                                ]"
                            >
                                <div class="flex items-start gap-4">
                                    <button
                                        @click="toggleTopic(topic.id)"
                                        :class="[
                                            'mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition-all duration-200',
                                            topic.is_completed
                                                ? 'border-emerald-500 bg-emerald-500 hover:bg-emerald-600'
                                                : 'border-gray-300 hover:border-brand-400 dark:border-gray-600 dark:hover:border-brand-500'
                                        ]"
                                    >
                                        <Icon v-if="topic.is_completed" name="check" :size="12" class="text-white" />
                                        <Icon v-else name="plus" :size="12" class="text-gray-300 dark:text-gray-600" />
                                    </button>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <div>
                                                <h4 :class="[
                                                    'text-sm font-medium transition-all',
                                                    topic.is_completed
                                                        ? 'text-gray-400 line-through dark:text-gray-500'
                                                        : 'text-gray-900 dark:text-white'
                                                ]">{{ topic.title }}</h4>
                                                <div v-if="topic.sessions?.length" class="mt-1 flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                                                    <span class="flex items-center gap-0.5">
                                                        <Icon name="clock" :size="10" />
                                                        {{ formatMinutes(topic.sessions.reduce((a: number, s: any) => a + s.duration_minutes, 0)) }}
                                                    </span>
                                                    <span>{{ topic.sessions.length }} sesiones</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button
                                                    @click.stop="openSessionForm(topic.id)"
                                                    class="btn-ghost text-xs !px-2.5 !py-1"
                                                >
                                                    <Icon name="plus" :size="12" />
                                                    Registrar
                                                </button>
                                                <span :class="['badge', difficultyColors[topic.difficulty] || 'badge-gray']">
                                                    <Icon :name="topic.difficulty === 'beginner' ? 'target' : topic.difficulty === 'intermediate' ? 'trending-up' : 'award'" :size="10" />
                                                    {{ topic.difficulty }}
                                                </span>
                                            </div>
                                        </div>

                                        <div v-if="topic.theory_content" class="mt-3 rounded-xl bg-brand-50/50 p-4 dark:bg-brand-950/30">
                                            <div class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400">
                                                <Icon name="book-open" :size="12" />
                                                Teoria
                                            </div>
                                            <p class="mt-1.5 text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ topic.theory_content }}</p>
                                        </div>

                                        <div v-if="topic.practice_description" class="mt-3">
                                            <div class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                                <Icon name="code" :size="12" />
                                                Practica
                                            </div>
                                            <p class="mt-1.5 text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ topic.practice_description }}</p>
                                        </div>

                                        <div v-if="topic.examples" class="mt-3 overflow-hidden rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                                            <div class="flex items-center gap-1.5 border-b border-gray-200 bg-gray-100/50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
                                                <Icon name="file-text" :size="12" class="text-gray-400" />
                                                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Ejemplo</span>
                                            </div>
                                            <pre class="overflow-x-auto p-4 text-sm text-gray-700 dark:text-gray-300"><code>{{ topic.examples }}</code></pre>
                                        </div>

                                        <!-- Session form -->
                                        <div v-if="sessionTopic === topic.id" class="mt-4 rounded-xl border border-brand-200 bg-brand-50/50 p-4 dark:border-brand-800/30 dark:bg-brand-950/20">
                                            <div class="flex items-center justify-between mb-4">
                                                <span class="text-sm font-medium text-brand-700 dark:text-brand-300">Registrar sesion</span>
                                                <button @click="closeSessionForm" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <Icon name="x" :size="16" />
                                                </button>
                                            </div>

                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Duracion (minutos)</label>
                                                    <div class="flex items-center gap-3">
                                                        <input
                                                            type="range"
                                                            min="5"
                                                            max="180"
                                                            v-model.number="sessionForm.duration_minutes"
                                                            class="h-1.5 flex-1 cursor-pointer appearance-none rounded-full bg-gray-200 accent-brand-600 dark:bg-gray-700"
                                                        />
                                                        <span class="text-sm font-bold text-brand-600 w-10 text-right">{{ sessionForm.duration_minutes }}m</span>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nivel de confianza</label>
                                                    <div class="flex gap-2">
                                                        <button
                                                            v-for="n in 5"
                                                            :key="n"
                                                            type="button"
                                                            @click="sessionForm.confidence_level = n"
                                                            :class="[
                                                                'flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold transition-all',
                                                                n <= sessionForm.confidence_level
                                                                    ? 'bg-brand-600 text-white'
                                                                    : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500'
                                                            ]"
                                                        >{{ n }}</button>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Notas (opcional)</label>
                                                    <textarea
                                                        v-model="sessionForm.notes"
                                                        rows="2"
                                                        placeholder="Que aprendiste? Que fue dificil?"
                                                        class="w-full rounded-lg border border-gray-200 bg-white/50 p-2.5 text-sm text-gray-900 transition-all placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500"
                                                    />
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <button
                                                        @click="submitSession(topic.id)"
                                                        :disabled="sessionForm.processing"
                                                        class="btn-primary px-4 py-2 text-xs"
                                                    >
                                                        <Icon v-if="sessionForm.processing" name="loader" :size="14" class="animate-spin" />
                                                        <Icon v-else name="check" :size="14" />
                                                        Guardar sesion
                                                    </button>
                                                    <button @click="closeSessionForm" type="button" class="btn-secondary px-4 py-2 text-xs">
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Session list -->
                                        <div v-if="topic.sessions?.length && sessionTopic !== topic.id" class="mt-3 space-y-1.5">
                                            <div
                                                v-for="session in topic.sessions"
                                                :key="session.id"
                                                class="flex items-center justify-between rounded-lg bg-gray-50/50 px-3 py-2 dark:bg-gray-800/30"
                                            >
                                                <div class="flex items-center gap-3 text-xs">
                                                    <Icon name="clock" :size="12" class="text-gray-400" />
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ formatMinutes(session.duration_minutes) }}</span>
                                                    <span class="flex items-center gap-1 text-gray-500">
                                                        <Icon name="star" :size="10" />
                                                        {{ session.confidence_level }}/5
                                                    </span>
                                                    <span v-if="session.notes" class="max-w-[200px] truncate text-gray-400">{{ session.notes }}</span>
                                                </div>
                                                <button
                                                    @click="deleteSession(session.id)"
                                                    class="text-gray-400 transition-colors hover:text-red-500"
                                                >
                                                    <Icon name="trash" :size="12" />
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                            <span class="inline-flex items-center gap-1">
                                                <Icon name="clock" :size="12" />
                                                {{ topic.estimated_minutes }} min
                                            </span>
                                            <span class="inline-flex items-center gap-1">
                                                <Icon :name="contentTypeIcons[topic.content_type] || 'file-text'" :size="12" />
                                                {{ contentTypeLabels[topic.content_type] || topic.content_type }}
                                            </span>
                                            <span v-if="topic.is_completed && topic.completed_at" class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                                                <Icon name="check-circle" :size="12" />
                                                Completado
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.3s ease;
    overflow: hidden;
}
.accordion-enter-from,
.accordion-leave-to {
    opacity: 0;
    max-height: 0;
}
.accordion-enter-to,
.accordion-leave-from {
    max-height: 2500px;
}
</style>
