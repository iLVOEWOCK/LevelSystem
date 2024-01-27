<?php

namespace xtcy\leveling\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use xtcy\leveling\Loader;
use xtcy\leveling\utils\Level;

class EventListener implements Listener
{

    public function __construct(public Loader $plugin) {

    }

    public function onPlayerLogin(PlayerLoginEvent $event) {
        $player = $event->getPlayer();
        if (Level::getInstance()->getLevelSession($player) === null) {
            Level::getInstance()->createLevel($player);
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();

        Level::getInstance()->getLevelSession($player)->setConnected(true);
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $session = Level::getInstance()->getLevelSession($player);

        $session->setConnected(false);
    }
}
