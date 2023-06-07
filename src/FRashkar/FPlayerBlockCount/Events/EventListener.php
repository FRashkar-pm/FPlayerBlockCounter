<?php

/**
*  ______ _____           _     _                                     
* |  ____|  __ \         | |   | |                                    
* | |__  | |__) |__ _ ___| |__ | | ____ _ _ __ ______ _ __  _ __ ___  
* |  __| |  _  // _` / __| '_ \| |/ / _` | '__|______| '_ \| '_ ` _ \ 
* | |    | | \ \ (_| \__ \ | | |   < (_| | |         | |_) | | | | | |
* |_|    |_|  \_\__,_|___/_| |_|_|\_\__,_|_|         | .__/|_| |_| |_|
*                                                    | |              
*                                                    |_|              
* The author of this plugin is FRashkar-pm
* https://github.com/FRashkar-pm
* Discord: FireRashkar#1519
*/

namespace FRashkar\FPlayerBlockCount\Events;

use pocketmine\event\Listener;
use pocketmine\event\block\{BlockBreakEvent, BlockPlaceEvent};
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use FRashkar\FPlayerBlockCount\Loader;

class EventListener implements Listener
{
    /** @var Loader */
    public Loader $loader;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        if (!$this->loader->rbreak->get($player->getName()))
        {
            $this->loader->rbreak->set($player->getName(), 0);
            $this->loader->rbreak->save();
        }
        if (!$this->loader->rplace->get($player->getName()))
        {
            $this->loader->rplace->set($player->getName(), 0);
            $this->loader->rplace->save();
        }
    }

    public function onBlockBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $break = $this->addBlockBreak($player);
        $tipbreak = $this->loader->getConfig()->get("message-break-tip");
        $msg = str_replace(["{player}", "{break}"], [$player->getName(), $break], $tipbreak);
        $player->sendTip($msg);

        return;
    }

    public function onBlockPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        $place = $this->addBlockPlace($player);
        $tipplace = $this->loader->getConfig()->get("message-place-tip");
        $msg = str_replace(["{player}", "{place}"], [$player->getName(), $place], $tipplace);
        $player->sendTip($msg);

        return;
    }

    public function addBlockBreak(Player $player): int
    {
        $rb = $this->loader->rbreak;
        $rb->set($player->getName(), $rb->get($player->getName()) +1);
        $rb->save();

        $array = [];
        foreach ($rb->getAll() as $name => $value)
        {
            $array[$name] = $value;
            $rb->remove($name);
        }

        arsort($array);
        foreach ($array as $name => $value)
        {
            $rb->set($name, $value);
            $rb->save();
        }
        
        return $rb->get($player->getName());
    }

    public function addBlockPlace(Player $player): int
    {
        $rp = $this->loader->rplace;
        $rp->set($player->getName(), $rp->get($player->getName()) +1);
        $rp->save();

        $array = [];
        foreach ($rp->getAll() as $name => $value)
        {
            $array[$name] = $value;
            $rp->remove($name);
        }

        arsort($array);
        foreach ($array as $name => $value)
        {
            $rp->set($name, $value);
            $rp->save();
        }

        return $rp->get($player->getName());
    }
}