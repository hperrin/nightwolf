import { Nymph, Entity } from 'nymph-client';

export class Game extends Entity {
  constructor(id) {
    super(id);
    this.code = null;
    this.state = Game.PENDING;
    this.acRead = [];
  }

  $start(...args) {
    return this.$serverCall('start', args);
  }

  $share(...args) {
    return this.$serverCall('share', args);
  }

  $unshare(...args) {
    return this.$serverCall('unshare', args);
  }

  $players() {
    return [this.user, ...this.acRead];
  }

  static join(code, ...args) {
    return Game.serverCallStatic('join', [code, ...args]);
  }
}

// The name of the server class
Game.class = 'NightWolf\\Game';

Game.PENDING = 0;
Game.IN_PROGRESS = 1;
Game.FINISHED = 2;

Nymph.setEntityClass(Game.class, Game);

export default Game;
