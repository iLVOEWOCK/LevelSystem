<?php

namespace xtcy\leveling\command;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use xtcy\leveling\command\subcommands\aboutSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use wockkinmycup\utilitycore\utils\Utils;
use xtcy\leveling\command\subcommands\addSubCommand;
use xtcy\leveling\command\subcommands\helpSubCommand;
use xtcy\leveling\command\subcommands\reloadSubCommand;
use xtcy\leveling\command\subcommands\removeSubCommand;
use xtcy\leveling\command\subcommands\setSubCommand;
use xtcy\leveling\Loader;
use xtcy\leveling\utils\Level;

class LevelsCommand extends BaseCommand
{

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void {

        $this->setPermission("leveling.default.command");
        $this->registerArgument(0, new RawStringArgument("player", true));
        $this->registerSubCommand(new aboutSubCommand($this->plugin, "about", "Information about the plugin"));
        $this->registerSubCommand(new reloadSubCommand($this->plugin, "reload", "Reload the plugins configuration."));
        $this->registerSubCommand(new helpSubCommand($this->plugin, "help", "Shows the leveling system commands."));
        //$this->registerSubCommand(new addSubCommand($this->plugin, "add", "Add levels to a player"));
        $this->registerSubCommand(new removeSubCommand($this->plugin, "remove", "Remove levels from a player", ["subtract", "rem", "del"]));
        //$this->registerSubCommand(new setSubCommand($this->plugin, "set", "Set levels to a player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $p = $args["player"] ?? null;
        $config = Utils::getConfiguration(Loader::getInstance(), "language/ENG-def.yml");
        if ($p !== null) {
            $player = Utils::getPlayerByPrefix($p);

            if ($player === null) {
                $sender->sendMessage(TextFormat::RED . "Player not found!");
                return;
            }

            $session = Level::getInstance()->getLevelSession($player);
            $level = $session->getLevel();

            foreach ($config->getNested("messages.level-info") as $message) {
                $sender->sendMessage(TextFormat::colorize(str_replace(["{player}", "{level}"], [$player->getName(), number_format($level)], $message)));
            }
        } else {
            if (!$sender instanceof Player) {
                return;
            }

            $session = Level::getInstance()->getLevelSession($sender);
            $level = $session->getLevel();

            foreach ($config->getNested("messages.level-info") as $message) {
                $sender->sendMessage(TextFormat::colorize(str_replace(["{player}", "{level}"], [$sender->getName(), number_format($level)], $message)));
            }
        }
    }


}