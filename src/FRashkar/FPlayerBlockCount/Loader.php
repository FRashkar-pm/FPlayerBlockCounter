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

namespace FRashkar\FPlayerBlockCount;

use FRashkar\FPlayerBlockCount\Command\FPlayerBlockCommand;
use FRashkar\FPlayerBlockCount\Events\EventListener;
use FRashkar\FPlayerBlockCount\Entities\{TopBreakNPC, TopPlaceNPC};
use pocketmine\entity\{EntityDataHelper, EntityFactory, Human, Skin};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\{Config, SingletonTrait};
use pocketmine\world\World;

class Loader extends PluginBase
{
    use SingletonTrait;

    public Config $rbreak;
    public Config $rplace;

    public const FPLAYERBLOCKCOUNT_CONSOLE = "Use this command in-game please!";
    public const FPLAYERBLOCKCOUNT_NO_PERMS = "You don't have permission to use this command!";
    public const FPLAYERBLOCKCOUNT_NO_PLAYER = "There are no player with that name!";
    public const FPLAYERBLOCKCOUNT_USAGE = "/fplayerblockcount <player|settop|top>";

    public function onEnable() : void
    {
        self::setInstance($this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("fplayerblockcount", new FPlayerBlockCommand($this));
        $this->rbreak = new Config($this->getDataFolder() . "/break.yml", Config::YAML);
        $this->rplace = new Config($this->getDataFolder() . "/place.yml", Config::YAML);
        $this->saveDefaultConfig();

        EntityFactory::getInstance()->register(TopBreakNPC::class, function(World $world, CompoundTag $tag): TopBreakNPC
        {
            return new TopBreakNPC(EntityDataHelper::parseLocation($tag, $world), Human::parseSkinNBT($tag), $tag);
        }, ["humanclass::npcentitybreak"]);

        EntityFactory::getInstance()->register(TopPlaceNPC::class, function(World $world, CompoundTag $tag): TopPlaceNPC
        {
            return new TopPlaceNPC(EntityDataHelper::parseLocation($tag, $world), Human::parseSkinNBT($tag), $tag);
        }, ["humanclass::npcentityplace"]);
    }

    public function getTopBlockBreak() : string
    {
        $data = $this->rbreak->getAll();
        $msg = '';

        if (count($data) > 0)
        {
            arsort($data);
            $num = 1;

            foreach ($data as $name => $value)
            {
                $msg .= "§a» §fTop (" . $num . ")§e " . $name . "§f, " . $value . " break blocks" . "\n";
                
                if ($num >= 10)
                {
                    break;
                }
                ++$num;
            }
        }

        return $msg;
    }

    public function getTopBlockPlace() : string
    {
        $data = $this->rplace->getAll();
        $msg = '';

        if (count($data) > 0)
        {
            arsort($data);
            $num = 1;

            foreach ($data as $name => $value)
            {
                $msg.= "§a» §fTop (". $num. ")§e ". $name. "§f, ". $value. " place blocks". "\n";
                
                if ($num >= 10)
                {
                    break;
                }
                ++$num;
            }
        }

        return $msg;
    }

    public function getBlockBreakPlayer(string $playerName, array $data) : string
    {
        foreach ($data as $name => $value)
        {
            if ($name === $playerName) 
            {
                return "$value";
            }
        }

        return "Player not found";
    }

    public function getBlockPlacePlayer(string $playerName, array $data) : string
    {
        foreach ($data as $name => $value)
        {
            if ($name === $playerName) 
            {
                return "$value";
            }
        }

        return "Player not found";
    }

    public function getTopBlockBreakPlayerName() : string
    {
        $data = $this->rbreak->getAll();
        if (count($data) > 0)
        {
            arsort($data);
            $num = 1;

            foreach ($data as $name => $value)
            {
                if ($num === 1)
                {
                    return $name;
                }
            }
        }
        return "Player not found";
    }

    public function getPlayerSkin(string $playerName) : ?Skin
    {
        $player = $this->getServer()->getPlayerByPrefix($playerName);
        if ($player instanceof Player)
        {
            return $player->getSkin();
        }
        return null;
    }

    public function getTopBlockPlacePlayerName() : string
    {
        $data = $this->rbreak->getAll();
        if (count($data) > 0)
        {
            arsort($data);
            $num = 1;

            foreach ($data as $name => $value)
            {
                if ($num === 1)
                {
                    return $name;
                }
            }
        }
        return "Player not found";
    }
}