<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

error_reporting(E_ALL);

/*
 * You don't need to edit below here. It is set up to work inside your Docker
 * container.
 */

if (php_sapi_name() != 'cli') {
  die('You can only run pubsub.php from the command line.');
}

require __DIR__.'/config.php';

\Nymph\Nymph::connect();

if (in_array('-d', $argv)) {
  if (!is_callable('pcntl_fork')) {
    die('PCNTL PHP extension is required to use daemon mode.');
  }

  function shutdown() {
    posix_kill(posix_getpid(), SIGHUP);
  }

  // Switch over to daemon mode.
  if ($pid = pcntl_fork()) {
    return;
  }

  register_shutdown_function('shutdown');
} else {
  error_reporting(E_ALL);
}

$server = new \Nymph\PubSub\Server();
$server->run();
