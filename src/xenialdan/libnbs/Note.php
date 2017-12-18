<?php

namespace xenialdan\libnbs;

class Note{
	/* short */
	public $tick, $layer;
	/* byte */
	public $instrument, $key;

	public function __construct($tick, $layer, int $inst, int $key){
		$this->tick = $tick;
		$this->layer = $layer;
		$this->instrument = $inst;
		$this->key = $key;
	}
}