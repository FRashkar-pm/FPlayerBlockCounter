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

namespace FRashkar\FPlayerBlockCounter\Command;

use FRashkar\FPlayerBlockCount\Loader;
use pocketmine\command\{Command, CommandSender};
use pocketmine\player\Player;
use pocketmine\plugin\{Plugin, PluginOwned};

class FPlayerBlockCommand extends Command implements PluginOwned
{
    /** @var Loader */
    public Loader $loader;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
        parent::__construct("fplayerblockcount", "FPlayerBlockCounter Command", "/fplayerblockcount <player|settop|top>", ["fp"]);
        $this->setPermission("fplayerblockcount.command.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) return;

        if (isset($args[0]))
        {
            foreach ($this->loader->getServer()->getOnlinePlayers() as $players)
            {
                if ($players instanceof Player)
                {
                    $pname = $players->getName();
                    switch (strtolower($args[0]))
                    {
                        case "$pname":
                            if ($sender instanceof Player)
                            {
                                $pbreak = $this->loader->getBlockBreakPlayer($pname);
                                $pplace = $this->loader->getBlockPlacePlayer($pname);
                                $sender->sendMessage(">> STATISTICS <<" . "\n" . "$pname break: $pbreak blocks." . "\n" . "$pname place: $pplace blocks.");
                            } else {
                                $sender->sendMessage(Loader::FPLAYERBLOCKCOUNT_CONSOLE);
                            }
                            break;
                        case "settop":
                            if (!$sender->hasPermission("fplayerblockcount.command.settop"))
                            {
                                $sender->sendMessage(Loader::FPLAYERBLOCKCOUNT_NO_PERMS);
                            } else {
                                // TopPlayerBlockCount Leader Board With NPC
                                // Soon!
                            }
                            break;
                        case "top":
                            if ($sender instanceof Player)
                            {
                                $topbreak = $this->loader->getTopBlockBreak();
                                $topplace = $this->loader->getTopBlockPlace();
                                $sender->sendMessage(">> Top Block Leaderboard <<" . "$topbreak" . "/n" . "$topplace");
                            } else {
                                $sender->sendMessage(Loader::FPLAYERBLOCKCOUNT_CONSOLE);
                            }
                            break;
                    }
                }
            }
        }
    }

    public function getOwningPlugin(): Plugin
    {
        return Loader::getInstance();
    }
}