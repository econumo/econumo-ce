export function useAvatar() {
  const avatarUrl = (url: string, size: number): string => {
    return `${url}?s=${size}`;
  };

  return {
    avatarUrl
  };
} 