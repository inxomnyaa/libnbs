<?php

namespace xenialdan\libnbs;

class Layer{
	/** @var string */
	public $name;
	/** @var int */
	public $id, $volume, $notes;

	public function __construct(int $id, string $name, int $volume, int $notes){
		$this->id = $id;
		$this->name = $name;
		$this->volume = $volume;
		$this->notes = $notes;
	}
}