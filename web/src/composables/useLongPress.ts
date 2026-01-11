import { ref } from 'vue';

interface TouchPosition {
  x: number;
  y: number;
}

export function useLongPress(onLongPress: (target: HTMLElement) => void, delay = 500, moveThreshold = 10) {
  const pressTimer = ref<number | null>(null);
  const touchStartPosition = ref<TouchPosition>({ x: 0, y: 0 });
  const touchTarget = ref<HTMLElement | null>(null);

  function startLongPress(): void {
    pressTimer.value = window.setTimeout(() => {
      if (touchTarget.value) {
        onLongPress(touchTarget.value);
      }
    }, delay);
  }

  function cancelLongPress(): void {
    if (pressTimer.value) {
      clearTimeout(pressTimer.value);
      pressTimer.value = null;
    }
    touchTarget.value = null;
  }

  function startTouch(event: TouchEvent): void {
    touchTarget.value = event.target as HTMLElement;
    touchStartPosition.value = {
      x: event.touches[0].clientX,
      y: event.touches[0].clientY
    };
    startLongPress();
  }

  function handleTouchMove(event: TouchEvent): void {
    if (!pressTimer.value) return;
    
    const deltaX = Math.abs(event.touches[0].clientX - touchStartPosition.value.x);
    const deltaY = Math.abs(event.touches[0].clientY - touchStartPosition.value.y);
    
    if (deltaX > moveThreshold || deltaY > moveThreshold) {
      cancelLongPress();
    }
  }

  function handleTouchEnd(): void {
    cancelLongPress();
  }

  return {
    startTouch,
    handleTouchMove,
    handleTouchEnd
  };
} 