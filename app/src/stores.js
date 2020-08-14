import { writable, get } from 'svelte/store';

import ErrHandler from './ErrHandler';

export * from './userStores';

export const game = writable(null);

let previousGameValue = null;
let gameSubscription = null;
let readyingEntities = false;
game.subscribe((gameValue) => {
  if (gameValue && gameValue.guid) {
    if (readyingEntities) {
      // It's just a notice about readying the entities.
      return;
    }

    // The user joined a game.
    if (!gameValue.$is(previousGameValue)) {
      // It's not the same game as before.
      if (gameSubscription) {
        gameSubscription.unsubscribe();
      }
      // Subscribe to the game.
      gameSubscription = gameValue.$subscribe(() => {
        if (gameValue.guid) {
          game.set(gameValue);
        } else {
          alert('The owner of the game has deleted it. :(');
          game.set(null);
        }
      }, ErrHandler);
    }
    // The game has been updated, so ready all its entities.
    gameValue.$readyAll(1).then(() => {
      readyingEntities = true;
      // We don't know at this point, whether gameValue is still the active
      // game, so just cause an update with whatever is in there.
      game.set(get(game));
      readyingEntities = false;
    }, ErrHandler);
  } else {
    // The user is not active in a game.
    if (gameSubscription) {
      gameSubscription.unsubscribe();
      gameSubscription = null;
      readyingEntities = false;
    }
  }
  previousGameValue = gameValue;
});
