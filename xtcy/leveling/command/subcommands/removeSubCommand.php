<?php

namespace xtcy\leveling\command\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use wockkinmycup\utilitycore\utils\Utils;
use xtcy\leveling\utils\Level;

class removeSubCommand extends BaseSubCommand
{

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->setPermission("leveling.admin.command");
        $this->registerArgument(0, new RawStringArgument("player", false));
        $this->registerArgument(1, new IntegerArgument("levels", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $config = Utils::getConfiguration($this->plugin, "language/ENG-def.yml");

        if (count($args) < 2) {
            $sender->sendMessage("§cUsage: /$aliasUsed <player> <amount>");
            return;
        }

        $playerName = $args["player"];
        $amount = $args["levels"];

        if (!is_numeric($amount)) {
            $sender->sendMessage(TextFormat::colorize($config->getNested("messages.not-number")));
            return;
        }

        $amount = (int)$amount;

        $player = Utils::getPlayerByPrefix($playerName);

        if ($player === null) {
            $sender->sendMessage(TextFormat::colorize(str_replace("{player}", $playerName, $config->getNested("messages.player-offline"))));
            return;
        }

        $session = Level::getInstance()->getLevelSession($player);
        $level = $session->getLevel() - $amount;
        $session->subtractLevel($amount);

        $sender->sendMessage(TextFormat::colorize(str_replace(["{removed_levels}", "{player}", "{level}"], [number_format($amount), $player->getName(), number_format($level)], $config->getNested("messages.removed-levels"))));
    }
}