export const useDownloadFile = () => {
  const downloadFile = (url: string, params?: Record<string, any>) => {
    if (params) {
      const urlParams = Object.entries(params).map(
        ([key, value]) => key + "=" + value
      );
      url = url + "?" + urlParams.join("&");
    }
    const config = useRuntimeConfig();
    const backendUrl = config.public.backendUrl;
    window.open(backendUrl + url, "_blank");
  };
  return { downloadFile };
};
