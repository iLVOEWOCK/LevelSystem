<?php

namespace xtcy\leveling;

use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use xtcy\leveling\command\LevelCommand;
use xtcy\leveling\command\LevelsCommand;
use xtcy\leveling\listener\EventListener;
use xtcy\leveling\utils\Level;
use xtcy\leveling\utils\QueryConstants;

class Loader extends PluginBase {

    private static DataConnector $database;

    private static Level $sessionManager;

    public static Loader $loader;

    public function onLoad(): void
    {
        self::$loader = $this;
    }

    public function onEnable(): void
    {
        $settings = [
            "type" => "sqlite",
            "sqlite" => ["file" => "sqlite.sql"],
            "worker-limit" => 1
        ];

        self::$database = libasynql::create(self::getInstance(), $settings, ["sqlite" => "sqlite.sql"]);
        self::$database->executeGeneric(QueryConstants::PLAYERS_INIT);
        self::$database->waitAll();

        self::$sessionManager = new Level($this);
        $this->saveDefaultConfig();
        //$this->saveLanguageFiles();
        $this->saveResource("language/ENG-def.yml");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->registerAll("leveling", [
            new LevelCommand($this, "levelup", "Increase your level"),
            new LevelsCommand($this, "level", "View your or another players level", ["levels"])
        ]);
    }

    public static function getInstance() : Loader {
        return self::$loader;
    }

    public static function getDatabase() : DataConnector
    {
        return self::$database;
    }

    public static function getSessionManager() : Level
    {
        return self::$sessionManager;
    }

    private function saveLanguageFiles(): void {
        $resourceFolder = $this->getDataFolder() . 'language/';

        if (!is_dir($resourceFolder)) {
            @mkdir($resourceFolder, 0777, true);
        }

        $files = scandir($resourceFolder);

        foreach ($files as $file) {
            if ($file !== "." && $file !== "..") {
                $resourcePath = $resourceFolder . $file;

                if (!file_exists($resourcePath)) {
                    $this->saveResource('language/' . $file);
                }
            }
        }
    }

}