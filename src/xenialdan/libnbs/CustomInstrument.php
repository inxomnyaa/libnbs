<?php

declare(strict_types=1);

namespace xenialdan\libnbs;

class CustomInstrument{
	/** @var string The sound file of the instrument (just the filename, not the path). */
	public string $soundFileName;
	/** @var string Sound name */
	public string $sound = "";

	/**
	 * Creates a CustomInstrument
	 *
	 * @param int    $index The instruments index
	 * @param string $name The name of the instrument
	 * @param string $soundFile The sound file of the instrument (just the filename, not the path)
	 * @param int    $pitch The pitch of the sound file. Just like the note blocks, this ranges from 0-87. Default is 45
	 * @param bool   $pressKey Whether the piano should automatically press keys with this instrument when the marker passes them (0 or 1)
	 */
	public function __construct(public int $index, public string $name, string $soundFile, public int $pitch = 45, public bool $pressKey = false){
		$this->soundFileName = str_replace([".ogg", ".fsb"], "", $soundFile);
		if(strtolower($this->soundFileName) === "pling"){
			$this->sound = "note.pling";
		}
	}

	/**
	 * Gets index of CustomInstrument
	 * @return int index
	 */
	public function getIndex() : int{		return $this->index;	}

	/**
	 * Gets name of CustomInstrument
	 * @return string $name
	 */
	public function getName() : string{		return $this->name;	}

	/**
	 * Gets file name of the sound
	 * @return string file name
	 */
	public function getSoundFileName() : string{		return $this->soundFileName;	}

	/**
	 * Gets the resource pack sound_definitions entry for this CustomInstrument
	 * @return string $resourceEntry
	 */
	public function getSound() : string{		return $this->sound;	}
}