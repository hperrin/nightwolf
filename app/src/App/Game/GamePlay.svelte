<div>
  Game Code: {$game.code}
</div>
<div>
  <Button on:click={() => $game = null}><Label>Leave</Label></Button>
</div>

<script>
  import {onMount, onDestroy} from 'svelte';
  import Button, {Label} from '@smui/button';

  import Game from '../../Entities/NightWolf/Game';
  import ErrHandler from '../../ErrHandler';
  import { user, game } from '../../stores';

  let shareUsername = '';
  let expanded = false;
  let subscription;

  $: createdDate = formatDate(new Date($game.cdate * 1000));
  $: modifiedDate = formatDate(new Date($game.mdate * 1000));
  $: isOwner = $user.$is($game.user);
  $: {
    if ($game && $game.user.$isASleepingReference) {
      $game.$readyAll(1).then(() => {
        $game = $game;
      }, ErrHandler);
    }
  }

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

    subscription = $game.$subscribe(
      (update) => {
        $game = update;
      },
      ErrHandler
    );
  }

  function formatDate(date) {
    return `${date.getFullYear()}-${
      date.getMonth() + 1 < 10
        ? '0' + (date.getMonth() + 1)
        : date.getMonth() + 1
    }-${date.getDate() < 10 ? '0' + date.getDate() : date.getDate()} ${
      date.getHours() < 10 ? '0' + date.getHours() : date.getHours()
    }:${date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()}`;
  }

  function share() {
    $game.$share(shareUsername).then((result) => {
      if (result) {
        shareUsername = '';
      } else {
        alert('Invalid user.');
      }
    }, ErrHandler);
  }

  function unshare(guid) {
    $game.$unshare(guid).then((result) => {
      if (!result) {
        alert('Invalid user.');
      }
    }, ErrHandler);
  }

  function save() {
    $game.$patch().then(() => ($game = $game), ErrHandler);
  }
</script>
