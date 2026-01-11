export const setDefaultIfNotFunction = fn => {
  if (typeof fn !== 'function') {
    return console.log
  }
  return fn
};

export const restDefaults = {
  client: 'axios'
};

export function getTimezone() {
  return Intl.DateTimeFormat().resolvedOptions().timeZone;
}
