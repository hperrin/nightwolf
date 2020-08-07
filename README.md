# Night Wolf

A werewolf/mafia game client/server. Play over VC.

Check out nightwolfgame.com for a running version, or start your own server.

# Running Dev Environment

Clone the repo then run:

```sh
./run.sh
```

Go in the `app` dir and run:

```sh
npm run watch
```

Go to http://localhost:8080.

## NPM and Composer

If [NPM](https://nodejs.org/en/download/current/) and/or [Composer](https://getcomposer.org/download/) are not installed, `npm.sh` and `composer.sh` will use a Docker container to run them.

You can run commands from the repository root (not the "app" directory) using `composer.sh` and `npm.sh`. For example:

```shell
./composer.sh require vendor/package
./npm.sh install --save package
./npm.sh run build
```

# License

Copyright 2020 Hunter Perrin

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

