<div
  aria-label="Player Avatar: {user.name}"
  title={user.name}
  class="container"
  style="height: {size}px; width: {size}px;"
>
  {#if avatar}
    <img
      class="circle center"
      style="height: {size}px; width: {size}px;"
      src={avatar}
      alt="{user.nameFirst} Avatar Image"
    />
  {:else}
    <div
      class="circle center spinning"
      style="height: {size}px; width: {size}px; background-color: #{complement}; color: #{color};"
    >
      <MdiIcon path={mdiLoading} size={Math.floor(size * .75)} />
    </div>
  {/if}
</div>

<script>
  import { mdiLoading } from '@mdi/js';

  import MdiIcon from '../../MdiIcon';

  export let user;
  export let size = 32;

  let avatar = null;
  const color = (user.guid % 0xFFFFFF).toString(16);
  const complement = (0xFFFFFF ^ (user.guid % 0xFFFFFF)).toString(16);

  $: if (user && !user.$isASleepingReference && avatar === null) {
    user.$getAvatar().then((userAvatarValue) => {
      avatar = userAvatarValue;
    });
  }

  $: if (user && user.$isASleepingReference) {
    user.$ready().then(() => user = user);
  }
</script>

<style>
  .container {
    display: inline-block;
    vertical-align: middle;
  }

  .circle {
    border-radius: 50%;
    border: 2px solid #333;
    box-sizing: border-box;
  }

  .center {
    display: inline-flex;
    justify-content: center;
    align-items: center;
  }

  .spinning {
    animation-name: spin;
    animation-duration: 800ms;
    animation-iteration-count: infinite;
    animation-timing-function: ease-in-out;
  }

  @keyframes spin {
    from {
      transform: rotate(45deg);
    }
    to {
      transform: rotate(405deg);
    }
  }
</style>