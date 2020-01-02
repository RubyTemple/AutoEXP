<?php


namespace RubyTemple\AutoEXP\Entity\projectile;


use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\utils\Color;
use function mt_rand;

class ExperienceBottle extends \pocketmine\entity\projectile\ExperienceBottle{

	public function onHit(ProjectileHitEvent $event) : void{
		$this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_PARTICLE_SPLASH, (new Color(0x38, 0x5d, 0xc6))->toARGB());
		$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_GLASS);
		$launcher = $event->getEntity()->getOwningEntity();
		if($launcher instanceof Player){
			$launcher->addXp(mt_rand(3, 11),true);
		}
	}
}