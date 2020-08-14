<div class="command-area">
  <div>
    <div>
      Game Code: <code>{$game.code}</code>
    </div>
    <div style="display: flex; justify-content: flex-start; align-items: center;">
      {$game.$players().length} Player{$game.$players().length > 1 ? 's' : ''}:
      {#each $game.$players() as user (user.guid)}
        <div style="display: inline-block; margin-left: 0.5em;">
          <Avatar user={user} />
        </div>
      {/each}
    </div>
  </div>
  <div>
    <Button on:click={() => $game = null}><Label>Leave</Label></Button>
    {#if $user.$is($game.user)}
      <Button on:click={deleteGame} disabled={isDeleting}><Label>Delete</Label></Button>
    {/if}
  </div>
</div>

<script>
  import {onMount, onDestroy} from 'svelte';
  import Button, {Label} from '@smui/button';

  import Avatar from '../Player/Avatar';
  import Game from '../../Entities/NightWolf/Game';
  import ErrHandler from '../../ErrHandler';
  import { user, game } from '../../stores';

  let isDeleting = false;

  $: createdDate = formatDate(new Date($game.cdate * 1000));
  $: modifiedDate = formatDate(new Date($game.mdate * 1000));
  $: isOwner = $user.$is($game.user);

  function formatDate(date) {
    return `${date.getFullYear()}-${
      date.getMonth() + 1 < 10
        ? '0' + (date.getMonth() + 1)
        : date.getMonth() + 1
    }-${date.getDate() < 10 ? '0' + date.getDate() : date.getDate()} ${
      date.getHours() < 10 ? '0' + date.getHours() : date.getHours()
    }:${date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()}`;
  }

  function save() {
    $game.$patch().then(() => ($game = $game), ErrHandler);
  }

  async function deleteGame() {
    if (confirm('Are you sure you want to delete this game? All players actively in the game will be notified that you deleted it.')) {
      try {
        isDeleting = true;
        $game.$delete();
      } catch (e) {
        ErrHandler(e);
      }
      isDeleting = false;
    }
  }
</script>

<style>
  .command-area {
    display: flex;
    justify-content: space-between;
  }
</style>