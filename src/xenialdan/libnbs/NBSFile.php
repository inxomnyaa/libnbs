<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace xenialdan\libnbs;


use pocketmine\nbt\NBT;

abstract class NBSFile extends NBT{
	const INSTRUMENT_PIANO = 0;
	const INSTRUMENT_DOUBLE_BASS = 1;
	const INSTRUMENT_BASS_DRUM = 2;
	const INSTRUMENT_SNARE = 3;
	const INSTRUMENT_CLICK = 4;
	const INSTRUMENT_GUITAR = 5;
	const INSTRUMENT_FLUTE = 6;
	const INSTRUMENT_BELL = 7;
	const INSTRUMENT_CHIME = 8;
	const INSTRUMENT_XYLOPHONE = 9;

	public $length = 0;
	public $height = 0;
	public $name = "";
	public $author = "";
	public $originalAuthor = "";
	public $songDescription = "";
	public $tempo = 0; // $tempo / 100 = tps
	public $autoSaving = 0;
	public $autoSavingDuration = 60;
	public $timeSignature = 4;
	public $minutesSpent = 0;
	public $leftClicks = 0;
	public $rightClicks = 0;
	public $blocksAdded = 0;
	public $blocksRemoved = 0;
	public $importedFileName = "";

	public static $notes = [];

	public function __construct($path){
		parent::__construct(self::LITTLE_ENDIAN);
		$fopen = fopen($path, "r");
		$this->buffer = fread($fopen, filesize($path));
		fclose($fopen);
		### HEADER ###
		$this->length = $this->getShort();
		$this->height = $this->getShort();
		$this->name = $this->getString();
		$this->author = $this->getString();
		$this->originalAuthor = $this->getString();
		$this->songDescription = $this->getString();
		$this->tempo = $this->getShort();
		$this->autoSaving = $this->getByte();
		$this->autoSavingDuration = $this->getByte();
		$this->timeSignature = $this->getByte();
		$this->minutesSpent = $this->getInt();
		$this->leftClicks = $this->getInt();
		$this->rightClicks = $this->getInt();
		$this->blocksAdded = $this->getInt();
		$this->blocksRemoved = $this->getInt();
		$this->importedFileName = $this->getString();
		### DATA ###
		$tick = -1;
		$jumps = 0;
		while (true){
			$jumps = $this->getShort();
			if ($jumps == 0) break;
			$tick += $jumps;
			$layer = -1;
			while (true){
				$jumps = $this->getShort();
				if ($jumps == 0) break;
				$layer += $jumps;
				$instrument = $this->getByte();
				$key = $this->getByte();
				self::addNoteBlock($tick, $layer, $instrument, $key);
			}
		}
		//TODO read further to figure out layer volume
	}

	public static function addNoteBlock($tick, $layer, $instrument, $key){
		self::$notes[$tick] = [$instrument, $key, $layer];
	}

	public static function getNotes(){
		return self::$notes;
	}

	/**
	 * @param int $tick
	 * @return int the note or -1 if no note exists at that tick
	 */
	public static function getNoteAtTick(int $tick){
		return self::$notes[$tick]??-1;
	}
}