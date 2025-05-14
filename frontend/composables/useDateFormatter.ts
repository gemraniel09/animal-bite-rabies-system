export const useDateFormatter = () => {
  const formatDate = (date?: string | number | Date) => {
    if (!date) return "—";
    return new Date(date).toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  };
  return { formatDate };
};
