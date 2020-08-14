{#if loading}
  <div style="height: 40px; width: 100%; min-width: 300px;">
    <LoadingIndicator />
  </div>
{:else}
  {#if games.length}
    <div class="game-container">
      {#each games as game (game.guid)}
        <Card style="width: 320px;">
          <div style="padding: 1rem;">
            <h6 style="margin: 0;">{stateText[game.state]} Game</h6>
            <div class="subtitle2" style="margin: 0; color: #888;">Game Code: <code>{game.code}</code></div>
          </div>
          <Content>
            {#each game.$players() as user (user.guid)}
              <div style="display: inline-block; margin-right: 1em;">
                <Avatar user={user} />
              </div>
            {/each}
          </Content>
          <Actions fullBleed>
            <Button on:click={() => load(game)}>
              <Label>Rejoin</Label>
            </Button>
          </Actions>
        </Card>
      {/each}
    </div>
  {:else}
    <small>You have no games in progress.</small>
  {/if}
{/if}

<script>
  import { Nymph, PubSub } from 'nymph-client';
  import { onMount, onDestroy } from 'svelte';
  import Button, {Label} from '@smui/button';
  import Card, {Content, PrimaryAction, Media, MediaContent, Actions, ActionButtons, ActionIcons} from '@smui/card';

  import Game from '../Entities/NightWolf/Game';
  import ErrHandler from '../ErrHandler';
  import { game } from '../stores';

  import Avatar from './Player/Avatar';
  import LoadingIndicator from './LoadingIndicator';

  let subscription;
  let loading = false;
  let games = [];

  const stateText = {
    [Game.PENDING]: 'Pending',
    [Game.IN_PROGRESS]: 'Ongoing'
  };

  // let previousUser;
  // $: {
  //   if ($user && !$user.$is(previousUser)) {
  //     subscribe();
  //   }
  //   previousUser = $user;
  // }

  onMount(subscribe);

  onDestroy(() => {
    if (subscription) {
      subscription.unsubscribe();
    }
  });

  function subscribe() {
    if (subscription) {
      subscription.unsubscribe();
    }

    loading = true;

    subscription = Nymph.getEntities(
      {
        class: Game.class,
        sort: 'cdate',
        reverse: true
      },
      {
        type: '!&',
        strict: ['state', Game.FINISHED]
      }
    ).subscribe(
      (update) => {
        loading = false;
        if (update) {
          PubSub.updateArray(games, update);
          games = Nymph.sort(games, 'cdate', false, true);
        }
      },
      ErrHandler
    );
  }

  function load(g) {
    $game = g;
  }
</script>

<style>
  .game-container {
    display: flex;
    flex-wrap: wrap;
  }

  .game-container > :global(*) {
    margin: 0 1em 1em;
  }
</style>