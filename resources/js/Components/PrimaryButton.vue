<template>
    <button
        :class="[
            'inline-flex items-center justify-center gap-2 rounded-xl border border-transparent px-5 py-2.5 text-sm font-semibold text-white transition-all duration-200 ease-out-expo focus:outline-none focus:ring-4 focus:ring-brand-500/20 active:scale-[0.97]',
            props.disabled
                ? 'cursor-not-allowed bg-gray-400 dark:bg-gray-600'
                : 'bg-gradient-to-b from-brand-600 to-brand-700 shadow-lg shadow-brand-600/25 hover:from-brand-500 hover:to-brand-600 hover:shadow-xl hover:shadow-brand-600/30',
        ]"
        @click="$emit('click')"
    >
        <slot />
    </button>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    disabled?: boolean
}>(), {
    disabled: false,
})

defineEmits<{
    click: []
}>()
</script>
