import type { DataFetchOptions, ExecuteOptions } from "~/lib/types";

export const useDataFetch = <T = any>(
  url: string,
  options?: DataFetchOptions
) => {
  const data = ref<T>({} as T);
  const loading = ref(false);
  const error = ref<any>();

  const config = useRuntimeConfig();
  const backendUrl = config.public.backendUrl;

  const execute = async (executeOptions?: ExecuteOptions) => {
    if (executeOptions?.showLoading) {
      loading.value = true;
    }
    let modifiedUrl = url;
    if (executeOptions?.search?.params) {
      const params = executeOptions.search.params;
      const urlParams = Object.entries(params).map(([key, value]) => key + "=" + value);
      modifiedUrl = url + "/search" + "?" + urlParams.join("&");
    }
    if (executeOptions?.params) {
      const params = executeOptions.params;
      const urlParams = Object.entries(params).map(([key, value]) => key + "=" + value);
      modifiedUrl = url  + "?" + urlParams.join("&");
    }
    try {
      await $fetch(backendUrl + "/sanctum/csrf-cookie", {
        credentials: "include",
      });

      // Get CSRF token
      const xsrfToken = useCookie("XSRF-TOKEN").value || "";

      const fetchData = (await $fetch<T>(`${backendUrl}${modifiedUrl}`, {
        credentials: "include",
        headers: {
          ...options?.headers,
          "Content-type": "application/json",
          Accept: "application/json",
          "x-xsrf-token": xsrfToken,
        },
        ...options,
      } as any)) as T;

      data.value = fetchData;
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };
  if (options?.immediate !== false) {
    execute({ showLoading: true });
  }

  function refresh() {
    execute({ showLoading: false });
  }

  return {
    data,
    loading,
    error,
    refresh,
    execute,
  };
};
