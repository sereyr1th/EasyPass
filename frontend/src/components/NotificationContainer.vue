<template>
  <div class="notification-container position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
    <TransitionGroup name="notification" tag="div">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'alert',
          'notification-item',
          'shadow-lg',
          'd-flex',
          'align-items-center',
          'justify-content-between',
          {
            'alert-success': notification.type === 'success',
            'alert-danger': notification.type === 'error',
            'alert-warning': notification.type === 'warning',
            'alert-info': notification.type === 'info'
          }
        ]"
        style="min-width: 300px; margin-bottom: 10px;"
      >
        <div class="d-flex align-items-center">
          <i 
            :class="[
              'me-2',
              {
                'bi bi-check-circle': notification.type === 'success',
                'bi bi-exclamation-triangle': notification.type === 'error',
                'bi bi-exclamation-triangle-fill': notification.type === 'warning',
                'bi bi-info-circle': notification.type === 'info'
              }
            ]"
          ></i>
          <div>
            <div class="fw-bold">{{ notification.title }}</div>
            <div v-if="notification.message" class="small">{{ notification.message }}</div>
          </div>
        </div>
        <button
          @click="removeNotification(notification.id)"
          type="button"
          class="btn-close"
          aria-label="Close"
        ></button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup lang="ts">
import { useNotifications } from '@/composables/useNotifications'

const { notifications, removeNotification } = useNotifications()
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.notification-item {
  border-radius: 8px;
  border: none;
  backdrop-filter: blur(10px);
}

.notification-container {
  max-width: 400px;
}
</style>
