import DefaultTheme from 'vitepress/theme'
import { h, nextTick, onMounted, watch } from 'vue'
import { useRoute } from 'vitepress'
import mediumZoom from 'medium-zoom'
import './custom.css'

export default {
  extends: DefaultTheme,
  Layout() {
    return h(DefaultTheme.Layout, null, {
      'home-hero-info-before': () =>
        h('p', { class: 'hero-logo' }, [
          h('img', { class: 'logo-light', src: '/logo.png', alt: 'WPMVC' }),
          h('img', { class: 'logo-dark', src: '/logo-dark.png', alt: 'WPMVC' }),
        ]),
    })
  },
  setup() {
    const route = useRoute()

    // Click-to-zoom (gallery-style lightbox) for the debugger screenshots.
    // Scoped to /debug/ images so linked images elsewhere keep navigating.
    // Re-attached on route change; detached first so images never end up
    // with two zoom instances.
    let zoom: ReturnType<typeof mediumZoom> | undefined

    const initZoom = () => {
      zoom?.detach()
      zoom = mediumZoom('.vp-doc img[src^="/debug/"]', {
        background: 'var(--vp-c-bg)',
        margin: 24,
      })

      // medium-zoom enlarges via transform: scale(), which scales the CSS
      // border-radius along with the image — compensate once the zoom-in
      // finishes so the opened image keeps a visual 8px radius.
      zoom.on('opened', (event) => {
        const img = event.target as HTMLImageElement
        const scale = img.getBoundingClientRect().width / img.offsetWidth

        if (scale > 0) {
          img.style.borderRadius = `${8 / scale}px`
        }
      })

      zoom.on('close', (event) => {
        ;(event.target as HTMLImageElement).style.borderRadius = ''
      })
    }

    onMounted(initZoom)
    watch(
      () => route.path,
      () => nextTick(initZoom),
    )
  },
}
