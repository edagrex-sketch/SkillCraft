<script setup lang="ts">
import { ref, computed, watch, onUnmounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import Icon from '@/Components/Icon.vue'

const props = defineProps<{
    topic: any
    visible: boolean
}>()

const emit = defineEmits<{
    close: []
}>()

const activeTab = ref<'theory' | 'practice' | 'examples' | 'session'>('theory')
const timerRunning = ref(false)
const timerSeconds = ref(0)
const timerInterval = ref<ReturnType<typeof setInterval> | null>(null)
const practiceDone = ref(false)
const showCompletion = ref(false)

const sessionForm = useForm({
    duration_minutes: 0,
    confidence_level: 3,
    notes: '',
})

const tabs = [
    { key: 'theory' as const, label: 'Teoria', icon: 'book-open' },
    { key: 'practice' as const, label: 'Practica', icon: 'code' },
    { key: 'examples' as const, label: 'Ejemplos', icon: 'file-text' },
    { key: 'session' as const, label: 'Mi sesion', icon: 'activity' },
]

const formattedTime = computed(() => {
    const h = Math.floor(timerSeconds.value / 3600)
    const m = Math.floor((timerSeconds.value % 3600) / 60)
    const s = timerSeconds.value % 60
    if (h > 0) return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
})

const hasContent = computed(() => ({
    theory: !!props.topic?.theory_content,
    practice: !!props.topic?.practice_description,
    examples: !!props.topic?.examples,
}))

function startTimer() {
    if (timerRunning.value) return
    timerRunning.value = true
    timerInterval.value = setInterval(() => {
        timerSeconds.value++
    }, 1000)
}

function stopTimer() {
    timerRunning.value = false
    if (timerInterval.value) {
        clearInterval(timerInterval.value)
        timerInterval.value = null
    }
}

function resetTimer() {
    stopTimer()
    timerSeconds.value = 0
}

function toggleTopic() {
    router.patch(route('topics.complete', props.topic.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            showCompletion.value = true
            setTimeout(() => { showCompletion.value = false }, 2000)
        },
    })
}

function submitSession() {
    sessionForm.duration_minutes = Math.max(1, Math.round(timerSeconds.value / 60))
    sessionForm.post(route('topics.sessions.store', props.topic.id), {
        preserveScroll: true,
        onSuccess: () => {
            sessionForm.reset()
            resetTimer()
            practiceDone.value = false
        },
    })
}

watch(() => props.visible, (val) => {
    if (val) {
        activeTab.value = props.topic?.theory_content ? 'theory' : props.topic?.practice_description ? 'practice' : 'session'
    } else {
        stopTimer()
    }
})

onUnmounted(() => stopTimer())
</script>

<template>
    <Teleport to="body">
        <transition name="overlay">
            <div v-if="visible" class="fixed inset-0 z-50 flex justify-end" @click.self="emit('close')">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity duration-300" @click="emit('close')" />

                <transition name="panel" appear>
                    <div v-if="visible" class="relative flex h-full w-full max-w-2xl flex-col bg-white shadow-2xl dark:bg-gray-900">
                        <div class="pointer-events-none absolute inset-0 bg-dot opacity-[0.03] dark:opacity-[0.05]" />

                        <div class="relative flex items-center justify-between border-b border-gray-100 px-6 py-5 dark:border-gray-800/50">
                            <div class="flex items-center gap-3 min-w-0">
                                <div :class="[
                                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm font-bold text-white shadow-sm transition-all',
                                    topic?.is_completed ? 'bg-emerald-500' : 'bg-brand-600'
                                ]">
                                    <Icon v-if="topic?.is_completed" name="check" :size="16" />
                                    <Icon v-else name="lightbulb" :size="16" />
                                </div>
                                <div class="min-w-0">
                                    <h2 class="truncate text-[15px] font-semibold text-gray-900 dark:text-white">{{ topic?.title }}</h2>
                                    <p v-if="topic?.estimated_minutes" class="text-xs text-gray-500 dark:text-gray-400">{{ topic.estimated_minutes }} min estimados</p>
                                </div>
                            </div>
                            <button @click="emit('close')" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                                <Icon name="x" :size="18" />
                            </button>
                        </div>

                        <div class="flex gap-1 border-b border-gray-100 px-4 py-3 dark:border-gray-800/50">
                            <button
                                v-for="tab in tabs"
                                :key="tab.key"
                                @click="activeTab = tab.key"
                                :class="[
                                    'flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-all duration-200',
                                    activeTab === tab.key
                                        ? 'bg-brand-100 text-brand-700 shadow-sm dark:bg-brand-900/30 dark:text-brand-300'
                                        : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200'
                                ]"
                            >
                                <Icon :name="tab.icon" :size="14" />
                                {{ tab.label }}
                            </button>
                        </div>

                        <div class="relative flex-1 overflow-y-auto">
                            <transition name="tab-fade" mode="out-in">
                                <div v-if="activeTab === 'theory'" key="theory" class="p-6">
                                    <div v-if="hasContent.theory" class="animate-fade-in-up">
                                        <div class="mb-4 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400">
                                            <Icon name="book-open" :size="14" />
                                            Contenido teorico
                                        </div>
                                        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed dark:text-gray-300">
                                            {{ topic.theory_content }}
                                        </div>
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center py-16 text-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                            <Icon name="book-open" :size="22" />
                                        </div>
                                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">No hay contenido teorico para este tema</p>
                                    </div>
                                </div>

                                <div v-else-if="activeTab === 'practice'" key="practice" class="p-6">
                                    <div v-if="hasContent.practice" class="animate-fade-in-up">
                                        <div class="mb-4 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                            <Icon name="code" :size="14" />
                                            Ejercicio practico
                                        </div>
                                        <div class="rounded-xl border border-emerald-200/60 bg-emerald-50/50 p-5 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                                            <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ topic.practice_description }}</p>
                                        </div>

                                        <div class="mt-6">
                                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition-all duration-200" :class="practiceDone ? 'border-emerald-400 bg-emerald-50/80 dark:border-emerald-600 dark:bg-emerald-950/30' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'">
                                                <div :class="[
                                                    'flex h-6 w-6 items-center justify-center rounded-md border-2 transition-all duration-200',
                                                    practiceDone
                                                        ? 'border-emerald-500 bg-emerald-500'
                                                        : 'border-gray-300 dark:border-gray-600'
                                                ]">
                                                    <Icon v-if="practiceDone" name="check" :size="14" class="text-white" />
                                                </div>
                                                <span :class="['text-sm font-medium transition-colors', practiceDone ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-300']">
                                                    {{ practiceDone ? 'Practica completada' : 'Marcar practica como realizada' }}
                                                </span>
                                            </label>
                                        </div>

                                        <div class="mt-6 rounded-xl border border-amber-200/60 bg-amber-50/50 p-4 dark:border-amber-900/30 dark:bg-amber-950/20">
                                            <div class="flex items-start gap-3">
                                                <Icon name="lightbulb" :size="18" class="mt-0.5 shrink-0 text-amber-600 dark:text-amber-400" />
                                                <div class="text-sm text-amber-800 dark:text-amber-200">
                                                    <p class="font-medium">Tip:</p>
                                                    <p class="mt-0.5 text-amber-700 dark:text-amber-300">Intenta resolverlo sin mirar la solucion primero. Si te atascas, revisa los ejemplos.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center py-16 text-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                            <Icon name="code" :size="22" />
                                        </div>
                                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">No hay ejercicio practico para este tema</p>
                                    </div>
                                </div>

                                <div v-else-if="activeTab === 'examples'" key="examples" class="p-6">
                                    <div v-if="hasContent.examples" class="animate-fade-in-up">
                                        <div class="mb-4 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                                            <Icon name="file-text" :size="14" />
                                            Ejemplos
                                        </div>
                                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50/80 dark:border-gray-700 dark:bg-gray-800/50">
                                            <div class="flex items-center gap-2 border-b border-gray-200 bg-gray-100/50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
                                                <Icon name="file-text" :size="14" class="text-gray-400" />
                                                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Ejemplo</span>
                                            </div>
                                            <pre class="overflow-x-auto p-4 text-sm text-gray-700 dark:text-gray-300"><code>{{ topic.examples }}</code></pre>
                                        </div>
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center py-16 text-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                            <Icon name="file-text" :size="22" />
                                        </div>
                                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">No hay ejemplos para este tema</p>
                                    </div>
                                </div>

                                <div v-else-if="activeTab === 'session'" key="session" class="p-6">
                                    <div class="animate-fade-in-up space-y-6">
                                        <div>
                                            <div class="mb-3 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                                <Icon name="clock" :size="14" />
                                                Temporizador
                                            </div>
                                            <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-6 text-center dark:border-gray-700 dark:bg-gray-800/50">
                                                <div class="text-5xl font-bold tabular-nums tracking-tight text-gray-900 dark:text-white">
                                                    {{ formattedTime }}
                                                </div>
                                                <div class="mt-4 flex items-center justify-center gap-3">
                                                    <button
                                                        v-if="!timerRunning"
                                                        @click="startTimer"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-brand-700 hover:shadow-md active:scale-[0.97]"
                                                    >
                                                        <Icon name="play" :size="14" />
                                                        Iniciar
                                                    </button>
                                                    <button
                                                        v-else
                                                        @click="stopTimer"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-amber-700 active:scale-[0.97]"
                                                    >
                                                        <Icon name="zap" :size="14" />
                                                        Pausar
                                                    </button>
                                                    <button
                                                        @click="resetTimer"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm transition-all hover:bg-gray-50 active:scale-[0.97] dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700"
                                                    >
                                                        <Icon name="refresh" :size="14" />
                                                        Reiniciar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="mb-3 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                                <Icon name="brain" :size="14" />
                                                Nivel de confianza
                                            </div>
                                            <div class="flex gap-2">
                                                <button
                                                    v-for="n in 5"
                                                    :key="n"
                                                    type="button"
                                                    @click="sessionForm.confidence_level = n"
                                                    :class="[
                                                        'flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold transition-all duration-200',
                                                        n <= sessionForm.confidence_level
                                                            ? 'bg-brand-600 text-white shadow-md'
                                                            : 'bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-500 dark:hover:bg-gray-700'
                                                    ]"
                                                >{{ n }}</button>
                                            </div>
                                            <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                                                {{ ['', 'Muy dificil', 'Dificil', 'Neutral', 'Facil', 'Muy facil'][sessionForm.confidence_level] }}
                                            </p>
                                        </div>

                                        <div>
                                            <div class="mb-3 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                                <Icon name="edit" :size="14" />
                                                Notas
                                            </div>
                                            <textarea
                                                v-model="sessionForm.notes"
                                                rows="4"
                                                placeholder="Que aprendiste? Que fue lo mas dificil? Que te gustaria repasar?"
                                                class="w-full rounded-xl border border-gray-200 bg-white/50 p-3.5 text-sm text-gray-900 transition-all placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500"
                                            />
                                        </div>

                                        <div class="flex flex-col gap-3 pt-2">
                                            <button
                                                @click="submitSession"
                                                :disabled="sessionForm.processing"
                                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-b from-brand-600 to-brand-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:from-brand-500 hover:to-brand-600 hover:shadow-xl active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                <Icon v-if="sessionForm.processing" name="loader" :size="16" class="animate-spin" />
                                                <Icon v-else name="check-circle" :size="16" />
                                                {{ sessionForm.processing ? 'Guardando...' : 'Guardar sesion de estudio' }}
                                            </button>

                                            <button
                                                @click="toggleTopic"
                                                :class="[
                                                    'flex w-full items-center justify-center gap-2 rounded-xl border-2 px-5 py-2.5 text-sm font-semibold transition-all duration-200 active:scale-[0.97]',
                                                    topic?.is_completed
                                                        ? 'border-emerald-400 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-300'
                                                        : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                                                ]"
                                            >
                                                <Icon :name="topic?.is_completed ? 'x' : 'check'" :size="16" />
                                                {{ topic?.is_completed ? 'Desmarcar completado' : 'Marcar tema como completado' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </transition>

                            <transition name="completion-pop">
                                <div v-if="showCompletion" class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 backdrop-blur-sm dark:bg-gray-900/80">
                                    <div class="text-center animate-scale-in">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            <Icon name="check" :size="32" />
                                        </div>
                                        <p class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Tema actualizado</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ topic?.is_completed ? 'Desmarcado' : 'Completado' }}</p>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
.overlay-enter-active,
.overlay-leave-active {
    transition: opacity 0.3s ease;
}
.overlay-enter-from,
.overlay-leave-to {
    opacity: 0;
}

.panel-enter-active {
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.panel-leave-active {
    transition: transform 0.25s ease-in;
}
.panel-enter-from,
.panel-leave-to {
    transform: translateX(100%);
}

.tab-fade-enter-active,
.tab-fade-leave-active {
    transition: all 0.25s ease;
}
.tab-fade-enter-from {
    opacity: 0;
    transform: translateY(8px);
}
.tab-fade-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

.completion-pop-enter-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.completion-pop-leave-active {
    transition: all 0.2s ease-in;
}
.completion-pop-enter-from {
    opacity: 0;
    transform: scale(0.9);
}
.completion-pop-leave-to {
    opacity: 0;
    transform: scale(0.9);
}
</style>
