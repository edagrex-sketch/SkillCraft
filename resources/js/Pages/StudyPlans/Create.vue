<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Icon from '@/Components/Icon.vue'

const form = useForm({
    skill: '',
    level: 'beginner',
    hours_per_week: 5,
    weeks: 8,
    goals: '',
    learning_style: '',
    focus: '',
})

const step = ref(1)
const totalSteps = 4

function nextStep() {
    if (step.value < totalSteps) step.value++
}

function prevStep() {
    if (step.value > 1) step.value--
}

function submit() {
    form.post(route('study-plans.store'))
}

const canNext = computed(() => {
    if (step.value === 1) return !!form.skill
    return true
})

const popularSkills = [
    'Python', 'JavaScript', 'Desarrollo Web', 'Data Science', 'Diseno UX/UI',
    'React', 'Vue.js', 'Machine Learning', 'Ingles', 'Fotografia',
    'Marketing Digital', 'Excel Avanzado', 'Diseno Grafico', 'Blockchain',
]

const steps = [
    { num: 1, title: 'Habilidad', icon: 'target' },
    { num: 2, title: 'Nivel', icon: 'bar-chart' },
    { num: 3, title: 'Tiempo', icon: 'clock' },
    { num: 4, title: 'Meta', icon: 'flag' },
]
</script>

<template>
    <Head title="Crear Plan" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Crear nuevo plan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Deja que la IA disene el plan perfecto para ti
                </p>
            </div>
        </template>

        <div class="mx-auto max-w-3xl">
            <div class="rounded-xl border border-gray-200/60 bg-white/60 p-8 shadow-sm dark:border-gray-800/40 dark:bg-gray-900/40">
                <div class="mb-12">
                    <div class="mx-auto flex max-w-xl items-center justify-between">
                        <div
                            v-for="(s, i) in steps"
                            :key="s.num"
                            class="flex items-center"
                        >
                            <div class="flex flex-col items-center">
                                <div :class="[
                                    'flex h-9 w-9 items-center justify-center rounded-[10px] text-sm font-bold transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]',
                                    s.num < step ? 'bg-emerald-500 text-white' :
                                    s.num === step ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30' :
                                    'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500'
                                ]">
                                    <Icon v-if="s.num < step" name="check" :size="16" />
                                    <Icon v-else :name="s.icon" :size="16" />
                                </div>
                                <span :class="[
                                    'mt-1.5 text-xs font-medium transition-colors duration-300',
                                    s.num === step ? 'text-brand-600 dark:text-brand-400' :
                                    s.num < step ? 'text-emerald-600 dark:text-emerald-400' :
                                    'text-gray-400 dark:text-gray-500'
                                ]">{{ s.title }}</span>
                            </div>
                            <div
                                v-if="i < steps.length - 1"
                                :class="[
                                    'mx-3 h-[2px] w-16 sm:w-24 transition-colors duration-500',
                                    s.num < step ? 'bg-emerald-400' : 'bg-gray-200 dark:bg-gray-700'
                                ]"
                            />
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit">
                    <transition name="slide" mode="out-in">
                        <div v-if="step === 1" key="step1" class="min-h-[280px]">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Que quieres aprender?</h3>
                            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Escribe la habilidad o materia que deseas dominar</p>

                            <div class="relative mt-6">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <Icon name="search" :size="18" class="text-gray-400" />
                                </div>
                                <input
                                    v-model="form.skill"
                                    type="text"
                                    placeholder="Ej: Python, Diseno UX, Guitarra, Marketing..."
                                    class="w-full rounded-xl border-2 border-gray-200 bg-white/50 py-3.5 pl-11 pr-4 text-[15px] text-gray-900 transition-all placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-400"
                                />
                            </div>
                            <p v-if="form.errors.skill" class="mt-2 text-sm text-red-500">{{ form.errors.skill }}</p>

                            <div class="mt-6">
                                <p class="mb-3 text-sm font-medium text-gray-500 dark:text-gray-400">O elige una popular:</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="skill in popularSkills"
                                        :key="skill"
                                        type="button"
                                        @click="form.skill = skill"
                                        :class="[
                                            'rounded-full border px-3.5 py-1.5 text-sm font-medium transition-all duration-200',
                                            form.skill === skill
                                                ? 'border-brand-500 bg-brand-50 text-brand-700 dark:border-brand-600 dark:bg-brand-950 dark:text-brand-300'
                                                : 'border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:border-gray-600'
                                        ]"
                                    >
                                        {{ skill }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="step === 2" key="step2" class="min-h-[280px]">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Cual es tu nivel?</h3>
                            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Selecciona tu nivel actual en esta habilidad</p>

                            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                                <button
                                    type="button"
                                    :class="[
                                        'relative overflow-hidden rounded-xl border-2 p-6 text-center transition-all duration-200 hover:shadow-md',
                                        form.level === 'beginner'
                                            ? 'border-brand-500 bg-brand-50/50 dark:border-brand-600 dark:bg-brand-950/30'
                                            : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'
                                    ]"
                                    @click="form.level = 'beginner'"
                                >
                                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        <Icon name="target" :size="20" />
                                    </div>
                                    <p class="mt-3 font-semibold text-gray-900 dark:text-white">Principiante</p>
                                    <p class="mt-1 text-xs text-gray-500">Nunca lo he intentado</p>
                                </button>

                                <button
                                    type="button"
                                    :class="[
                                        'relative overflow-hidden rounded-xl border-2 p-6 text-center transition-all duration-200 hover:shadow-md',
                                        form.level === 'intermediate'
                                            ? 'border-brand-500 bg-brand-50/50 dark:border-brand-600 dark:bg-brand-950/30'
                                            : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'
                                    ]"
                                    @click="form.level = 'intermediate'"
                                >
                                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        <Icon name="trending-up" :size="20" />
                                    </div>
                                    <p class="mt-3 font-semibold text-gray-900 dark:text-white">Intermedio</p>
                                    <p class="mt-1 text-xs text-gray-500">Tengo bases solidas</p>
                                </button>

                                <button
                                    type="button"
                                    :class="[
                                        'relative overflow-hidden rounded-xl border-2 p-6 text-center transition-all duration-200 hover:shadow-md',
                                        form.level === 'advanced'
                                            ? 'border-brand-500 bg-brand-50/50 dark:border-brand-600 dark:bg-brand-950/30'
                                            : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'
                                    ]"
                                    @click="form.level = 'advanced'"
                                >
                                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        <Icon name="award" :size="20" />
                                    </div>
                                    <p class="mt-3 font-semibold text-gray-900 dark:text-white">Avanzado</p>
                                    <p class="mt-1 text-xs text-gray-500">Quiero profundizar</p>
                                </button>
                            </div>
                        </div>

                        <div v-if="step === 3" key="step3" class="min-h-[280px]">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Duracion y dedicacion</h3>
                            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Configura cuanto tiempo puedes dedicar</p>

                            <div class="mt-8 grid gap-8 sm:grid-cols-2">
                                <div>
                                    <label class="flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <span>Horas por semana</span>
                                        <span class="text-lg font-bold text-brand-600">{{ form.hours_per_week }}h</span>
                                    </label>
                                    <input
                                        type="range"
                                        min="1"
                                        max="40"
                                        v-model.number="form.hours_per_week"
                                        class="mt-3 h-1.5 w-full cursor-pointer appearance-none rounded-full bg-gray-200 accent-brand-600 dark:bg-gray-700"
                                    />
                                    <div class="mt-1 flex justify-between text-xs text-gray-400">
                                        <span>1h</span>
                                        <span>40h</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <span>Semanas de duracion</span>
                                        <span class="text-lg font-bold text-brand-600">{{ form.weeks }} sem</span>
                                    </label>
                                    <input
                                        type="range"
                                        min="2"
                                        max="24"
                                        v-model.number="form.weeks"
                                        class="mt-3 h-1.5 w-full cursor-pointer appearance-none rounded-full bg-gray-200 accent-brand-600 dark:bg-gray-700"
                                    />
                                    <div class="mt-1 flex justify-between text-xs text-gray-400">
                                        <span>2 sem</span>
                                        <span>24 sem</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 rounded-xl bg-brand-50/50 p-5 dark:bg-brand-950/30">
                                <div class="flex items-center gap-3.5">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-100 text-brand-600 dark:bg-brand-900 dark:text-brand-400">
                                        <Icon name="clock" :size="20" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-brand-900 dark:text-brand-100">Total estimado: <strong>{{ form.hours_per_week * form.weeks }} horas</strong> de estudio</p>
                                        <p class="text-xs text-brand-600 dark:text-brand-300">{{ Math.ceil((form.hours_per_week * form.weeks) / 4) }} dias de aprendizaje enfocado</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="step === 4" key="step4" class="min-h-[280px]">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tu meta personal</h3>
                            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Cuentanos mas para personalizar tu plan al maximo</p>

                            <div class="mt-6 space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cual es tu objetivo final?</label>
                                    <textarea
                                        v-model="form.goals"
                                        rows="3"
                                        placeholder="Quiero crear mi propia app web, conseguir trabajo como data scientist, hacer mi portfolio..."
                                        class="mt-1.5 w-full rounded-xl border border-gray-200 bg-white/50 p-3.5 text-[15px] text-gray-900 transition-all placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-400"
                                    />
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estilo de aprendizaje</label>
                                        <select
                                            v-model="form.learning_style"
                                            class="mt-1.5 w-full rounded-xl border border-gray-200 bg-white/50 p-3.5 text-[15px] text-gray-900 transition-all focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white"
                                        >
                                            <option value="">Selecciona tu estilo</option>
                                            <option value="visual">Visual (videos, diagramas)</option>
                                            <option value="auditivo">Auditivo (podcasts, explicaciones)</option>
                                            <option value="lectura">Lectura (articulos, libros)</option>
                                            <option value="practico">Practico (ejercicios, proyectos)</option>
                                            <option value="mixto">Mixto (combinacion)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Area de enfoque (opcional)</label>
                                        <input
                                            v-model="form.focus"
                                            type="text"
                                            placeholder="Ej: desarrollo web, analisis de datos..."
                                            class="mt-1.5 w-full rounded-xl border border-gray-200 bg-white/50 p-3.5 text-[15px] text-gray-900 transition-all placeholder:text-gray-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-400"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <div class="mt-10 flex items-center justify-between border-t border-gray-100 pt-6 dark:border-gray-800/50">
                        <button
                            v-if="step > 1"
                            type="button"
                            @click="prevStep"
                            class="btn-secondary px-5 py-2.5 text-sm"
                        >
                            <Icon name="arrow-left" :size="16" />
                            Anterior
                        </button>
                        <div v-else />

                        <template v-if="step < totalSteps">
                            <button
                                type="button"
                                @click="nextStep"
                                :disabled="!canNext"
                                class="btn-primary px-6 py-2.5 text-sm disabled:opacity-50"
                            >
                                Siguiente
                                <Icon name="arrow-right" :size="16" />
                            </button>
                        </template>

                        <template v-else>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="btn-primary relative overflow-hidden px-8 py-2.5 text-sm"
                            >
                                <span v-if="form.processing" class="flex items-center gap-2">
                                    <Icon name="loader" :size="16" class="animate-spin" />
                                    Generando plan con IA...
                                </span>
                                <span v-else class="flex items-center gap-2">
                                    <Icon name="sparkle" :size="16" />
                                    Generar mi plan
                                </span>
                            </button>
                        </template>
                    </div>
                </form>

                <transition name="slide">
                    <div v-if="form.recentlySuccessful" class="mt-4 flex items-center gap-2 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300">
                        <Icon name="check-circle" :size="16" />
                        Plan creado exitosamente. Redirigiendo...
                    </div>
                </transition>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.35s ease;
}
.slide-enter-from {
    opacity: 0;
    transform: translateX(24px);
}
.slide-leave-to {
    opacity: 0;
    transform: translateX(-24px);
}
</style>
