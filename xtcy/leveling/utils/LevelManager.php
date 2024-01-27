<?php

namespace xtcy\leveling\utils;

use pocketmine\player\Player;
use pocketmine\Server;
use Ramsey\Uuid\UuidInterface;
use xtcy\leveling\Loader;

final class LevelManager
{

    private bool $isConnected = false;

    public function __construct(private readonly UuidInterface $uuid, private string $username, private int $level) {

    }

    public function isConnected() : bool
    {
        return $this->isConnected;
    }

    public function setConnected(bool $connected) : void
    {
        $this->isConnected = $connected;
    }

    /**
     * Get UUID of the player
     *
     * @return UuidInterface
     */
    public function getUuid() : UuidInterface
    {
        return $this->uuid;
    }

    /**
     *
     * @return Player|null
     */
    public function getPocketminePlayer() : ?Player
    {
        return Server::getInstance()->getPlayerByUUID($this->uuid);
    }

    /**
     * Get username of the session
     *
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * Set username of the session
     *
     * @param string $username
     * @return void
     */
    public function setUsername(string $username) : void
    {
        $this->username = $username;
        $this->updateDb();
    }

    /**
     * Get level of the session
     *
     * @return int
     */
    public function getLevel() : int
    {
        return $this->level;
    }

    /**
     * Add level to the session
     *
     * @param int $amount
     * @return void
     */
    public function addLevel(int $amount) : void
    {
        $this->level += $amount;
        $this->updateDb();
    }

    /**
     * Subtract level from the session
     *
     * @param int $amount
     * @return void
     */
    public function subtractLevel(int $amount) : void
    {
        $this->level -= $amount;
        $this->updateDb();
    }

    /**
     * Set level of the session
     *
     * @param int $amount
     * @return void
     */
    public function setLevel(int $amount) : void
    {
        $this->level = $amount;
        $this->updateDb();
    }

    /**
     * Update player information in the database
     *
     * @return void
     */
    private function updateDb() : void
    {
        Loader::getDatabase()->executeChange(QueryConstants::PLAYERS_UPDATE, [
            "uuid" => $this->uuid->toString(),
            "username" => $this->username,
            "level" => $this->level
        ]);
    }
}