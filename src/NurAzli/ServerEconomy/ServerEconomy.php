<?php

namespace NurAzli\ServerEconomy;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ServerEconomy extends PluginBase implements Listener {

    public function onEnable() {
        $this->getLogger()->info("ServerEconomy has been enabled.");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable() {
        $this->getLogger()->info("ServerEconomy has been disabled.");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "balance") {
            if ($sender instanceof Player) {
                $playerName = $sender->getName();
                $balance = ServerEconomyPlayerMoney::getInstance()->getPlayerBalance($playerName);
                $sender->sendMessage(TextFormat::GREEN . "Your balance: " . TextFormat::YELLOW . "$" . $balance);
                return true;
            } else {
                $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
                return false;
            }
        } elseif ($command->getName() === "pay") {
            if ($sender instanceof Player) {
                if (isset($args[0]) && isset($args[1])) {
                    $targetPlayer = $args[0];
                    $amount = (int)$args[1];

                    $senderName = $sender->getName();
                    $targetBalance = ServerEconomyPlayerMoney::getInstance()->getPlayerBalance($targetPlayer);

                    if ($amount > 0 && ServerEconomyPlayerMoney::getInstance()->getPlayerBalance($senderName) >= $amount) {
                        ServerEconomyPlayerMoney::getInstance()->setPlayerBalance($senderName, ServerEconomyPlayerMoney::getInstance()->getPlayerBalance($senderName) - $amount);
                        ServerEconomyPlayerMoney::getInstance()->setPlayerBalance($targetPlayer, $targetBalance + $amount);
                        $sender->sendMessage(TextFormat::GREEN . "Successfully transferred $" . $amount . " to " . $targetPlayer . ".");
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Invalid amount or insufficient funds.");
                    }
                    return true;
                } else {
                    $sender->sendMessage(TextFormat::RED . "Usage: /pay <player> <amount>");
                    return false;
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
                return false;
            }
        }
        return false;
    }
}
