<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });

const props = withDefaults(defineProps<{
    type?: string
    placeholder?: string
    icon?: string
    error?: string
}>(), {
    type: 'text',
    placeholder: '',
    icon: '',
    error: '',
})
</script>

<template>
    <div class="relative">
        <div v-if="icon" class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
            <Icon :name="icon" :size="16" class="text-gray-400 transition-colors duration-200" :class="error ? 'text-red-400' : model ? 'text-brand-600 dark:text-brand-400' : ''" />
        </div>
        <input
            :type="type"
            :placeholder="placeholder"
            :class="[
                'block w-full rounded-xl border bg-white/50 py-2.5 text-[15px] text-gray-900 transition-all duration-200 placeholder:text-gray-400 focus:outline-none focus:ring-4 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500',
                icon ? 'pl-10 pr-4' : 'px-4',
                error
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10 dark:border-red-800 dark:focus:border-red-400'
                    : 'border-gray-200 focus:border-brand-500 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-400'
            ]"
            v-model="model"
            ref="input"
        />
    </div>
</template>
