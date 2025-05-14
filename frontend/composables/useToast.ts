import { ref } from "vue";

const toasts = ref<Array<{ id: number; message: string; type: string }>>([]);

const toast = (message: string, type: string = "info") => {
  const id = Date.now();
  toasts.value.push({ id, message, type });
  setTimeout(() => {
    toasts.value = toasts.value.filter((toast) => toast.id !== id);
  }, 4000);
};
const errorToast = (error: any) => {
  toast(error.data.message, "error");
};

export const useToast = () => {
  return {
    toasts,
    toast,
    errorToast,
  };
};
