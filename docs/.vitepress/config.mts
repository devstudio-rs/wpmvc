import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'WPMVC',
  description: 'A super-light, component-based MVC framework for WordPress.',
  cleanUrls: true,
  lastUpdated: true,
  appearance: 'dark',

  head: [
    ['meta', { name: 'google-site-verification', content: 'qKeiA8balNmaVTx5CfOlPRiTMuHerlun72W3Wdis0qw' }],
    ['link', { rel: 'icon', href: '/favicon.ico', sizes: '48x48' }],
    ['link', { rel: 'icon', type: 'image/png', sizes: '32x32', href: '/favicon-32.png' }],
    ['link', { rel: 'apple-touch-icon', sizes: '180x180', href: '/apple-touch-icon.png' }],
  ],

  sitemap: {
    hostname: 'https://wpmvc.devstudio.rs',
  },

  themeConfig: {
    logo: {
      light: '/logo.png',
      dark: '/logo-dark.png',
      alt: 'WPMVC',
    },
    siteTitle: false,

    nav: [
      { text: 'Guide', link: '/guide/getting-started' },
      { text: 'Debug', link: '/guide/debug' },
      { text: 'Changelog', link: '/changelog' },
    ],

    sidebar: [
      {
        text: 'Introduction',
        items: [
          { text: 'Getting Started', link: '/guide/getting-started' },
        ],
      },
      {
        text: 'Core Concepts',
        items: [
          { text: 'Applications', link: '/guide/applications' },
          { text: 'Components', link: '/guide/components' },
          { text: 'Aliases', link: '/guide/aliases' },
        ],
      },
      {
        text: 'Handling Requests',
        items: [
          { text: 'Routing', link: '/guide/routing' },
          { text: 'Controllers', link: '/guide/controllers' },
          { text: 'Views', link: '/guide/views' },
        ],
      },
      {
        text: 'Working with Data',
        items: [
          { text: 'Post Models', link: '/guide/models' },
          { text: 'Taxonomy Models', link: '/guide/taxonomies' },
          { text: 'Meta Boxes', link: '/guide/meta-boxes' },
          { text: 'Options', link: '/guide/options' },
        ],
      },
      {
        text: 'Utilities',
        items: [
          { text: 'Current User', link: '/guide/user' },
          { text: 'Logger', link: '/guide/logger' },
          { text: 'Form & Html Helpers', link: '/guide/helpers' },
        ],
      },
      {
        text: 'Tooling',
        items: [
          { text: 'WPMVC Debug', link: '/guide/debug' },
        ],
      },
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/devstudio-rs/wpmvc' },
    ],

    search: {
      provider: 'local',
    },

    outline: [2, 3],
  },
})
