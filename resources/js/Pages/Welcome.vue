<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import Icon from '@/Components/Icon.vue'

defineProps<{
    canLogin?: boolean
    canRegister?: boolean
}>()

const features = [
    {
        icon: 'brain',
        title: 'IA Adaptativa',
        desc: 'Planes generados por Groq AI que se adaptan a tu nivel y estilo de aprendizaje'
    },
    {
        icon: 'code',
        title: 'Aprende Haciendo',
        desc: '80% practica, 20% teoria. Ejercicios progresivos que realmente funcionan'
    },
    {
        icon: 'trending-up',
        title: 'Progreso Real',
        desc: 'Seguimiento detallado de tu avance con estadisticas y metricas'
    },
    {
        icon: 'target',
        title: 'Metas Claras',
        desc: 'Planes estructurados por semanas con objetivos especificos y alcanzables'
    },
    {
        icon: 'layers',
        title: 'Proyectos Reales',
        desc: 'Construye proyectos desde el dia 1 mientras aprendes nuevas habilidades'
    },
    {
        icon: 'zap',
        title: 'Rapido y Eficiente',
        desc: 'Estudia a tu ritmo con sesiones optimizadas para maxima retencion'
    },
]

const isVisible = ref(false)

onMounted(() => {
    setTimeout(() => isVisible.value = true, 100)
})
</script>

<template>
    <Head title="SkillCraft - Aprendizaje Inteligente" />

    <div class="relative min-h-screen overflow-hidden bg-[#f8f8fc] dark:bg-[#0a0a14]">
        <div class="pointer-events-none fixed inset-0 bg-dot opacity-60" />

        <div class="pointer-events-none fixed -top-[30rem] -right-[20rem] h-[600px] w-[600px] rounded-full bg-brand-500/[0.06] blur-[120px]" />
        <div class="pointer-events-none fixed -bottom-[30rem] -left-[20rem] h-[600px] w-[600px] rounded-full bg-purple-500/[0.06] blur-[120px]" />

        <nav class="relative z-10 border-b border-gray-200/50 bg-white/60 backdrop-blur-2xl dark:border-gray-800/30 dark:bg-[#0a0a14]/60">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">
                        S
                    </div>
                    <span class="text-[15px] font-semibold tracking-tight text-gray-900 dark:text-white">SkillCraft</span>
                </Link>

                <div v-if="canLogin" class="flex items-center gap-3">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="btn-primary text-sm">
                        <Icon name="dashboard" :size="16" />
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="btn-ghost text-sm font-medium">
                            Iniciar sesion
                        </Link>
                        <Link :href="route('register')" class="btn-primary text-sm">
                            <Icon name="user" :size="16" />
                            Crear cuenta
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <main class="relative z-10">
            <section class="relative overflow-hidden px-4 pt-24 pb-40 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-5xl text-center">
                    <div
                        :class="['transition-all duration-[800ms] ease-[cubic-bezier(0.16,1,0.3,1)]', isVisible ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0']"
                    >
                        <div class="inline-flex items-center gap-1.5 rounded-full border border-brand-200/50 bg-brand-50/80 px-3.5 py-1.5 text-sm font-medium text-brand-700 backdrop-blur-sm dark:border-brand-800/50 dark:bg-brand-950/50 dark:text-brand-300">
                            <Icon name="sparkle" :size="14" class="text-brand-500" />
                            Aprendizaje impulsado por IA
                        </div>

                        <h1 class="mt-8 text-balance text-5xl font-extrabold leading-[1.08] tracking-tight text-gray-900 sm:text-6xl lg:text-7xl xl:text-8xl dark:text-white">
                            Aprende cualquier
                            <br />
                            <span class="bg-gradient-to-r from-brand-600 via-purple-600 to-pink-500 bg-clip-text text-transparent">habilidad</span>
                            con un plan inteligente
                        </h1>

                        <p class="mx-auto mt-6 max-w-2xl text-balance text-[17px] leading-relaxed text-gray-500 dark:text-gray-400">
                            SkillCraft usa inteligencia artificial para crear planes de estudio personalizados
                            basados en proyectos practicos. Domina cualquier habilidad con ejercicios
                            progresivos adaptados a tu nivel.
                        </p>

                        <div class="mt-10 flex items-center justify-center gap-4">
                            <Link
                                :href="route('register')"
                                class="group relative inline-flex items-center gap-2 rounded-xl bg-brand-600 px-8 py-3.5 text-[15px] font-semibold text-white shadow-lg shadow-brand-600/25 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-600/30 active:scale-[0.97]"
                            >
                                Comenzar gratis
                                <Icon name="arrow-right" :size="18" class="transition-transform duration-300 group-hover:translate-x-1" />
                            </Link>
                            <Link
                                :href="route('login')"
                                class="btn-secondary px-8 py-3.5 text-[15px]"
                            >
                                <Icon name="play" :size="16" />
                                Ver demo
                            </Link>
                        </div>

                        <div class="mt-12 flex items-center justify-center gap-8 text-sm text-gray-400 dark:text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <Icon name="zap" :size="14" />
                                Impulsado por Groq AI
                            </span>
                            <span class="flex items-center gap-1.5">
                                <Icon name="shield" :size="14" />
                                Sin compromisos
                            </span>
                            <span class="flex items-center gap-1.5">
                                <Icon name="check-circle" :size="14" />
                                Planes comprobados
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="relative px-4 pb-40 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <div class="text-center">
                        <h2 class="text-[32px] font-bold tracking-tight text-gray-900 dark:text-white">
                            Por que SkillCraft?
                        </h2>
                        <p class="mt-3 text-[15px] text-gray-500 dark:text-gray-400">
                            Mas que una plataforma de cursos, tu coach personal de aprendizaje
                        </p>
                    </div>

                    <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                            class="group relative rounded-2xl border border-gray-200/60 bg-white/60 p-8 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-0.5 hover:border-brand-200/60 hover:shadow-lg hover:shadow-brand-500/5 dark:border-gray-800/40 dark:bg-gray-900/40 dark:hover:border-brand-800/40 dark:hover:shadow-brand-500/10"
                        >
                            <div class="pointer-events-none absolute inset-0 rounded-2xl bg-gradient-to-b from-brand-500/[0.02] to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100" />
                            <div class="relative">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition-colors duration-300 group-hover:bg-brand-100 dark:bg-brand-950 dark:text-brand-400 dark:group-hover:bg-brand-900/50">
                                    <Icon :name="feature.icon" :size="20" />
                                </div>
                                <h3 class="mt-5 text-[17px] font-semibold text-gray-900 dark:text-white">{{ feature.title }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ feature.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="relative px-4 pb-40 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-5xl">
                    <div class="relative overflow-hidden rounded-3xl bg-brand-600 px-12 py-16 shadow-2xl sm:px-16 sm:py-20">
                        <div class="pointer-events-none absolute inset-0 bg-dot opacity-[0.08]" />
                        <div class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-white/[0.06] blur-3xl" />
                        <div class="relative">
                            <h2 class="text-[32px] font-bold tracking-tight text-white sm:text-4xl">
                                Listo para empezar tu viaje?
                            </h2>
                            <p class="mx-auto mt-3 max-w-xl text-balance text-[17px] leading-relaxed text-brand-100">
                                Unete a SkillCraft y deja que la IA disene el plan perfecto para ti
                            </p>
                            <div class="mt-8 flex items-center gap-4">
                                <Link
                                    :href="route('register')"
                                    class="group inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-[15px] font-semibold text-brand-700 shadow-lg transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] hover:bg-brand-50 hover:shadow-xl active:scale-[0.97]"
                                >
                                    Crear mi cuenta gratis
                                    <Icon name="arrow-right" :size="18" class="transition-transform duration-300 group-hover:translate-x-1" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="relative border-t border-gray-200/50 bg-white/40 py-12 backdrop-blur-sm dark:border-gray-800/30 dark:bg-[#0a0a14]/40">
            <div class="mx-auto max-w-7xl px-4 text-center text-sm text-gray-400 dark:text-gray-500">
                <p>&copy; 2026 SkillCraft.</p>
            </div>
        </footer>
    </div>
</template>
