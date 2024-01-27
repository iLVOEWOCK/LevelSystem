<?php

namespace xtcy\leveling\command;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\database\exception\InsufficientFundsException;
use cooldogedev\BedrockEconomy\database\exception\RecordNotFoundException;
use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\exception\SQLException;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use wockkinmycup\utilitycore\utils\Utils;
use xtcy\leveling\Loader;
use xtcy\leveling\utils\Level;

class LevelCommand extends BaseCommand {

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->setPermission("leveling.default.command");
        $this->registerArgument(0, new RawStringArgument("max", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        $session = Loader::getSessionManager()->getLevelSession($sender);
        $pLevel = $session->getLevel();
        $config = Utils::getConfiguration(Loader::getInstance(), "config.yml");
        $maxLevel = $config->getNested("settings.max-level");

        if ($pLevel === $maxLevel) {
            $sender->sendMessage(TextFormat::RED . "You are already at the highest level!");
            return;
        }

        if ($config->getNested("levels." . ($pLevel + 1))) {
            $nextLevelConfig = $config->getNested("levels." . ($pLevel + 1));

            $requiredMoney = $nextLevelConfig["price"];
            BedrockEconomyAPI::CLOSURE()->subtract(
                $sender->getXuid(),
                $sender->getName(),
                $requiredMoney,
                00,
                static function () use ($sender, $pLevel, $nextLevelConfig, $config): void {
                    Level::getInstance()->getLevelSession($sender)->setLevel($pLevel + 1);

                    foreach ($nextLevelConfig["messages"] as $message) {
                        $sender->sendMessage(TextFormat::colorize($message));
                    }

                    foreach ($nextLevelConfig["rewards"] as $reward) {
                       $item = StringToItemParser::getInstance()->parse($reward["item"]);

                         if (isset($reward["name"])) {
                            $item->setCustomName(TextFormat::colorize($reward["name"]));
                        }

                        if (isset($reward["lore"])) {
                            $item->setLore(array_map(function ($line) {
                                return TextFormat::colorize($line);
                            }, $reward["lore"]));
                        }

                        if (isset($reward["nbt"])) {
                            $nbt = $item->getNamedTag();
                            foreach ($reward["nbt"] as $tag => $value) {
                                $nbt->setString($tag, $value);
                            }
                        }

                        if (isset($reward["enchantments"])) {
                            foreach ($reward["enchantments"] as $enchantment) {
                                $enchant = StringToEnchantmentParser::getInstance()->parse($enchantment["enchant"]);
                                if ($enchant !== null) {
                                    $item->addEnchantment(new EnchantmentInstance($enchant, $enchantment["level"]));
                                }
                            }
                        }

                        $sender->getInventory()->addItem($item);
                    }

                    Utils::playSound($sender, $config->getNested("settings.sound"));
                },
                static function (SQLException $exception) use ($sender, $pLevel, $config): void {
                    if ($exception instanceof RecordNotFoundException) {
                        echo 'Account not found';
                        return;
                    }

                    if ($exception instanceof InsufficientFundsException) {
                        $sender->sendMessage(TextFormat::RED . "You don't have enough money to level up!");
                        return;
                    }

                    echo 'An error occurred while updating the balance.';
                }
            );
        }
    }
}