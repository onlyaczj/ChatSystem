<?php

declare(strict_types=1);

namespace AEDXDEV\ECMD;

use AEDXDEV\ECMD\args\Argument;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

abstract class BaseCommand extends Command{
    public const ALL_CONSTRAINT = 0;
    public const IN_GAME_CONSTRAINT = 1;

    /** @var array<string, array{args: Argument[], handler: callable, permission: ?string, constraint: int, name: string}> */
    private array $subCommands = [];
    private string $permissionMessageText = "§cYou don't have permission.";

    public function __construct(protected Plugin $owningPlugin, string $name, string $description = "", array $aliases = []){
        parent::__construct($name, $description, null, $aliases);
        $this->prepare();
    }

    abstract protected function prepare() : void;
    abstract public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void;

    /** @param Argument[] $arguments */
    protected function registerSubCommand(string $name, array $arguments, callable $handler, ?string $permission = null, int $constraint = self::ALL_CONSTRAINT) : void{
        $this->subCommands[strtolower($name)] = [
            "args" => $arguments,
            "handler" => $handler,
            "permission" => $permission,
            "constraint" => $constraint,
            "name" => $name
        ];
    }

    public function setPermissionMessage(string $message) : void{ $this->permissionMessageText = $message; }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!$this->testPermissionSilent($sender)){
            $sender->sendMessage($this->permissionMessageText);
            return true;
        }
        if(count($args) === 0){
            $this->onRun($sender, $commandLabel, []);
            return true;
        }
        $subName = strtolower((string)array_shift($args));
        $sub = $this->subCommands[$subName] ?? null;
        if($sub === null){
            $this->onRun($sender, $commandLabel, $args);
            return true;
        }
        $permission = $sub["permission"];
        if($permission !== null && !$sender->hasPermission($permission)){
            $sender->sendMessage($this->permissionMessageText);
            return true;
        }
        if($sub["constraint"] === self::IN_GAME_CONSTRAINT && !$sender instanceof \pocketmine\player\Player){
            $sender->sendMessage("§cUse this command in-game only.");
            return true;
        }
        try{
            $parsed = [];
            $offset = 0;
            foreach($sub["args"] as $argument){
                $parsed[$argument->getName()] = $argument->parse($args, $offset);
            }
            ($sub["handler"])(...[$sender, $parsed]);
        }catch(\Throwable $e){
            $sender->sendMessage("§cInvalid command usage. §7" . $e->getMessage());
        }
        return true;
    }

    public function tabComplete(CommandSender $sender, string $aliasUsed, array $args) : array{
        if(count($args) <= 1){
            $current = strtolower($args[0] ?? "");
            $list = [];
            foreach($this->subCommands as $sub){
                if(($sub["permission"] === null || $sender->hasPermission($sub["permission"])) && str_starts_with(strtolower($sub["name"]), $current)){
                    $list[] = $sub["name"];
                }
            }
            return $list;
        }
        $sub = $this->subCommands[strtolower((string)$args[0])] ?? null;
        if($sub === null){ return []; }
        $argument = $sub["args"][count($args) - 2] ?? null;
        return $argument instanceof Argument ? $argument->suggest($sender, (string)end($args)) : [];
    }
}
