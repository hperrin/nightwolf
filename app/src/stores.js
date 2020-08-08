import { writable } from 'svelte/store';

export * from './userStores';

export const game = writable(null);
