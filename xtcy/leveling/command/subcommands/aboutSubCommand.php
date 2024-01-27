<?php

namespace xtcy\leveling\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use xtcy\leveling\Loader;

class aboutSubCommand extends BaseSubCommand
{

    public function prepare(): void
    {
        $this->setPermission("leveling.default.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {

        $version = $this->plugin->getDescription()->getVersion();
        $authors = implode(", ", $this->plugin->getDescription()->getAuthors());
        $messages = [
            "       &r&8―――――――&8<&6&l Leveling&f&lSystem About &8>&8―――――――",
            "&7Plugin Information:",
            " - Version: &f" . $version,
            " - Author(s): &f" . $authors,
            " - Github: &ehttps://github.com/iLVOEWOCK",
            "",
            "Thank you for using &6Leveling&fSystem&f! I hope it enhances your server experience.",
            "       &r&8――――――――――――――――――――――――――――――――"
        ];
        foreach ($messages as $message) {
            $sender->sendMessage(TextFormat::colorize($message));
        }
    }
}