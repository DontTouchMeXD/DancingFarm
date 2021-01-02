<?php

namespace DancingFarm;

use pocketmine\Player;
use pocketmine\block\{
    Block, Crops, Sapling
};
use pocketmine\event\player\PlayerToggleSneakEvent; 
use pocketmine\level\generator\object\Tree;
use pocketmine\utils\Random; 
use pocketmine\event\Listener;
use pocketmine\event\block\BlockGrowEvent;

class EventListener implements Listener{
    
    public function onSneak(PlayerToggleSneakEvent $event) {
        $player = $event->getPlayer();
        $isSneak = $event->isSneaking();
        $start = $player->add(-1, 0, -1);
        $end = $player->add(1, 0, 1);
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
    
    public function grow($block) : void {
        if($block->getDamage() >= 7) return;
        if($block instanceof Sapling){
            if(mt_rand(1,7) === 1){
                Tree::growTree($block->getLevelNonNull(), $block->x, $block->y, $block->z, new Random(mt_rand()), $block->getVariant());
            }
            return;
        }
        if($block instanceof Crops){
            $random = mt_rand(2,5);
            if($block->getDamage() < 7){
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
            return;
        }
    }
}
