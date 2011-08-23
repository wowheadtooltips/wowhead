<?php

$stats_conf_enable = Array(
                            /* --base stats-- */
                                'stamina',
                            //    'strength',
                                'intellect',
                            //    'agility',
                            //    'spirit',
                            //    'armor',
			    /*   -- mastery -- */
			    //    'mastery',
                            /*   -- spell damage--   */
                            //     'spell_power',
                            //     'spell_crit',
                            //     'mana_regen',
                            //     'mana_regen_cast',
                            //     'spell_hit',
                            //     'penetration',
							//	   'haste_rating',
                            /*   -- melee damage--   */
                            //       'melee_main_dmg',
                            //       'melee_main_speed',
                            //       'melee_main_dps',
                            //       'melee_off_dmg',
                            //       'melee_off_speed',
                            //       'melee_off_dps',
                            //       'melee_power',
                            //       'melee_hit',
                            //       'melee_crit',
                            //       'melee_expertise',
                            /*   -- ranged damage--   */
                            //       'ranged_power',
                            //       'ranged_dmg',
                            //       'ranged_speed',
                            //       'ranged_dps',
                            //       'ranged_crit',
                            //       'ranged_hit',
                            /*  --defenses--  */
                           //        'defense',
                           //        'dodge',
                           //        'parry',
                           //        'block',
                            //       'resilience',
                            /*  --resistances--  */
                            //       'arcane_resist',
                            //       'fire_resist',
                            //       'frost_resist',
                            //       'shadow_resist',
                            //       'nature_resist',
                            //       'holy_resist',
);

$this->enable_stats ($stats_conf_enable);

switch ($this->main_spec) {
	case 1: // Elemental
		$this->enable_stats (array ('mastery', 'spell_power', 'spell_crit', 'spell_hit', 'mana_regen', 'mana_regen_cast'));
		break;
	case 2: // Enh
		$this->enable_stats (array ('strength', 'agility', 'mastery', 'melee_main_dps', 'melee_off_dps', 'melee_power',
					    'melee_crit', 'melee_hit', 'melee_expertise'));
		break;
	case 3: // Resto
		$this->enable_stats (array ('spirit', 'mastery', 'spell_power', 'spell_crit', 'mana_regen', 'mana_regen_cast'));
		break;
}

?>
