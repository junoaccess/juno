import { defineConfig } from 'vitepress';

export default defineConfig({
    title: 'Juno',
    description: 'Opinionated access management system for modern applications',
    base: '/docs/',

    themeConfig: {
        logo: '/logo.svg',

        nav: [
            { text: 'Guide', link: '/guide/getting-started' },
            { text: 'Architecture', link: '/guide/architecture' },
            { text: 'API', link: '/api/overview' },
            { text: 'GitHub', link: 'https://github.com/usejuno/juno' },
        ],

        sidebar: {
            '/guide/': [
                {
                    text: 'Introduction',
                    items: [
                        { text: 'What is Juno?', link: '/guide/introduction' },
                        {
                            text: 'Getting Started',
                            link: '/guide/getting-started',
                        },
                        { text: 'Installation', link: '/guide/installation' },
                    ],
                },
                {
                    text: 'Architecture',
                    items: [
                        { text: 'Overview', link: '/guide/architecture' },
                        {
                            text: 'Backend Structure',
                            link: '/guide/backend-structure',
                        },
                        {
                            text: 'Frontend Structure',
                            link: '/guide/frontend-structure',
                        },
                    ],
                },
                {
                    text: 'Core Concepts',
                    items: [
                        { text: 'Organisations', link: '/guide/organisations' },
                        {
                            text: 'Roles & Permissions',
                            link: '/guide/roles-and-permissions',
                        },
                        { text: 'Teams', link: '/guide/teams' },
                        { text: 'Invitations', link: '/guide/invitations' },
                    ],
                },
                {
                    text: 'Development',
                    items: [
                        {
                            text: 'Local Development',
                            link: '/guide/local-development',
                        },
                        { text: 'Testing', link: '/guide/testing' },
                        { text: 'Code Style', link: '/guide/code-style' },
                    ],
                },
            ],
            '/api/': [
                {
                    text: 'API Reference',
                    items: [
                        { text: 'Overview', link: '/api/overview' },
                        { text: 'Authentication', link: '/api/authentication' },
                        { text: 'Users', link: '/api/users' },
                        { text: 'Organisations', link: '/api/organisations' },
                        { text: 'Teams', link: '/api/teams' },
                        { text: 'Roles', link: '/api/roles' },
                        { text: 'Permissions', link: '/api/permissions' },
                        { text: 'Invitations', link: '/api/invitations' },
                    ],
                },
            ],
        },

        socialLinks: [
            { icon: 'github', link: 'https://github.com/usejuno/juno' },
        ],

        search: {
            provider: 'local',
        },

        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2025 Juno Contributors',
        },

        editLink: {
            pattern: 'https://github.com/usejuno/juno/edit/main/docs/:path',
            text: 'Edit this page on GitHub',
        },
    },

    head: [
        [
            'link',
            { rel: 'icon', type: 'image/svg+xml', href: '/docs/logo.svg' },
        ],
        ['meta', { name: 'theme-color', content: '#3b82f6' }],
        ['meta', { name: 'og:type', content: 'website' }],
        ['meta', { name: 'og:locale', content: 'en' }],
        ['meta', { name: 'og:site_name', content: 'Juno' }],
    ],
});
