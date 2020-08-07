<div class="loading-container">
  <div>
    <LinearProgress indeterminate />
  </div>
</div>

<script>
  import LinearProgress from '@smui/linear-progress';
</script>

<style>
  .loading-container {
    display: flex;
    height: 100%;
    align-items: center;
    justify-content: center;
  }
  .loading-container > * {
    width: 60%;
  }
</style>