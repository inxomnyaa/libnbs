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
use pocketmine\Server;
use pocketmine\utils\Binary;

class NBSFile{
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

	public $buffer;
	public $offset;
	public $endianness = NBT::LITTLE_ENDIAN;
	private $data;

	public $length = 0;
	public $layers = 0;
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

	/** @var Note[] */
	public $notes = [];
	/** @var Layer[] */
	public $layerInfo = [];

	public function __construct(string $path){
		$fopen = fopen($path, "r");
		$this->buffer = fread($fopen, filesize($path));
		fclose($fopen);
		### HEADER ###
		$this->length = $this->getShort();
		$this->layers = $this->getShort();
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
		/** @var Note[] $noteblocks */
		$notes = [];
		/** @var int[] $instrumentcount */
		$instrumentcount = [];
		/** @var int[] $layercount */
		$layercount = [];

		$tick = -1;
		$jumps = 0;
		while (true){
			$jumps = $this->getShort();
			if ($jumps === 0) break;
			$tick += $jumps;
			$layer = -1;
			while (true){
				$jumps = $this->getShort();
				if ($jumps === 0) break;
				$layer += $jumps;
				$instrument = $this->getByte();
				$key = $this->getByte();
				$notes[] = new Note($tick, $layer, $instrument, $key);
				if (isset($instrumentcount[$instrument])){
					$instrumentcount[$instrument]++;
				} else{
					$instrumentcount[$instrument] = 1;
				}
				if ($layer < $this->layers){
					if (isset($layercount[$layer])){
						$layercount[$layer]++;
					} else{
						$layercount[$layer] = 1;
					}
				};
			}
		}

		$this->notes = $notes;

		Server::getInstance()->getLogger()->debug("Found " . count($notes) . " notes!");
		Server::getInstance()->getLogger()->debug("Piano: " . ($instrumentcount[self::INSTRUMENT_PIANO] ?? 0));
		Server::getInstance()->getLogger()->debug("Double Bass: " . ($instrumentcount[self::INSTRUMENT_DOUBLE_BASS] ?? 0));
		Server::getInstance()->getLogger()->debug("Bass Drum: " . ($instrumentcount[self::INSTRUMENT_BASS_DRUM] ?? 0));
		Server::getInstance()->getLogger()->debug("Snare Drum: " . ($instrumentcount[self::INSTRUMENT_SNARE] ?? 0));
		Server::getInstance()->getLogger()->debug("Click: " . ($instrumentcount[self::INSTRUMENT_CLICK] ?? 0));
		Server::getInstance()->getLogger()->debug("Guitar: " . ($instrumentcount[self::INSTRUMENT_GUITAR] ?? 0));
		Server::getInstance()->getLogger()->debug("Flute: " . ($instrumentcount[self::INSTRUMENT_FLUTE] ?? 0));
		Server::getInstance()->getLogger()->debug("Bell: " . ($instrumentcount[self::INSTRUMENT_BELL] ?? 0));
		Server::getInstance()->getLogger()->debug("Chime: " . ($instrumentcount[self::INSTRUMENT_CHIME] ?? 0));
		Server::getInstance()->getLogger()->debug("Xylophone: " . ($instrumentcount[self::INSTRUMENT_XYLOPHONE] ?? 0));

		### LAYER INFO ###
		for ($i = 0; $i < $this->layers; $i++){
			$layer = new Layer($i + 1, $this->getString(), $this->getByte(), $layercount[$i] ?? 0);
			$this->layerInfo[] = $layer;
			Server::getInstance()->getLogger()->debug("Layer " . $layer->id . ", Name: " . $layer->name . ", Volume: " . $layer->volume . "%, Note blocks: " . $layer->notes);
		}

		### CUSTOM INSTRUMENTS - UNUSED ###
	}

	/**
	 * @return Note[]
	 */
	public function getNotes(){
		return $this->notes;
	}

	/**
	 * @param int $tick
	 * @return Note[]
	 */
	public function getNotesAtTick(int $tick){
		$notes = [];
		foreach ($this->notes as $note){
			if ($note->tick === $tick) $notes[] = $note;
		}
		return $notes;
	}

	/**
	 * @return Layer[]
	 */
	public function getLayerInfo(): array{
		return $this->layerInfo;
	}

	public function get($len){
		if($len < 0){
			$this->offset = strlen($this->buffer) - 1;
			return "";
		}elseif($len === true){
			return substr($this->buffer, $this->offset);
		}

		return $len === 1 ? $this->buffer{$this->offset++} : substr($this->buffer, ($this->offset += $len) - $len, $len);
	}

	public function getString(bool $network = false){
		return $this->get(unpack("I", $this->get(4))[1]);
	}

	public function getShort() : int{
		return $this->endianness === NBT::BIG_ENDIAN ? Binary::readShort($this->get(2)) : Binary::readLShort($this->get(2));
	}

	public function getByte() : int{
		return Binary::readByte($this->get(1));
	}

	public function getInt(bool $network = false) : int{
		if($network === true){
			return Binary::readVarInt($this->buffer, $this->offset);
		}
		return $this->endianness === NBT::BIG_ENDIAN ? Binary::readInt($this->get(4)) : Binary::readLInt($this->get(4));
	}
}