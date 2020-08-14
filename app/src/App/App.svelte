<div>
  {#if loading}
    <div style="height: 200px;">
      <LoadingIndicator />
    </div>
  {:else if $game}
    <GamePlay />
  {:else}
    <div class="lobby">
      <div class="lobby-section">
        <div>
          <Textfield variant="outlined" label="Game Code" bind:value={joinCode} input$maxlength="5" />
        </div>
        <div style="margin-left: 20px;">
          <Button on:click={join} disabled={joinCode.length != 4 || joinCode.match(/[^A-Za-z1-9]/)}>
            <Label>Join Game</Label>
          </Button>
        </div>
        <div style="margin-left: 20px;">
          <Button on:click={create}><Label>New Game</Label></Button>
        </div>
      </div>
      <div class="lobby-section">
        <GamesList />
      </div>
    </div>
  {/if}
</div>

<script>
  import Textfield from '@smui/textfield';
  import Button, {Label} from '@smui/button';
  import Paper from '@smui/paper';

  import Game from '../Entities/NightWolf/Game';
  import ErrHandler from '../ErrHandler';
  import { game } from '../stores';

  import LoadingIndicator from './LoadingIndicator';
  import GamesList from './GamesList';
  import GamePlay from './Game/GamePlay';

  let joinCode = '';
  let loading = false;

  async function create() {
    const newGame = new Game();
    try {
      if (await newGame.$save()) {
        $game = newGame;
      } else {
        alert('The game couldn\'t be created. :(');
      }
    } catch (e) {
      ErrHandler(e);
    }
  }

  async function join() {
    try {
      const joinedGame = await Game.join(joinCode.toUpperCase());
      if (joinedGame) {
        joinCode = '';
        $game = joinedGame;
      } else {
        alert('That code is not working.');
      }
    } catch (e) {
      ErrHandler(e);
    }
  }
</script>

<style>
  .lobby {
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  * > :global(.lobby-section) {
    width: max-content;
    padding-left: 20px;
    padding-right: 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
  }
</style>