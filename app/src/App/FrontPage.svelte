<div>
  <h1>{brand}!</h1>
  <p>
    A free and open source web based Werewolf/Mafia game. Play in the same room on your phones
    or play in a video call together! The server acts as the narrator, so everyone can play!
  </p>
  <p>
    <Button href="https://github.com/hperrin/nightwolf" target="_blank">
      <Label>Fork Me on GitHub</Label><Icon><MdiIcon size={18} path={mdiGithub} /></Icon>
    </Button>
  </p>
</div>

<script>
  import Button, {Label, Icon} from '@smui/button';
  import {mdiGithub} from '@mdi/js';

  import MdiIcon from '../MdiIcon';

  export let brand;
</script>
