import { Nymph, Entity } from 'nymph-client';

export class Game extends Entity {
  constructor(id) {
    super(id);
    this.code = null;
    this.finished = false;
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
