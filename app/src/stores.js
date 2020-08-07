import { writable } from 'svelte/store';

export * from './userStores';

export const games = writable([]);
export const game = writable(null);
export const archived = writable(false);
