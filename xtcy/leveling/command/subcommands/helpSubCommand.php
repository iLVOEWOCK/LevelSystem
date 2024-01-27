<?php

namespace xtcy\leveling\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use wockkinmycup\utilitycore\utils\Utils;

class helpSubCommand extends BaseSubCommand
{

    public function prepare(): void
    {
        $this->setPermission("leveling.default.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $config = Utils::getConfiguration($this->plugin, "language/ENG-def.yml");
        $messages = $config->getNested("messages.help-player");
        $adminMessages = $config->getNested("messages.help-admin");

        if (!$sender->hasPermission("leveling.admin.command")) {
            foreach ($messages as $message) {
                $sender->sendMessage(TextFormat::colorize($message));
            }
        }

        if ($sender instanceof Player && $sender->hasPermission("leveling.admin.command")) {
            foreach ($adminMessages as $adminMessage) {
                $sender->sendMessage(TextFormat::colorize($adminMessage));
            }
        }
    }
}