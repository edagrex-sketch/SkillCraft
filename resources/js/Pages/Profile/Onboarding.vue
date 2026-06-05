<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const form = useForm({
    learning_style: '',
    time_availability_minutes: 60,
    goals: '',
    experience_level: 'beginner',
})

const step = ref(1)
const totalSteps = 4

function nextStep() {
    if (step.value < totalSteps) step.value++
}

function prevStep() {
    if (step.value > 1) step.value--
}

const learningStyles = [
    { value: 'visual', label: 'Visual', desc: 'Aprendo mejor con imágenes, diagramas y videos' },
    { value: 'auditivo', label: 'Auditivo', desc: 'Aprendo mejor escuchando explicaciones y podcasts' },
    { value: 'lectura', label: 'Lectura', desc: 'Aprendo mejor leyendo textos y documentación' },
    { value: 'kinestesico', label: 'Kinestésico', desc: 'Aprendo mejor haciendo y moviéndome' },
    { value: 'practico', label: 'Práctico', desc: 'Aprendo mejor resolviendo ejercicios y proyectos' },
]

function submit() {
    form.post(route('onboarding.store'))
}
</script>

<template>
    <Head title="Configura tu perfil" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Configura tu experiencia de aprendizaje
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="p-8">
                        <div class="mb-8 flex items-center justify-between">
                            <div v-for="i in totalSteps" :key="i" class="flex items-center">
                                <div :class="['flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium', i <= step ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500 dark:bg-gray-600']">
                                    {{ i }}
                                </div>
                                <div v-if="i < totalSteps" class="mx-2 h-1 w-12 rounded" :class="i < step ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600'"></div>
                            </div>
                        </div>

                        <form @submit.prevent="submit">
                            <div v-show="step === 1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">¿Cuál es tu estilo de aprendizaje?</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecciona el que más te describa</p>
                                <div class="mt-6 grid grid-cols-1 gap-4">
                                    <button
                                        v-for="style in learningStyles"
                                        :key="style.value"
                                        type="button"
                                        :class="['rounded-lg border-2 p-4 text-left transition', form.learning_style === style.value ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 hover:border-gray-300 dark:border-gray-600']"
                                        @click="form.learning_style = style.value"
                                    >
                                        <p class="font-medium text-gray-900 dark:text-white">{{ style.label }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ style.desc }}</p>
                                    </button>
                                </div>
                                <p v-if="form.errors.learning_style" class="mt-2 text-sm text-red-600">{{ form.errors.learning_style }}</p>
                            </div>

                            <div v-show="step === 2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">¿Cuánto tiempo puedes dedicar al día?</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Elige los minutos que puedes estudiar diariamente</p>
                                <div class="mt-6">
                                    <input type="range" min="15" max="480" step="15" v-model="form.time_availability_minutes" class="w-full accent-indigo-600" />
                                    <p class="mt-4 text-center text-3xl font-bold text-indigo-600">{{ form.time_availability_minutes }} minutos/día</p>
                                </div>
                            </div>

                            <div v-show="step === 3">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">¿Cuál es tu nivel de experiencia?</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecciona tu nivel general en aprendizaje</p>
                                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <button
                                        type="button"
                                        :class="['rounded-lg border-2 p-4 text-center transition', form.experience_level === 'beginner' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 hover:border-gray-300 dark:border-gray-600']"
                                        @click="form.experience_level = 'beginner'"
                                    >
                                        <p class="text-2xl">🌱</p>
                                        <p class="mt-2 font-medium text-gray-900 dark:text-white">Principiante</p>
                                        <p class="text-sm text-gray-500">Nunca he estudiado esto</p>
                                    </button>
                                    <button
                                        type="button"
                                        :class="['rounded-lg border-2 p-4 text-center transition', form.experience_level === 'intermediate' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 hover:border-gray-300 dark:border-gray-600']"
                                        @click="form.experience_level = 'intermediate'"
                                    >
                                        <p class="text-2xl">🚀</p>
                                        <p class="mt-2 font-medium text-gray-900 dark:text-white">Intermedio</p>
                                        <p class="text-sm text-gray-500">Tengo algo de experiencia</p>
                                    </button>
                                    <button
                                        type="button"
                                        :class="['rounded-lg border-2 p-4 text-center transition', form.experience_level === 'advanced' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 hover:border-gray-300 dark:border-gray-600']"
                                        @click="form.experience_level = 'advanced'"
                                    >
                                        <p class="text-2xl">🏆</p>
                                        <p class="mt-2 font-medium text-gray-900 dark:text-white">Avanzado</p>
                                        <p class="text-sm text-gray-500">Ya tengo conocimientos sólidos</p>
                                    </button>
                                </div>
                            </div>

                            <div v-show="step === 4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">¿Cuáles son tus metas?</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cuéntanos qué quieres lograr con SkillCraft</p>
                                <div class="mt-6">
                                    <textarea
                                        v-model="form.goals"
                                        rows="4"
                                        placeholder="Quiero aprender programación para crear mi propia app, mejorar mis habilidades en diseño UX, dominar Python para data science..."
                                        class="w-full rounded-lg border border-gray-300 p-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    ></textarea>
                                    <p v-if="form.errors.goals" class="mt-2 text-sm text-red-600">{{ form.errors.goals }}</p>
                                </div>
                            </div>

                            <div class="mt-8 flex items-center justify-between">
                                <button
                                    v-if="step > 1"
                                    type="button"
                                    @click="prevStep"
                                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300"
                                >
                                    Anterior
                                </button>
                                <div v-else></div>

                                <button
                                    v-if="step < totalSteps"
                                    type="button"
                                    @click="nextStep"
                                    class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                >
                                    Siguiente
                                </button>
                                <button
                                    v-else
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Guardando...' : 'Finalizar' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
