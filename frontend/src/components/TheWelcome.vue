<script setup lang="ts">
import WelcomeItem from './WelcomeItem.vue'
import DocumentationIcon from './icons/IconDocumentation.vue'
import ToolingIcon from './icons/IconTooling.vue'
import EcosystemIcon from './icons/IconEcosystem.vue'
import CommunityIcon from './icons/IconCommunity.vue'
import SupportIcon from './icons/IconSupport.vue'

const openReadmeInEditor = () => fetch('/__open-in-editor?file=README.md')
</script>

<template>
  <WelcomeItem>
    <template #icon>
      <DocumentationIcon />
    </template>
    <template #heading>Documentation</template>

    Vue's
    <a href="https://vuejs.org/" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-700 transition-colors font-semibold">official documentation</a>
    provides you with all information you need to get started.
  </WelcomeItem>

  <WelcomeItem>
    <template #icon>
      <ToolingIcon />
    </template>
    <template #heading>Tooling</template>

    This project is served and bundled with
    <a href="https://vite.dev/guide/features.html" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-700 transition-colors font-semibold">Vite</a>. The
    recommended IDE setup is
    <a href="https://code.visualstudio.com/" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-700 transition-colors font-semibold">VSCode</a>
    +
    <a href="https://github.com/vuejs/language-tools" target="_blank" rel="noopener" class="text-purple-600 hover:text-purple-700 transition-colors font-semibold">Vue - Official</a>. If
    you need to test your components and web pages, check out
    <a href="https://vitest.dev/" target="_blank" rel="noopener" class="text-cyan-600 hover:text-cyan-700 transition-colors font-semibold">Vitest</a>
    and
    <a href="https://www.cypress.io/" target="_blank" rel="noopener" class="text-emerald-600 hover:text-emerald-700 transition-colors font-semibold">Cypress</a>
    /
    <a href="https://playwright.dev/" target="_blank" rel="noopener" class="text-teal-600 hover:text-teal-700 transition-colors font-semibold">Playwright</a>.

    <br />

    More instructions are available in
    <a href="javascript:void(0)" @click="openReadmeInEditor" class="text-blue-600 hover:text-blue-700 transition-colors font-semibold"><code class="bg-blue-100 text-blue-800 px-2 py-1 rounded-lg text-sm font-mono">README.md</code></a>.
  </WelcomeItem>

  <WelcomeItem>
    <template #icon>
      <EcosystemIcon />
    </template>
    <template #heading>Ecosystem</template>

    Get official tools and libraries for your project:
    <a href="https://pinia.vuejs.org/" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-700 transition-colors font-semibold">Pinia</a>,
    <a href="https://router.vuejs.org/" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-700 transition-colors font-semibold">Vue Router</a>,
    <a href="https://test-utils.vuejs.org/" target="_blank" rel="noopener" class="text-purple-600 hover:text-purple-700 transition-colors font-semibold">Vue Test Utils</a>, and
    <a href="https://github.com/vuejs/devtools" target="_blank" rel="noopener" class="text-cyan-600 hover:text-cyan-700 transition-colors font-semibold">Vue Dev Tools</a>. If
    you need more resources, we suggest paying
    <a href="https://github.com/vuejs/awesome-vue" target="_blank" rel="noopener" class="text-emerald-600 hover:text-emerald-700 transition-colors font-semibold">Awesome Vue</a>
    a visit.
  </WelcomeItem>

  <WelcomeItem>
    <template #icon>
      <CommunityIcon />
    </template>
    <template #heading>Community</template>

    Got stuck? Ask your question on
    <a href="https://chat.vuejs.org" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-700 transition-colors font-semibold">Vue Land</a>
    (our official Discord server), or
    <a href="https://stackoverflow.com/questions/tagged/vue.js" target="_blank" rel="noopener" class="text-orange-600 hover:text-orange-700 transition-colors font-semibold">StackOverflow</a>. You should also follow the official
    <a href="https://bsky.app/profile/vuejs.org" target="_blank" rel="noopener" class="text-sky-600 hover:text-sky-700 transition-colors font-semibold">@vuejs.org</a>
    Bluesky account or the
    <a href="https://x.com/vuejs" target="_blank" rel="noopener" class="text-slate-600 hover:text-slate-700 transition-colors font-semibold">@vuejs</a>
    X account for latest news in the Vue world.
  </WelcomeItem>

  <WelcomeItem>
    <template #icon>
      <SupportIcon />
    </template>
    <template #heading>Support Vue</template>

    As an independent project, Vue relies on community backing for its sustainability. You can help
    us by
    <a href="https://vuejs.org/sponsor/" target="_blank" rel="noopener" class="text-rose-600 hover:text-rose-700 transition-colors font-semibold">becoming a sponsor</a>.
  </WelcomeItem>
</template>
