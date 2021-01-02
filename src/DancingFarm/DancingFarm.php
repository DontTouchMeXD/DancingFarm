<?php

namespace DancingFarm;

use pocketmine\plugin\PluginBase;

class DancingFarm extends PluginBase {
    
    public function onEnable() : void {
        $this->saveResource("config.yml");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }
}
