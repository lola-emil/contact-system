import { onMounted, onBeforeUnmount } from 'vue';

type Options = {
  close: () => void;
  shouldIgnore?: () => boolean; // optional condition before closing
};

export function useEscapeKeyClose({ close, shouldIgnore }: Options) {
  const handleKeydown = (evt: KeyboardEvent) => {
    if (evt.key === 'Escape') {
      if (shouldIgnore?.()) {
        evt.preventDefault();
        return;
      }

      evt.preventDefault();
      close();
    }
  };

  onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown);
  });
}