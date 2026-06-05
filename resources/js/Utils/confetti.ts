const colors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899']

export function fireConfetti(origin: HTMLElement | { x: number; y: number } | null = null) {
    const rect = origin
        ? origin instanceof HTMLElement
            ? origin.getBoundingClientRect()
            : { left: origin.x, top: origin.y, width: 0, height: 0 }
        : { left: window.innerWidth / 2, top: window.innerHeight / 2, width: 0, height: 0 }

    const count = 30

    for (let i = 0; i < count; i++) {
        const particle = document.createElement('div')
        particle.className = 'confetti-particle'
        particle.style.left = `${rect.left + rect.width / 2 + (Math.random() - 0.5) * 40}px`
        particle.style.top = `${rect.top + rect.height / 2}px`
        particle.style.background = colors[Math.floor(Math.random() * colors.length)]
        particle.style.width = `${4 + Math.random() * 6}px`
        particle.style.height = `${4 + Math.random() * 6}px`
        particle.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px'
        particle.style.animationDuration = `${1 + Math.random() * 0.8}s`
        particle.style.transform = `rotate(${Math.random() * 360}deg)`

        const angle = (Math.PI * 2 * i) / count + (Math.random() - 0.5) * 0.5
        const velocity = 80 + Math.random() * 120
        const vx = Math.cos(angle) * velocity
        const vy = Math.sin(angle) * velocity - 80

        document.body.appendChild(particle)

        const startTime = performance.now()
        const duration = 1200 + Math.random() * 400

        function animate(time: number) {
            const elapsed = time - startTime
            const progress = elapsed / duration
            if (progress >= 1) {
                particle.remove()
                return
            }
            const x = vx * progress
            const y = vy * progress + 0.5 * 400 * progress * progress
            particle.style.transform = `translate(${x}px, ${y}px) rotate(${progress * 360}deg)`
            particle.style.opacity = String(1 - progress)
            requestAnimationFrame(animate)
        }
        requestAnimationFrame(animate)
    }
}
