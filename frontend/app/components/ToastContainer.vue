<template>
  <div class="fixed bottom-5 right-5 z-50 space-y-3 max-w-sm w-full pointer-events-none">
    <TransitionGroup 
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-for="toast in toasts" 
        :key="toast.id" 
        class="pointer-events-auto w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-4 flex items-start space-x-3 overflow-hidden relative"
      >
        <div class="flex-shrink-0 mt-0.5">
          <span v-if="toast.type === 'success'">✨</span>
          <span v-else-if="toast.type === 'error'">🚨</span>
          <span v-else>ℹ️</span>
        </div>
        
        <div class="flex-1">
          <p class="text-sm font-semibold text-slate-800 leading-tight">
            {{ toast.message }}
          </p>
        </div>

        <button @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-600 transition text-xs font-bold pl-2">
          ✕
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
const { toasts, removeToast } = useToast()
</script>