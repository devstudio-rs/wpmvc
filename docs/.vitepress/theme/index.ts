import DefaultTheme from 'vitepress/theme'
import { h } from 'vue'
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
}
