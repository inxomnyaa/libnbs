<?php

namespace xenialdan\libnbs;

class Layer{
	/** @var string */
	public $name;
	/** @var int */
	public $id, $volume, $notes;
    /**
     * This is from OpenNoteBlockStudio
     * How much this layer is panned to the left/right. 0 is 2 blocks right, 100 is centre, 200 is 2 blocks left
     * @var int
     */
    public $stereo = 100;

    public function __construct(int $id, string $name, int $volume, int $notes, int $stereo = 100)
    {
		$this->id = $id;
		$this->name = $name;
		$this->volume = $volume;
		$this->notes = $notes;
        $this->stereo = $stereo;
	}
}