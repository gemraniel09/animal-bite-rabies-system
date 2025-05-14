import type { $Fetch, FetchOptions } from "ofetch";

// Define a generic type for the response
export const useApiFetch = async <T = any>(
  url: string,
  options?: FetchOptions<any>
): Promise<T> => {
  const config = useRuntimeConfig();
  const backendUrl = config.public.backendUrl;
  await $fetch(backendUrl + "/sanctum/csrf-cookie", {
    credentials: "include",
  });

  // Get CSRF token
  const xsrfToken = useCookie("XSRF-TOKEN").value || "";

  // Return the $fetch call with proper typing
  return $fetch<T>(`${backendUrl}${url}`, {
    credentials: "include",
    headers: {
      ...options?.headers,
      "Content-type": "application/json",
      Accept: "application/json",
      "x-xsrf-token": xsrfToken,
    },
    ...options,
  } as any);
};
