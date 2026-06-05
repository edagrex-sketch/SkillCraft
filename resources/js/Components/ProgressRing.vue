<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
    percent: number
    size?: number
    strokeWidth?: number
    wrapperClass?: string
}>(), {
    size: 80,
    strokeWidth: 6,
    wrapperClass: '',
})

const radius = computed(() => (props.size - props.strokeWidth) / 2)
const circumference = computed(() => 2 * Math.PI * radius.value)
const offset = computed(() => circumference.value - (props.percent / 100) * circumference.value)

const color = computed(() => {
    if (props.percent >= 80) return '#10b981'
    if (props.percent >= 40) return '#4F46E5'
    return '#f59e0b'
})
</script>

<template>
    <div :class="['relative inline-flex items-center justify-center', wrapperClass]">
        <svg :width="size" :height="size" class="-rotate-90">
            <circle
                :cx="size / 2"
                :cy="size / 2"
                :r="radius"
                fill="none"
                :stroke-width="strokeWidth"
                class="stroke-gray-100 dark:stroke-gray-800"
            />
            <circle
                :cx="size / 2"
                :cy="size / 2"
                :r="radius"
                fill="none"
                :stroke-width="strokeWidth"
                :stroke="color"
                :stroke-dasharray="circumference"
                :stroke-dashoffset="offset"
                stroke-linecap="round"
                class="transition-all duration-1000 ease-out-expo"
            />
        </svg>
        <span class="absolute text-sm font-bold" :style="{ color }">{{ percent }}%</span>
    </div>
</template>
