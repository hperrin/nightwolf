<?php
namespace NightWolf;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

/**
 * @property string $code The game's access code.
 */
class Game extends \Nymph\Entity {
  const ETYPE = 'game';

  const PENDING = 0;
  const IN_PROGRESS = 1;
  const FINISHED = 2;

  protected $clientEnabledMethods = ['start', 'share', 'unshare'];
  public static $clientEnabledStaticMethods = ['join'];
  protected $whitelistData = [];
  public static $searchRestrictedData = ['code'];
  protected $protectedTags = [];
  protected $whitelistTags = [];

  /**
   * This is explicitly used only for joining game.
   *
   * @var bool
   * @access private
   */
  private $skipAcWhenSaving = false;

  public function __construct($id = 0) {
    $this->state = self::PENDING;
    $this->acOther = \Tilmeld\Tilmeld::NO_ACCESS;
    parent::__construct($id);
  }

  public static function generateCode() {
    $chars = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789');

    do {
      $code = '';
      for ($i = 0; $i < 5; $i++) {
        $code .= $chars[rand(0, count($chars) - 1)];
      }

      $existingGame = \Nymph\Nymph::getEntity(
        ['class' => 'NightWolf\\Game', 'skip_ac' => true],
        ['&',
          'strict' => [
            ['code', $code],
            ['state', self::PENDING]
          ]
        ]
      );
    } while (isset($existingGame));

    return $code;
  }

  public static function join($code) {
    if (!\Tilmeld\Tilmeld::gatekeeper()) {
      // Only allow logged in users to join.
      return false;
    }

    $game = \Nymph\Nymph::getEntity(
      ['class' => 'NightWolf\\Game', 'skip_ac' => true],
      ['&',
        'strict' => [
          ['code', $code],
          ['state', self::PENDING]
        ]
      ]
    );

    if ($game &&
      !\Tilmeld\Tilmeld::$currentUser->is($game->user) &&
      !\Tilmeld\Tilmeld::$currentUser->inArray($game->acRead)
    ) {
      $game->acRead[] = \Tilmeld\Tilmeld::$currentUser;
      return $game->saveSkipAC();
    }

    return false;
  }

  public function start() {
    if (!$this->guid || !$this->user->is(\Tilmeld\Tilmeld::$currentUser)) {
      return false;
    }

    $this->state = self::IN_PROGRESS;

    return $this->save();
  }

  public function share($username) {
    $user = \Tilmeld\Entities\User::factory($username);
    if (!$user->guid) {
      return false;
    }
    if (!$user->inArray($this->acWrite)) {
      $this->acWrite[] = $user;
    }
    return $this->save();
  }

  public function unshare($guid) {
    $user = \Tilmeld\Entities\User::factory($guid);
    if (!$user->guid) {
      return false;
    }
    while (($index = $user->arraySearch($this->acWrite)) !== false) {
      array_splice($this->acWrite, $index, 1);
    }
    return $this->save();
  }

  public function save() {
    if (!\Tilmeld\Tilmeld::gatekeeper()) {
      // Only allow logged in users to save.
      return false;
    }

    if (empty($this->code)) {
      $this->code = self::generateCode();
    }

    try {
      v::notEmpty()
        // ->attribute(
        //   'name',
        //   v::stringType()->notEmpty()->prnt()->length(1, 2048)
        // )
        // ->attribute('started', v::boolType())
        ->attribute(
          'state',
          v::in([self::PENDING, self::IN_PROGRESS, self::FINISHED], true)
        )
        ->setName('game')
        ->assert($this->getValidatable());
    } catch (NestedValidationException $exception) {
      throw new \Exception($exception->getFullMessage());
    }
    return parent::save();
  }

  /*
   * This should *never* be accessible on the client.
   */
  public function saveSkipAC() {
    $this->skipAcWhenSaving = true;
    return $this->save();
  }

  public function tilmeldSaveSkipAC() {
    if ($this->skipAcWhenSaving) {
      $this->skipAcWhenSaving = false;
      return true;
    }
    return false;
  }
}
