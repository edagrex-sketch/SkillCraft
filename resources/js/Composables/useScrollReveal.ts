import { ref, onMounted, onUnmounted } from 'vue'

export function useScrollReveal(options = { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }) {
    const el = ref<HTMLElement | null>(null)
    const isVisible = ref(false)

    let observer: IntersectionObserver | null = null

    onMounted(() => {
        if (!el.value) return

        observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    isVisible.value = true
                    observer?.unobserve(entry.target)
                }
            },
            { threshold: options.threshold, rootMargin: options.rootMargin }
        )

        observer.observe(el.value)
    })

    onUnmounted(() => {
        if (observer && el.value) {
            observer.unobserve(el.value)
        }
    })

    return { el, isVisible }
}
