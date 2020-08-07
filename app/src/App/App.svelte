<div>
  {#if loading}
    <div style="height: 200px;">
      <LoadingIndicator />
    </div>
  {:else}
    <div class="row">
      <div class="col-sm-8 mb-3">
        <div class="list-group">
          {#if $games.length}
            {#each $games as todo (todo.guid)}
              <TodoEl bind:todo />
            {/each}
          {:else}
            <div class="alert alert-secondary">You have no games in progress.</div>
          {/if}
        </div>
      </div>
    </div>
    <form class="d-flex my-3" on:submit|preventDefault={addTodo}>
      <input
        class="form-control mr-2"
        style="flex-grow: 1;"
        type="text"
        bind:value={todoText}
        placeholder="add new todo here"
      />
      <input
        class="btn btn-primary"
        type="submit"
        value="add #{$games.length + 1}"
      />
    </form>
  {/if}
</div>

<script>
  import { onDestroy } from 'svelte';
  import { Nymph, PubSub } from 'nymph-client';
  import Game from '../Entities/NightWolf/Game';
  import LoadingIndicator from './LoadingIndicator';
  import TodoEl from './TodoEl';
  import ErrHandler from '../ErrHandler';
  import { games, game, user } from '../stores';

  let todoText = '';
  let subscription;
  let loading = false;

  $: remaining = $games.filter((todo) => !todo.done).length;

  let previousUser;
  $: {
    if ($user && !$user.$is(previousUser)) {
      subscribe();
    }
    previousUser = $user;
  }

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
        strict: [
          'state',
          Game.FINISHED
        ]
      }
    ).subscribe(
      (update) => {
        loading = false;
        if (update) {
          PubSub.updateArray($games, update);
          $games = Nymph.sort($games, 'cdate');
        }
      },
      ErrHandler
    );
  }

  function addTodo() {
    if (todoText === undefined || todoText === '') {
      return;
    }
    const todo = new Game();
    todo.name = todoText;
    todo.$save().then(() => {
      todoText = '';
    }, ErrHandler);
  }

  function deleteTodos() {
    Nymph.deleteEntities($games);
  }
</script>
