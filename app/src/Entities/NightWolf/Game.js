import { Nymph, Entity } from 'nymph-client';

export class Game extends Entity {
  constructor(id) {
    super(id);
    this.done = false;
  }

  $archive(...args) {
    return this.$serverCall('archive', args);
  }

  $share(...args) {
    return this.$serverCall('share', args);
  }

  $unshare(...args) {
    return this.$serverCall('unshare', args);
  }
}

// The name of the server class
Game.class = 'NightWolf\\Game';

Nymph.setEntityClass(Game.class, Game);

export default Game;
