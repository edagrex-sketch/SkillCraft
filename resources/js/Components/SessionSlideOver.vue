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

const currentStep = ref(0)
const timerRunning = ref(false)
const timerSeconds = ref(0)
const timerInterval = ref<ReturnType<typeof setInterval> | null>(null)
const showCompletion = ref(false)

const activeRecallAnswer = ref('')
const activeRecallRevealed = ref(false)
const practiceSubLevel = ref<'guided' | 'structured' | 'open'>('guided')
const reflectionAnswers = ref<Record<number, string>>({})
const practiceGuidedDone = ref(false)
const practiceStructuredDone = ref(false)
const practiceOpenDone = ref(false)

const sessionForm = useForm({
    duration_minutes: 0,
    confidence_level: 3,
    notes: '',
    active_recall_score: null as number | null,
    reflection_answers: null as Record<number, string> | null,
    phase_completions: null as string[] | null,
    needs_review: false,
})

const steps = computed(() => [
    {
        key: 'preparation',
        label: 'Preparacion',
        icon: 'target',
        condition: true,
    },
    {
        key: 'learn',
        label: 'Aprender',
        icon: 'book-open',
        condition: true,
    },
    {
        key: 'verify',
        label: 'Verificar',
        icon: 'brain',
        condition: !!props.topic?.active_recall_question,
    },
    {
        key: 'practice',
        label: 'Practicar',
        icon: 'code',
        condition: true,
    },
    {
        key: 'debug',
        label: 'Depurar',
        icon: 'alert-circle',
        condition: !!(props.topic?.common_mistakes?.length || props.topic?.reflection_prompts?.length),
    },
    {
        key: 'project',
        label: 'Proyecto',
        icon: 'layers',
        condition: !!props.topic?.challenge,
    },
    {
        key: 'evaluate',
        label: 'Evaluar',
        icon: 'check-circle',
        condition: true,
    },
])

const filteredSteps = computed(() => steps.value.filter(s => s.condition))

const progressPercent = computed(() => {
    return Math.round((currentStep.value / (filteredSteps.value.length - 1)) * 100)
})

const formattedTime = computed(() => {
    const h = Math.floor(timerSeconds.value / 3600)
    const m = Math.floor((timerSeconds.value % 3600) / 60)
    const s = timerSeconds.value % 60
    if (h > 0) return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
})

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

function nextStep() {
    if (currentStep.value < filteredSteps.value.length - 1) {
        currentStep.value++
    }
}

function prevStep() {
    if (currentStep.value > 0) {
        currentStep.value--
    }
}

function revealAnswer() {
    activeRecallRevealed.value = true
}

function rateRecall(score: number) {
    sessionForm.active_recall_score = score
}

function markPracticeLevel(level: 'guided' | 'structured' | 'open') {
    if (level === 'guided') practiceGuidedDone.value = true
    if (level === 'structured') practiceStructuredDone.value = true
    if (level === 'open') practiceOpenDone.value = true

    if (level === 'guided' && props.topic?.practice_structured) {
        practiceSubLevel.value = 'structured'
    } else if (level === 'structured' && props.topic?.practice_open) {
        practiceSubLevel.value = 'open'
    }
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
    const phasesCompleted = filteredSteps.value.map(s => s.key)

    sessionForm.duration_minutes = Math.max(1, Math.round(timerSeconds.value / 60))
    sessionForm.reflection_answers = Object.keys(reflectionAnswers.value).length > 0 ? reflectionAnswers.value : null
    sessionForm.phase_completions = phasesCompleted
    sessionForm.needs_review = (sessionForm.active_recall_score ?? 3) < 3

    sessionForm.post(route('topics.sessions.store', props.topic.id), {
        preserveScroll: true,
        onSuccess: () => {
            sessionForm.reset()
            resetTimer()
            activeRecallAnswer.value = ''
            activeRecallRevealed.value = false
            practiceGuidedDone.value = false
            practiceStructuredDone.value = false
            practiceOpenDone.value = false
            practiceSubLevel.value = 'guided'
            reflectionAnswers.value = {}
            currentStep.value = 0
        },
    })
}

const currentStepKey = computed(() => filteredSteps.value[currentStep.value]?.key ?? 'evaluate')

watch(() => props.visible, (val) => {
    if (!val) {
        stopTimer()
        currentStep.value = 0
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

                        <div class="relative flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800/50">
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
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ topic?.estimated_minutes }} min &middot; Nivel {{ topic?.difficulty }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-mono font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    <Icon name="clock" :size="14" class="text-gray-400" />
                                    {{ formattedTime }}
                                </div>
                                <button
                                    v-if="!timerRunning"
                                    @click="startTimer"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-gray-100 hover:text-brand-600 dark:hover:bg-gray-800 dark:hover:text-brand-400"
                                >
                                    <Icon name="play" :size="16" />
                                </button>
                                <button
                                    v-else
                                    @click="stopTimer"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-gray-100 hover:text-amber-600 dark:hover:bg-gray-800 dark:hover:text-amber-400"
                                >
                                    <Icon name="zap" :size="16" />
                                </button>
                                <button @click="emit('close')" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                                    <Icon name="x" :size="18" />
                                </button>
                            </div>
                        </div>

                        <div class="border-b border-gray-100 px-6 py-3 dark:border-gray-800/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                        Paso {{ currentStep + 1 }} de {{ filteredSteps.length }}
                                    </span>
                                    <span class="rounded-md bg-brand-100 px-2 py-0.5 text-[11px] font-semibold text-brand-700 dark:bg-brand-900/30 dark:text-brand-300">
                                        {{ filteredSteps[currentStep]?.label }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600 transition-all duration-500 ease-out-expo" :style="{ width: progressPercent + '%' }" />
                            </div>
                        </div>

                        <div class="relative flex-1 overflow-y-auto">
                            <div class="min-h-full p-6">
                                <transition name="tab-fade" mode="out-in">
                                    <!-- PREPARACION -->
                                    <div v-if="currentStepKey === 'preparation'" key="preparation" class="animate-fade-in-up space-y-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                                <Icon name="target" :size="20" />
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Preparacion</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Contexto y preparacion para el tema</p>
                                            </div>
                                        </div>

                                        <div v-if="topic?.session_context" class="rounded-xl border border-amber-200/60 bg-amber-50/60 p-5 dark:border-amber-900/30 dark:bg-amber-950/20">
                                            <div class="mb-2 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">
                                                <Icon name="info" :size="12" />
                                                Por que es importante?
                                            </div>
                                            <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ topic.session_context }}</p>
                                        </div>

                                        <div v-if="topic?.analogies?.length" class="space-y-3">
                                            <div class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                                <Icon name="lightbulb" :size="12" />
                                                Analogias
                                            </div>
                                            <div v-for="(analogy, i) in topic.analogies" :key="i" class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                                    <span class="text-sm font-bold">{{ i + 1 }}</span>
                                                </div>
                                                <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ analogy }}</p>
                                            </div>
                                        </div>

                                        <div class="flex justify-end">
                                            <button @click="nextStep" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all hover:bg-brand-500 active:scale-[0.97]">
                                                Comenzar aprendizaje
                                                <Icon name="arrow-right" :size="16" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- APRENDER -->
                                    <div v-else-if="currentStepKey === 'learn'" key="learn" class="animate-fade-in-up space-y-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                                <Icon name="book-open" :size="20" />
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aprende el concepto</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Estudia la teoria y entiende el fundamento</p>
                                            </div>
                                        </div>

                                        <div v-if="topic?.theory_content" class="prose prose-sm max-w-none leading-relaxed text-gray-700 dark:text-gray-300">
                                            {{ topic.theory_content }}
                                        </div>

                                        <div v-if="topic?.examples" class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50/80 dark:border-gray-700 dark:bg-gray-800/50">
                                            <div class="flex items-center gap-2 border-b border-gray-200 bg-gray-100/50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
                                                <Icon name="file-text" :size="14" class="text-gray-400" />
                                                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Ejemplo</span>
                                            </div>
                                            <pre class="overflow-x-auto p-4 text-sm text-gray-700 dark:text-gray-300"><code>{{ topic.examples }}</code></pre>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <button @click="prevStep" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                                                <Icon name="arrow-left" :size="14" />
                                                Atras
                                            </button>
                                            <button @click="nextStep" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all hover:bg-brand-500 active:scale-[0.97]">
                                                Lo entendi, siguiente
                                                <Icon name="arrow-right" :size="16" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- VERIFICAR (Active Recall) -->
                                    <div v-else-if="currentStepKey === 'verify'" key="verify" class="animate-fade-in-up space-y-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                                <Icon name="brain" :size="20" />
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Verifica tu comprension</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Active Recall: explica sin mirar la teoria</p>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-purple-200/60 bg-purple-50/60 p-5 dark:border-purple-900/30 dark:bg-purple-950/20">
                                            <p class="text-sm font-medium text-purple-900 dark:text-purple-200">{{ topic?.active_recall_question }}</p>
                                        </div>

                                        <div>
                                            <textarea
                                                v-model="activeRecallAnswer"
                                                rows="4"
                                                placeholder="Escribe tu respuesta aqui. Intenta explicarlo como si le ensearas a alguien mas (Tecnica Feynman)..."
                                                class="w-full rounded-xl border border-gray-200 bg-white/50 p-3.5 text-sm text-gray-900 transition-all placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white"
                                            />
                                        </div>

                                        <div v-if="!activeRecallRevealed" class="flex items-center gap-3">
                                            <button @click="revealAnswer" class="inline-flex items-center gap-1.5 rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-purple-600/25 transition-all hover:bg-purple-500 active:scale-[0.97]">
                                                <Icon name="eye" :size="16" />
                                                Ver retroalimentacion
                                            </button>
                                            <button @click="nextStep" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                                Omitir
                                            </button>
                                        </div>

                                        <div v-else class="space-y-4">
                                            <div class="rounded-xl border border-emerald-200/60 bg-emerald-50/60 p-4 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                                                <div class="mb-2 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                                                    <Icon name="check-circle" :size="12" />
                                                    Reflexion
                                                </div>
                                                <p class="text-sm text-gray-700 dark:text-gray-300">Compara tu respuesta con lo que aprendiste en la teoria. La practica de recordar activamente consolida el aprendizaje mucho mas que releer.</p>
                                            </div>

                                            <div>
                                                <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Que tan bien pudiste explicarlo?</p>
                                                <div class="flex gap-2">
                                                    <button
                                                        v-for="n in 5"
                                                        :key="n"
                                                        type="button"
                                                        @click="rateRecall(n)"
                                                        :class="[
                                                            'flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold transition-all duration-200',
                                                            n <= (sessionForm.active_recall_score ?? 0)
                                                                ? 'bg-purple-600 text-white shadow-md'
                                                                : 'bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-500'
                                                        ]"
                                                    >{{ n }}</button>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                                    {{ ['', 'No lo recordaba', 'Idea vaga', 'Bastante bien', 'Casi perfecto', 'Perfecto, podria ensearlo'][sessionForm.active_recall_score ?? 0] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <button @click="prevStep" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                                <Icon name="arrow-left" :size="14" />
                                                Atras
                                            </button>
                                            <button @click="nextStep" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all hover:bg-brand-500 active:scale-[0.97]">
                                                Siguiente
                                                <Icon name="arrow-right" :size="16" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- PRACTICAR (3 niveles de andamiaje) -->
                                    <div v-else-if="currentStepKey === 'practice'" key="practice" class="animate-fade-in-up space-y-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                <Icon name="code" :size="20" />
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Practica progresiva</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Andamiaje cognitivo: de guiado a autonomo</p>
                                            </div>
                                        </div>

                                        <div class="flex gap-2">
                                            <button
                                                @click="practiceSubLevel = 'guided'"
                                                :class="[
                                                    'flex-1 rounded-lg border px-3 py-2 text-center text-xs font-semibold transition-all',
                                                    practiceSubLevel === 'guided' ? 'border-emerald-400 bg-emerald-50 text-emerald-700 dark:border-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-300' : 'border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400'
                                                ]"
                                            >
                                                <Icon name="chevron-right" :size="12" class="mx-auto mb-0.5" />
                                                Guiado
                                                <span v-if="practiceGuidedDone" class="ml-1 text-emerald-500"><Icon name="check" :size="10" /></span>
                                            </button>
                                            <button
                                                v-if="topic?.practice_structured"
                                                @click="practiceSubLevel = 'structured'"
                                                :class="[
                                                    'flex-1 rounded-lg border px-3 py-2 text-center text-xs font-semibold transition-all',
                                                    practiceSubLevel === 'structured' ? 'border-emerald-400 bg-emerald-50 text-emerald-700 dark:border-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-300' : 'border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400'
                                                ]"
                                            >
                                                <Icon name="list" :size="12" class="mx-auto mb-0.5" />
                                                Estructurado
                                                <span v-if="practiceStructuredDone" class="ml-1 text-emerald-500"><Icon name="check" :size="10" /></span>
                                            </button>
                                            <button
                                                v-if="topic?.practice_open"
                                                @click="practiceSubLevel = 'open'"
                                                :class="[
                                                    'flex-1 rounded-lg border px-3 py-2 text-center text-xs font-semibold transition-all',
                                                    practiceSubLevel === 'open' ? 'border-emerald-400 bg-emerald-50 text-emerald-700 dark:border-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-300' : 'border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400'
                                                ]"
                                            >
                                                <Icon name="layers" :size="12" class="mx-auto mb-0.5" />
                                                Abierto
                                                <span v-if="practiceOpenDone" class="ml-1 text-emerald-500"><Icon name="check" :size="10" /></span>
                                            </button>
                                        </div>

                                        <div class="rounded-xl border border-emerald-200/60 bg-emerald-50/60 p-5 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                                            <div class="mb-2 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                                                <Icon :name="practiceSubLevel === 'guided' ? 'chevron-right' : practiceSubLevel === 'structured' ? 'list' : 'layers'" :size="12" />
                                                {{ practiceSubLevel === 'guided' ? 'Ejercicio Guiado' : practiceSubLevel === 'structured' ? 'Ejercicio Estructurado' : 'Ejercicio Abierto' }}
                                            </div>
                                            <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">
                                                {{ practiceSubLevel === 'guided' ? topic?.practice_guided || topic?.practice_description : practiceSubLevel === 'structured' ? topic?.practice_structured : topic?.practice_open }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-amber-200/60 bg-amber-50/50 p-4 dark:border-amber-900/30 dark:bg-amber-950/20">
                                            <div class="flex items-start gap-3">
                                                <Icon name="lightbulb" :size="18" class="mt-0.5 shrink-0 text-amber-600 dark:text-amber-400" />
                                                <div class="text-sm text-amber-800 dark:text-amber-200">
                                                    <p class="font-medium">Consejo:</p>
                                                    <p class="mt-0.5 text-amber-700 dark:text-amber-300">Intenta resolverlo sin mirar la solucion. Si te atascas, revisa los ejemplos del paso anterior.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <button @click="prevStep" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                                <Icon name="arrow-left" :size="14" />
                                                Atras
                                            </button>
                                            <div class="flex items-center gap-2">
                                                <button
                                                    v-if="!practiceGuidedDone || (practiceSubLevel === 'structured' && !practiceStructuredDone) || (practiceSubLevel === 'open' && !practiceOpenDone)"
                                                    @click="markPracticeLevel(practiceSubLevel)"
                                                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/25 transition-all hover:bg-emerald-500 active:scale-[0.97]"
                                                >
                                                    <Icon name="check" :size="14" />
                                                    Marcar como hecho
                                                </button>
                                                <button @click="nextStep" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all hover:bg-brand-500 active:scale-[0.97]">
                                                    Siguiente
                                                    <Icon name="arrow-right" :size="16" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- DEPURAR / REFLEXIONAR -->
                                    <div v-else-if="currentStepKey === 'debug'" key="debug" class="animate-fade-in-up space-y-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                                                <Icon name="alert-circle" :size="20" />
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Depura y Reflexiona</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Errores comunes y reflexion guiada</p>
                                            </div>
                                        </div>

                                        <div v-if="topic?.common_mistakes?.length">
                                            <div class="mb-3 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                                                <Icon name="alert-circle" :size="12" />
                                                Errores comunes
                                            </div>
                                            <div class="space-y-2">
                                                <div v-for="(mistake, i) in topic.common_mistakes" :key="i" class="flex items-start gap-3 rounded-xl border border-rose-200/60 bg-rose-50/60 p-4 dark:border-rose-900/30 dark:bg-rose-950/20">
                                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-200 text-rose-700 dark:bg-rose-800/50 dark:text-rose-300">
                                                        <span class="text-xs font-bold">!</span>
                                                    </div>
                                                    <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ mistake }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="topic?.reflection_prompts?.length">
                                            <div class="mb-3 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                                <Icon name="edit" :size="12" />
                                                Reflexion final
                                            </div>
                                            <div class="space-y-4">
                                                <div v-for="(prompt, i) in topic.reflection_prompts" :key="i" class="rounded-xl border border-gray-200 bg-white/50 p-4 dark:border-gray-700 dark:bg-gray-800/30">
                                                    <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ prompt }}</p>
                                                    <textarea
                                                        v-model="reflectionAnswers[i]"
                                                        rows="2"
                                                        placeholder="Escribe tu reflexion..."
                                                        class="w-full rounded-lg border border-gray-200 bg-white/50 p-3 text-sm text-gray-900 transition-all placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <button @click="prevStep" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                                <Icon name="arrow-left" :size="14" />
                                                Atras
                                            </button>
                                            <button @click="nextStep" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all hover:bg-brand-500 active:scale-[0.97]">
                                                Siguiente
                                                <Icon name="arrow-right" :size="16" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- PROYECTO / DESAFIO -->
                                    <div v-else-if="currentStepKey === 'project'" key="project" class="animate-fade-in-up space-y-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                                <Icon name="layers" :size="20" />
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Desafio / Mini-Proyecto</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Aplica lo aprendido en un contexto real</p>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-indigo-200/60 bg-indigo-50/60 p-5 dark:border-indigo-900/30 dark:bg-indigo-950/20">
                                            <div class="mb-2 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">
                                                <Icon name="target" :size="12" />
                                                Desafio
                                            </div>
                                            <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ topic?.challenge }}</p>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <button @click="prevStep" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                                <Icon name="arrow-left" :size="14" />
                                                Atras
                                            </button>
                                            <button @click="nextStep" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all hover:bg-brand-500 active:scale-[0.97]">
                                                Lo complete
                                                <Icon name="arrow-right" :size="16" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- EVALUAR -->
                                    <div v-else-if="currentStepKey === 'evaluate'" key="evaluate" class="animate-fade-in-up space-y-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                                                <Icon name="check-circle" :size="20" />
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Evaluar sesion</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Resumen y plan de repaso</p>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-emerald-200/60 bg-emerald-50/60 p-5 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                                            <div class="mb-2 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                                                <Icon name="clock" :size="12" />
                                                Tiempo de estudio
                                            </div>
                                            <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ formattedTime }}</p>
                                        </div>

                                        <div>
                                            <div class="mb-3 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                                <Icon name="star" :size="14" />
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
                                                            : 'bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-500'
                                                    ]"
                                                >{{ n }}</button>
                                            </div>
                                            <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                                                {{ ['', 'Muy dificil, necesito repasar', 'Dificil, pero lo entiendo', 'Neutral, lo basico', 'Facil, lo domino', 'Muy facil, podria ensearlo'][sessionForm.confidence_level] }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-4 dark:border-gray-700 dark:bg-gray-800/30">
                                            <div class="mb-2 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                                <Icon :name="(sessionForm.active_recall_score ?? 0) < 3 ? 'alert-circle' : 'check-circle'" :size="12" />
                                                Repaso espaciado
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                <template v-if="(sessionForm.active_recall_score ?? 0) < 3">
                                                    Te recomendamos repasar este tema pronto (nivel de active recall bajo).
                                                    <span class="font-medium text-amber-600 dark:text-amber-400">Sugerencia: repasa en 1-2 dias.</span>
                                                </template>
                                                <template v-else>
                                                    Buen trabajo! Tu nivel de comprension es bueno.
                                                    <span class="font-medium text-emerald-600 dark:text-emerald-400">Proxima revision sugerida: {{ sessionForm.confidence_level >= 4 ? '1-2 semanas' : '3-5 dias' }}</span>
                                                </template>
                                            </p>
                                        </div>

                                        <div>
                                            <div class="mb-3 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                                <Icon name="edit" :size="14" />
                                                Notas de la sesion
                                            </div>
                                            <textarea
                                                v-model="sessionForm.notes"
                                                rows="3"
                                                placeholder="Que aprendiste? Que fue lo mas dificil? Que te gustaria repasar en la proxima sesion?"
                                                class="w-full rounded-xl border border-gray-200 bg-white/50 p-3.5 text-sm text-gray-900 transition-all placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white"
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

                                            <div class="flex items-center gap-3">
                                                <button
                                                    @click="prevStep"
                                                    class="flex flex-1 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                                                >
                                                    <Icon name="arrow-left" :size="14" />
                                                    Atras
                                                </button>
                                                <button
                                                    @click="toggleTopic"
                                                    :class="[
                                                        'flex flex-1 items-center justify-center gap-2 rounded-xl border-2 px-4 py-2.5 text-sm font-semibold transition-all duration-200 active:scale-[0.97]',
                                                        topic?.is_completed
                                                            ? 'border-emerald-400 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-300'
                                                            : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                                    ]"
                                                >
                                                    <Icon :name="topic?.is_completed ? 'x' : 'check'" :size="16" />
                                                    {{ topic?.is_completed ? 'Desmarcar' : 'Completar tema' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-100 px-6 py-3 dark:border-gray-800/50">
                            <div class="flex gap-1">
                                <div
                                    v-for="(step, i) in filteredSteps"
                                    :key="step.key"
                                    :class="[
                                        'h-1.5 flex-1 rounded-full transition-all duration-300',
                                        i <= currentStep ? 'bg-brand-500' : 'bg-gray-200 dark:bg-gray-700'
                                    ]"
                                />
                            </div>
                            <div class="flex items-center gap-1 text-xs text-gray-400">
                                <Icon name="clock" :size="10" />
                                {{ formattedTime }}
                            </div>
                        </div>

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
