// app/composables/useToast.ts
import { ref } from 'vue'

export interface Toast {
  id: number
  message: string
  type: 'success' | 'error' | 'info'
}

const toasts = ref<Toast[]>([])
let counter = 0

export const useToast = () => {
  const addToast = (message: string, type: 'success' | 'error' | 'info' = 'success') => {
    const id = counter++
    toasts.value.push({ id, message, type })

    // Auto-suppression après 3,5 secondes
    setTimeout(() => {
      removeToast(id)
    }, 3500)
  }

  const removeToast = (id: number) => {
    toasts.value = toasts.value.filter(t => t.id !== id)
  }

  return {
    toasts,
    addToast,
    removeToast
  }
}