<?php

namespace RubyTemple\AutoEXP;

use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use RubyTemple\AutoEXP\Entity\projectile\ExperienceBottle;

class Main extends PluginBase implements Listener {

	public function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents(($this), $this);
		if($this->getConfig()->get('override-experience-bottle')){
			Entity::registerEntity(ExperienceBottle::class, false, ['ThrownExpBottle', 'minecraft:xp_bottle']);
		}
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function isValidWorld(Player $player): bool {
		if($this->getConfig()->get('worlds') === 'all') return true;
		foreach($this->getConfig()->get('worlds') as $worlds){
			if($player->getLevel()->getFolderName() === $worlds){
				return true;
			}
		}
		return false;
	}

	/**
	 * @param BlockBreakEvent $event
	 * @priority MONITOR
	 */
	public function onBlockBreak(BlockBreakEvent $event): void {
		if ($event->isCancelled()) return;
		if($this->getConfig()->get('breaking-experience') and $this->isValidWorld($event->getPlayer())){
			$event->getPlayer()->addXp($event->getXpDropAmount());
			$event->setXpDropAmount(0);
		}
	}

	/**
	 * @param PlayerDeathEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerDeath(PlayerDeathEvent $event): void {
		$player = $event->getPlayer();
		$cause = $player->getLastDamageCause();
		if($this->getConfig()->get('killing-experience') and $this->isValidWorld($event->getPlayer())){
			if($cause instanceof EntityDamageByEntityEvent){
				$damager = $cause->getDamager();
				if($damager instanceof Player){
					$damager->addXp($player->getXpDropAmount());
					$player->setCurrentTotalXp(0);
				}
			}
		}
	}
}