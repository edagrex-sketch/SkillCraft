<script setup lang="ts">
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps<{
    checked: boolean;
    value?: any;
}>();

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="h-4 w-4 rounded border-gray-300 text-brand-600 shadow-sm transition-all duration-150 focus:ring-4 focus:ring-brand-500/20 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-800 dark:checked:bg-brand-600 dark:focus:ring-brand-500/20"
    />
</template>
