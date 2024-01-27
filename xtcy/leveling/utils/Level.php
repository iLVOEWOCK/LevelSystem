<?php

namespace xtcy\leveling\utils;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use xtcy\leveling\Loader;

final class Level {

    use SingletonTrait;

    /** @var LevelManager[] */
    private array $levels;

    public function __construct(private Loader $plugin) {
        self::setInstance($this);

        $this->loadLevels();
    }
    /**
     * Store all player data in $sessions property
     *
     * @return void
     */
    private function loadLevels() : void
    {
        Loader::getDatabase()->executeSelect(QueryConstants::PLAYERS_SELECT, [], function (array $rows) : void {
            foreach ($rows as $row) {
                $this->levels[$row["uuid"]] = new LevelManager(
                    Uuid::fromString($row["uuid"]),
                    $row["username"],
                    $row["level"]
                );
            }
        });
    }

    /**
     * Create a session
     *
     * @param Player $player
     * @return LevelManager
     */
    public function createLevel(Player $player) : LevelManager
    {
        $args = [
            "uuid"     => $player->getUniqueId()->toString(),
            "username" => $player->getName(),
            "level"    => 0,
        ];

        Loader::getDatabase()->executeInsert(QueryConstants::PLAYERS_CREATE, $args);

        $this->levels[$player->getUniqueId()->toString()] = new LevelManager(
            $player->getUniqueId(),
            $args["username"],
            $args["level"]
        );
        return $this->levels[$player->getUniqueId()->toString()];
    }

    /**
     * Get levels by player object
     *
     * @param Player $player
     * @return LevelManager|null
     */
    public function getLevelSession(Player $player) : ?LevelManager
    {
        return $this->getLevelsSessionByUuid($player->getUniqueId());
    }

    /**
     * Get levels by player name
     *
     * @param string $name
     * @return LevelManager|null
     */
    public function getLevelSessionByName(string $name) : ?LevelManager
    {
        foreach ($this->levels as $session) {
            if (strtolower($session->getUsername()) === strtolower($name)) {
                return $session;
            }
        }
        return null;
    }

    /**
     * Get levels by UuidInterface
     *
     * @param UuidInterface $uuid
     * @return LevelManager|null
     */
    public function getLevelsSessionByUuid(UuidInterface $uuid) : ?LevelManager
    {
        return $this->levels[$uuid->toString()] ?? null;
    }

    public function destroyLevelSession(LevelManager $session) : void
    {
        Loader::getDatabase()->executeChange(QueryConstants::PLAYERS_DELETE, ["uuid", $session->getUuid()->toString()]);

        unset($this->levels[$session->getUuid()->toString()]);
    }

    public function getLevelSessions() : array
    {
        return $this->levels;
    }
}