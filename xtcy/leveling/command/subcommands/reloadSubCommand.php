<?php

namespace xtcy\leveling\command\subcommands;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use xtcy\leveling\Loader;

class reloadSubCommand extends BaseSubCommand
{

    public function prepare(): void
    {
        $this->setPermission("leveling.admin.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::colorize("&r&l&aReloading all configurations"));
        Loader::getInstance()->reloadConfig();
    }
}