<?php

namespace inxomnyaa\libnbs;

/**
 * Represents a note played; contains the instrument and the key
 */
final class Note{
	public function __construct(public int $instrument, public int $key){
	}

	/**
	 * Gets instrument number
	 * @return int
	 */
	public function getInstrument() : int{ return $this->instrument; }

	/**
	 * Sets instrument number
	 *
	 * @param int $instrument
	 */
	public function setInstrument(int $instrument) : void{
		$this->instrument = $instrument;
	}

	public function getKey() : int{ return $this->key; }

	public function setKey(int $key) : void{
		$this->key = $key;
	}

}