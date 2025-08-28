<script setup lang="ts">
defineProps<{
  msg: string
}>()
</script>

<template>
  <div class="text-center lg:text-left">
    <h1 class="text-5xl font-bold text-slate-800 relative -top-2 mb-6" style="font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ msg }}</h1>
    <h3 class="text-xl text-slate-600 leading-relaxed">
      You've successfully created a project with
      <a href="https://vite.dev/" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-700 transition-colors font-semibold">Vite</a> +
      <a href="https://vuejs.org/" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-700 transition-colors font-semibold">Vue 3</a>. What's next?
    </h3>
  </div>
</template>


