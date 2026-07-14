import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'WPMVC',
  description: 'An MVC framework for WordPress.',
  cleanUrls: true,
  lastUpdated: true,

  sitemap: {
    hostname: 'https://wpmvc.devstudio.rs',
  },

  themeConfig: {
    nav: [
      { text: 'Guide', link: '/guide/getting-started' },
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
