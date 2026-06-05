<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Icon from '@/Components/Icon.vue'

defineProps<{
    canResetPassword?: boolean
    status?: string
}>()

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const showPassword = ref(false)

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password')
        },
    })
}
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesion" />

        <div class="relative">
            <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Bienvenido de vuelta</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ingresa tus credenciales para continuar</p>
        </div>

        <div v-if="status" class="mt-5 flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300 animate-fade-in-down">
            <Icon name="check-circle" :size="16" />
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="mt-6 space-y-5">
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Correo electronico</label>
                <TextInput
                    id="email"
                    type="email"
                    placeholder="tu@correo.com"
                    v-model="form.email"
                    icon="mail"
                    :error="form.errors.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Contrasena</label>
                <div class="relative">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Tu contrasena"
                        v-model="form.password"
                        icon="lock"
                        :error="form.errors.password"
                        required
                        autocomplete="current-password"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <Icon :name="showPassword ? 'eye-off' : 'eye'" :size="16" />
                    </button>
                </div>
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex cursor-pointer items-center gap-2">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="h-4 w-4 rounded border-gray-300 text-brand-600 shadow-sm transition-all duration-150 focus:ring-4 focus:ring-brand-500/20 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-800 dark:checked:bg-brand-600 dark:focus:ring-brand-500/20"
                    />
                    <span class="text-sm text-gray-600 dark:text-gray-400">Recordarme</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-brand-600 transition-colors hover:text-brand-500 dark:text-brand-400"
                >
                    Olvidaste tu contrasena?
                </Link>
            </div>

            <div class="space-y-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    :class="[
                        'flex w-full items-center justify-center gap-2 rounded-xl border border-transparent px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-200 ease-out-expo focus:outline-none focus:ring-4 focus:ring-brand-500/20 active:scale-[0.97]',
                        form.processing
                            ? 'cursor-not-allowed bg-brand-400'
                            : 'bg-gradient-to-b from-brand-600 to-brand-700 shadow-brand-600/25 hover:from-brand-500 hover:to-brand-600 hover:shadow-xl hover:shadow-brand-600/30'
                    ]"
                >
                    <Icon v-if="form.processing" name="loader" :size="16" class="animate-spin" />
                    <Icon v-else name="sparkle" :size="16" />
                    {{ form.processing ? 'Iniciando sesion...' : 'Iniciar sesion' }}
                </button>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700" />
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-white/70 px-3 text-gray-400 dark:bg-gray-900/70 dark:text-gray-500">O continua con</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" class="flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white/50 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-white hover:shadow-md dark:border-gray-700 dark:bg-gray-800/50 dark:text-gray-300 dark:hover:bg-gray-800">
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4" />
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                    </svg>
                    Google
                </button>
                <button type="button" class="flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white/50 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-white hover:shadow-md dark:border-gray-700 dark:bg-gray-800/50 dark:text-gray-300 dark:hover:bg-gray-800">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 21.795 24 17.295 24 12 24 5.37 18.63 0 12 0z" />
                    </svg>
                    GitHub
                </button>
            </div>

            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                No tienes cuenta?
                <Link :href="route('register')" class="font-medium text-brand-600 transition-colors hover:text-brand-500 dark:text-brand-400">
                    Registrate
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
