<?php

namespace DancingFarm;

use pocketmine\plugin\PluginBase;

class DancingFarm extends PluginBase {
    
    public const CONFIG_VERSION = 2;
    
    public function onEnable() : void {
        $this->saveDefaultConfig();
        $this->checkConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }
    
    private function checkConfig(): void {
        if($this->getConfig()->get("config_version") < self::CONFIG_VERSION){
            $this->getLogger()->notice("your config file is outdate.., creating a new config file");
            $this->replaceConfig();
        } 
        if($this->getConfig()->get("config_version") > self::CONFIG_VERSION){
            $this->getLogger()->notice("your config version is higher than the latest version..."); 
            $this->replaceConfig(); 
        }
    }
    
    private function replaceConfig(): void {
        rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
        $this->saveResource("config.yml");  
    }
}
