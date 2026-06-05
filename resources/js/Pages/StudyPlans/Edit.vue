<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps<{
    plan: any
}>()

const form = useForm({
    title: props.plan.title,
    description: props.plan.description || '',
    hours_per_week: props.plan.hours_per_week,
})

function submit() {
    form.put(route('study-plans.update', props.plan.id))
}
</script>

<template>
    <Head :title="'Editar: ' + plan.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('study-plans.show', plan.id)" class="group inline-flex items-center gap-1 text-sm font-medium text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <Icon name="arrow-left" :size="14" class="transition-transform duration-200 group-hover:-translate-x-0.5" />
                        Volver al plan
                    </Link>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Editar plan</h1>
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-2xl">
            <div class="rounded-xl border border-gray-200/60 bg-white/60 p-8 shadow-sm dark:border-gray-800/40 dark:bg-gray-900/40">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titulo del plan</label>
                        <input
                            v-model="form.title"
                            type="text"
                            class="input-premium mt-1.5"
                        />
                        <p v-if="form.errors.title" class="mt-1.5 text-sm text-red-500">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripcion</label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="input-premium mt-1.5"
                        />
                        <p v-if="form.errors.description" class="mt-1.5 text-sm text-red-500">{{ form.errors.description }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Horas por semana</label>
                        <div class="mt-1.5 flex items-center gap-4">
                            <input
                                type="range"
                                min="1"
                                max="40"
                                v-model.number="form.hours_per_week"
                                class="h-1.5 w-full cursor-pointer appearance-none rounded-full bg-gray-200 accent-brand-600 dark:bg-gray-700"
                            />
                            <span class="text-lg font-bold text-brand-600 w-12 text-right">{{ form.hours_per_week }}h</span>
                        </div>
                        <p v-if="form.errors.hours_per_week" class="mt-1.5 text-sm text-red-500">{{ form.errors.hours_per_week }}</p>
                    </div>

                    <div class="flex items-center gap-3 border-t border-gray-100 pt-6 dark:border-gray-800/50">
                        <button type="submit" :disabled="form.processing" class="btn-primary px-6 py-2.5 text-sm">
                            <Icon v-if="form.processing" name="loader" :size="16" class="animate-spin" />
                            <Icon v-else name="check" :size="16" />
                            Guardar cambios
                        </button>
                        <Link :href="route('study-plans.show', plan.id)" class="btn-secondary px-5 py-2.5 text-sm">
                            Cancelar
                        </Link>
                    </div>
                </form>

                <div v-if="($page.props as any).flash?.success" class="mt-4 flex items-center gap-2 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300">
                    <Icon name="check-circle" :size="16" />
                    {{ ($page.props as any).flash.success }}
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
