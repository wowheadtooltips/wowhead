<?php
/**
* Wowhead Tooltips - Russian Language Pack
* By: Adam "crackpot" Koch (support@wowhead-tooltips.com)
**/

/**
    Copyright (C) 2010  Adam Koch (email : support@wowhead-tooltips.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

// Translated By: Ksidden <http://forums.wowhead-tooltips.com/profile/Ksidden>

// ***NOTE***
// Some words or phrases were translated using Google's translator, if they are incorrect
// contact me at support@wowhead-tooltips.com and I will correct it.
$lang_array = array(
	// general errors
	'notfound'			=>	'{type} "{name}" не найден.',
	'curl_fail'			=>	'Не получилось найти URL, ни с cURL, ни fopen',		// failed to get XML
	'faction_fail'		=>	'Фракция {name} несовместима с этим сценарием.',	// incompatible tooltip

	// types
	'achievement'		=>	'Достижение',
	'item'				=>	'Предмет',
	'item_icon'			=>	'Предмет',
	'itemset'			=>	'Комплект',
	'spell'				=>	'Заклинание',
	'quest'				=>	'Задание',
	'profile'			=>	'Профиль',
	'craft'				=>	'Професии',
	'npc'				=>	'НИП',
	'zone'				=>	'Зона',
	'object'			=>	'Объект',
	'faction'			=>	'Фракция',
	'enchant'			=>	'Enchant',
	'title'				=>	'Название',
	'event'				=>	'Событие',
	'pet'				=>	'Pet',
	'stats'				=>	'Statistic',
	'race'				=>	'Race',
	'class'				=>	'Class',
	'talents'			=>	'Talents',
	'currency'			=>	'Currency',
	'profession'		=>	'Profession',

	/*
	 * Armory Module
	 */

	// armory and guild
	'armory_blocked'	=>	'Проблема. Возможно вы заблокированы к Оружейной.',
	'char_not_found'	=>	'Персонаж не найден',
	'char_no_data'		=>	'Персонаж недоступен',
	'guild_not_found'	=>	'Ошибка. Не могу найти такой гильдии',

	// misc area
	'achievements'		=>	'Достижения',
	'achievement_pts'	=>	'Очки Достижений',
	'avg_ilevel'		=>	'Средний ilvl',
	'lifetime_hk'		=>	'Пожизненная HKs',

	// base stats
	'stamina'			=>	'Выносливость',
	'intellect'			=>	'Интелект',
	'strength'			=>	'Сила',
	'agility'			=>	'Ловкость',
	'spirit'			=>	'Дух',
	'armor'				=>	'Броня',

	// spell damage and crit
	'arcane_dmg'		=>	'Урон тайной магией',
	'arcane_crit'		=>	'Шанс крита тайной',
	'fire_dmg'			=>	'Урон огненной магией',
	'fire_crit'			=>	'Шанс крита огненной',
	'frost_dmg'			=>	'Урон магией льда',
	'frost_crit'		=>	'Шанс крита ледяной',
	'shadow_dmg'		=>	'Урон магией тьмы',
	'shadow_crit'		=>	'Шанс крита тёмной',
	'holy_dmg'			=>	'Урон магией света',
	'holy_crit'			=>	'Шанс крита светлой',
	'nature_dmg'		=>	'Урон силами природы',
	'nature_crit'		=>	'Шанс крита природной',

	// spell hit, haste, and penetration
	'spell_hit'			=>	'Шанс попасть заклинанием',
	'haste'				=>	'Рейтинг скорости',		// spell, ranged, or melee
	'spell_pen'			=>	'Проникающая способность заклинаний',

	// melee shizzle
	'melee_main_dmg'	=>	'Урон правой бб',
	'melee_main_dps'	=>	'УВС правой бб',
	'melee_main_speed'	=>	'Скорость правой бб',
	'melee_off_dmg'		=>	'Урон левой бб',
	'melee_off_dps'		=>	'УВС левой бб',
	'melee_off_speed'	=>	'Скорость левой бб',
	'melee_power'		=>	'Сила атаки бб',
	'melee_hit'			=>	'Шанс попасть бб',
	'melee_crit'		=>	'Шанс критического урона бб',
	'melee_expertise'	=>	'Меткость бб',
	
	// tanking stats
	'defense'			=>	'Защита',
	'parry_chance'		=>	'Шанс парировать',
	'dodge_chance'		=>	'Шанс увернутся',
	'block_chance'		=>	'Шанс заблокировать',
	'resilience'		=>	'Устойчивость',

	// healing and associated stats
	'healing'			=>	'Лечение',
	'mana_regen'		=>	'Восполнение маны',
	'mana_regen_cast'	=>	'Восполнение маны (При чтении Заклинаний)',

	// ranged stats
	'ranged_dmg'		=>	'Урон в дб',
	'ranged_dps'		=>	'УВС в дб',
	'ranged_speed'		=>	'Скорость дб',
	'ranged_hit'		=>	'Шанс попасть в дб',
	'ranged_crit'		=>	'Шанс критического урона в дб',
	'ranged_power'		=>	'сила атаки дб',

	// recruit
	'already_used'		=>	'Тэг набора уже использован на этой странице. Только 1 раз на страницу',
	'invalid_xml'		=>	'Возвращаемых запросом недействительными XML. Пожалуйста, попробуйте еще раз.',
	'untalented'		=>	'Символ предоставленная бездарный.',
	'no_char_breakdown'	=>	'Символ дерева талантов разбивка не существует.',
	
	// gear slots
	'ammo'				=>	'Патрон',
	'head'				=>	'Голова',
	'neck'				=>	'Шея',
	'shoulder'			=>	'Плечо',
	'shirt'				=>	'Рубашка',
	'chest'				=>	'Грудь',
	'belt'				=>	'Пояс',
	'legs'				=>	'Legs',
	'feet'				=>	'Ноги',
	'wrist'				=>	'Запястье',
	'gloves'			=>	'Перчатки',
	'ring1'				=>	'Кольцо 1',
	'ring2'				=>	'Кольцо 2',
	'trinket1'			=>	'Брелок 1',
	'trinket2'			=>	'Брелок 2',
	'back'				=>	'Назад',
	'main_hand'			=>	'Главное Hand',
	'off_hand'			=>	'Off Hand',
	'ranged'			=>	'Ranged',
	'tabard'			=>	'Tabard',
	
	// reputation
	'hated'				=>	'Hated',
	'hostile'			=>	'Враждебные',
	'unfriendly'		=>	'Плохие отношения с',
	'neutral'			=>	'Нейтральные',
	'friendly'			=>	'Дружественные',
	'honored'			=>	'Заслуженная',
	'revered'			=>	'Чтимые',
	'exalted'			=>	'Возвышенный',
	
	/*
	 * Talent Tree Names
	 */
	// death knight
	'deathknight_1' 	=> 'кровь',
	'deathknight_2'		=> 'Мороз',
	'deathKnight_3'		=> 'Unholy',
	
	// druid
	'druid_1' 			=> 'Сбалансированное',
	'druid_2' 			=> 'Ферал',
	'druid_3' 			=> 'реставрация',
	
	// hunter
	'hunter_1' 			=> 'Повелитель зверей',
	'hunter_2' 			=> 'Marksmanship',
	'hunter_3' 			=> 'выживание',
	
	// mage
	'mage_1' 			=> 'Arcane',
	'mage_2' 			=> 'огонь',
	'mage_3' 			=> 'Мороз',
	
	// paladin
	'paladin_1' 		=> 'святой',
	'paladin_2' 		=> 'защите',
	'paladin_3' 		=> 'Возмездие',
	
	// priest
	'priest_1'	 		=> 'дисциплины',
	'priest_2'			=> 'святой',
	'priest_3' 			=> 'тени',
	
	// rogue
	'rogue_1' 			=> 'Убийство',
	'rogue_2' 			=> 'Борьба',
	'rogue_3' 			=> 'тонкости',
	
	// shaman 
	'shaman_1'		 	=> 'Elemental',
	'shaman_2' 			=> 'повышение',
	'shaman_3' 			=> 'реставрация',
	
	// warlock
	'warlock_1' 		=> 'колдовство',
	'warlock_2' 		=> 'демонология',
	'warlock_3'	 		=> 'уничтожении',
	
	// warrior
	'warrior_1' 		=> 'оружие',
	'warrior_2' 		=> 'Ярость',
	'warrior_3'	 		=> 'охрана'
);

?>
