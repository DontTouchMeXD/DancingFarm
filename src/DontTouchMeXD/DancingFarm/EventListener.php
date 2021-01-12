<?php

namespace DontTouchMeXD\DancingFarm;

use pocketmine\Player;
use pocketmine\block\{
    Block, Crops, Sapling
};
use pocketmine\event\player\PlayerToggleSneakEvent; 
use pocketmine\level\generator\object\Tree;
use pocketmine\utils\{
    Random,
    Config
}; 
use pocketmine\event\Listener;
use pocketmine\event\block\BlockGrowEvent;

class EventListener implements Listener{
    
    /** @var DancingFarm $plugin */
    public $plugin;
    
    /** @var Config $cfg */
    public $cfg;
    
    public function __construct(DancingFarm $plugin) {
        $this->plugin = $plugin;
        $this->cfg = $this->plugin->getConfig();
    }
    
    public function onSneak(PlayerToggleSneakEvent $event) {
        $player = $event->getPlayer();
        $isSneak = $event->isSneaking();
        $range = $this->cfg->get("range");
        if($range > 10){
            $range = 10; // prevent lag
        }
        $start = $player->add(-$range, 0, -$range);
        $end = $player->add($range, 0, $range);
        if(!$isSneak){
            for($y = $start->y; $y <= $end->y; ++$y){
                for($z = $start->z; $z <= $end->z; ++$z){
                    for($x = $start->x; $x <= $end->x; ++$x){
                        $block = $player->level->getBlockAt($x, $y, $z);
                        if($block instanceof Crops or $block instanceof Sapling){
                            $this->grow($block);
                        }
                    }
                }
            }
        }
    }
    
    public function grow($block): void {
        if($block instanceof Sapling){
            if(!$this->cfg->get("tree_grow")) return;
            $chance = $this->cfg->get("tree_chance");
            if($chance > 10){
                $chance = 10; 
            }
            if(mt_rand($chance,10) === 1){
                Tree::growTree($block->getLevelNonNull(), $block->x, $block->y, $block->z, new Random(mt_rand()), $block->getVariant());
            }
            return;
        }
        if($block instanceof Crops){
            if(!$this->cfg->get("crops_grow")) return; 
            if(!in_array($block->getId(), $this->cfg->get("crops_list"))) return;
            if($block->getDamage() >= 7) return;
            $random = mt_rand(2,5);
            $crops = clone $block;
            $crops->setDamage($block->getDamage() + $random);
            if($crops->getDamage() > 7){
                $crops->setDamage(7);
            }
            $ev = new BlockGrowEvent($block, $crops);
            $ev->call();
            if(!$ev->isCancelled()){
                $block->getLevelNonNull()->setBlock($block, $ev->getNewState(), true, true);
            }
        }
    }
}
