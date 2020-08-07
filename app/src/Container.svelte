{#if $user === false}
  <LoadingIndicator />
{:else}
  <div class="app-frame">
    <TopAppBar variant="static" color="primary">
      <Row>
        <Section>
          <Title>{brand}</Title>
        </Section>
        {#if $user}
          <Section align="end" toolbar>
            <IconButton aria-label="Account" on:click={() => accountMenu.setOpen(true)}>
              {#if $userAvatar}
                <img
                  style="border-radius: 50%; height: 24px; width: 24px;"
                  src={$userAvatar}
                  alt={$user.nameFirst}
                />
              {:else}
                <MdiIcon path={mdiAccount} />
              {/if}
              <Menu bind:this={accountMenu}>
                <List>
                  <Item on:SMUI:action={() => accountDialog.open()}><Text>Account Info</Text></Item>
                  <Item on:SMUI:action={logout}><Text>Log Out</Text></Item>
                  {#if $userIsTilmeldAdmin}
                    <Separator />
                    <Item on:SMUI:action={() => window.open('/user/')}><Text>User Admin App</Text></Item>
                  {/if}
                </List>
              </Menu>
            </IconButton>
            <Dialog bind:this={accountDialog} aria-labelledby="account-title" aria-describedby="account-content">
              <!-- Title cannot contain leading whitespace due to mdc-typography-baseline-top() -->
              <DialogTitle id="account-title">Account Info</DialogTitle>
              <Content id="account-content">
                <div>
                  <Textfield bind:value={$user.username} label="Email" type="email" />
                </div>
                <div>
                  <Textfield bind:value={$user.nameFirst} label="First Name" />
                </div>
                <div>
                  <Textfield bind:value={$user.nameLast} label="Last Name" />
                </div>
                <!-- <div><Textfield bind:value={$user.phone} label="Phone" type="tel" /></div> -->
                {#if clientConfig.timezones}
                  <div>
                    <Select bind:value={$user.timezone} label="Timezone">
                      <Option value=""></Option>
                      {#each clientConfig.timezones as tz}
                        <Option value={tz} selected={$user.timezone === tz}>{tz}</Option>
                      {/each}
                    </Select>
                  </div>
                {/if}
                <div style="margin-top: 1em;">
                  <ChangePassword
                    layout="compact"
                    classInput="form-control"
                    classSubmit="btn btn-primary"
                    classButton="btn btn-secondary"
                  />
                </div>
              </Content>
              <Actions>
                <Button>
                  <Label>Close</Label>
                </Button>
                <Button on:click={saveUser}>
                  <Label>Save</Label>
                </Button>
              </Actions>
            </Dialog>
          </Section>
        {/if}
      </Row>
    </TopAppBar>
    <div class="app-content">
      {#if $user === null}
        <div class="frontpage-container">
          <div class="login">
            <Paper class="login-paper">
              <Login
                layout="small"
                classInput="form-control"
                classSelect="form-control"
                classTextarea="form-control"
                classSubmit="btn btn-primary"
                classButtonGroup="btn-group d-flex"
                classButton="btn btn-secondary"
                classButtonToggle="flex-grow-1"
                classButtonActive="active"
                disableActiveButton={false}
              />
            </Paper>
          </div>
          <div class="frontpage">
            <FrontPage {brand} />
          </div>
        </div>
      {/if}
      {#if $user}
        <App />
      {/if}
    </div>
  </div>
{/if}

<script>
  import { onMount } from 'svelte';
  import { User } from 'tilmeld-client';
  import Login from 'tilmeld-components/src/Login';
  import ChangePassword from 'tilmeld-components/src/ChangePassword';
  import TopAppBar, {Row, Section, Title} from '@smui/top-app-bar';
  import IconButton from '@smui/icon-button';
  import { mdiAccount } from '@mdi/js';
  import Menu from '@smui/menu';
  import List, {Item, Separator, Text, PrimaryText, SecondaryText, Graphic} from '@smui/list';
  import Button, {Label} from '@smui/button';
  import Paper from '@smui/paper';
  import Dialog, {Title as DialogTitle, Content, Actions, InitialFocus} from '@smui/dialog';
  import Textfield from '@smui/textfield';
  import Select, {Option} from '@smui/select';

  import App from './App/App';
  import FrontPage from './App/FrontPage';
  import LoadingIndicator from './App/LoadingIndicator';

  import MdiIcon from './MdiIcon';
  import ErrHandler from './ErrHandler';
  import { user, userAvatar, userIsTilmeldAdmin, logout } from './stores';

  export let brand;
  let clientConfig = {};

  let accountMenu;
  let accountDialog;

  onMount(() => {
    // Get the client config (for timezones).
    User.getClientConfig().then((config) => {
      clientConfig = config;
    });
  });

  function saveUser() {
    $user.$save().then((userValue) => {
      $user = userValue;
    }, ErrHandler);
  }
</script>

<style>
  .app-frame {
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  .app-content {
    flex-basis: 0;
    height: 0;
    flex-grow: 1;
    overflow: auto;
    width: 100%;
    padding: 20px;
    box-sizing: border-box;
  }

  .frontpage-container {
    display: flex;
  }
  .login {
    order: 2;
    width: 30%;
    margin-left: 20px;
    display: flex;
    justify-content: center;
  }
  .login > :global(.login-paper) {
    width: max-content;
    height: max-content;
  }
  .frontpage {
    order: 1;
    flex-grow: 1;
  }

  @media (max-width: 600px) {
    .frontpage-container {
      flex-direction: column;
    }
    .login {
      order: 1;
      width: 100%;
      margin-left: 0;
    }
    .frontpage {
      order: 2;
    }
  }
</style>