<template>
  <div class="mt-8 flex relative lg:mt-0 lg:pb-4 lg:pl-[calc(var(--section-gap)/2)]">
    <i class="flex items-center justify-center w-12 h-12 text-blue-500 lg:absolute lg:top-[calc(50%-25px)] lg:left-[-26px] lg:w-16 lg:h-16 lg:border-2 lg:border-blue-200 lg:bg-white lg:rounded-2xl lg:shadow-lg">
      <slot name="icon"></slot>
    </i>
    <div class="flex-1 ml-4 lg:ml-0">
      <h3 class="text-xl font-semibold mb-3 text-slate-800" style="font-family: 'Poppins', sans-serif;">
        <slot name="heading"></slot>
      </h3>
      <div class="text-slate-600 leading-relaxed">
        <slot></slot>
      </div>
    </div>
  </div>
</template>


