<?php

namespace xenialdan\libnbs;

/**
 * Represents a series of notes in Note Block Studio.
 * A Layer can have a maximum of one note per tick (20 ticks a second)
 */
class Layer{
	/** @var array<int,Note> */
	private array $notesAtTicks = [];

	public function __construct(public string $name = "", public int $volume = 100, public int $stereo = 100){
	}

	/**
	 * Gets the notes in the Layer with the tick they are created as a map
	 * @return array<int,Note> of notes with the tick they are played at
	 */
	public function getNotesAtTicks() : array{
		return $this->notesAtTicks;
	}

	/**
	 * Sets the notes in the Layer with the tick they are created as a map
	 *
	 * @param array $notesAtTicks
	 */
	public function setNotesAtTicks(array $notesAtTicks) : void{
		$this->notesAtTicks = $notesAtTicks;
	}

	/**
	 * Gets the name of the Layer
	 * @return string
	 */
	public function getName() : string{ return $this->name; }

	/**
	 * Sets the name of the Layer
	 *
	 * @param string $name
	 */
	public function setName(string $name) : void{ $this->name = $name; }

	/**
	 * Gets the note played at a given tick
	 *
	 * @param int $tick
	 *
	 * @return Note|null
	 */
	public function getNote(int $tick) : ?Note{ return $this->notesAtTicks[$tick] ?? null; }

	/**
	 * Sets the given note at the given tick in the Layer
	 *
	 * @param int  $tick
	 * @param Note $note
	 */
	public function setNote(int $tick, Note $note) : void{ $this->notesAtTicks[$tick] = $note; }

	/**
	 * Gets the volume of all notes in the Layer
	 * @return int representing the volume
	 */
	public function getVolume() : int{ return $this->volume; }

	/**
	 * Sets the volume for all notes in the Layer
	 *
	 * @param int $volume
	 */
	public function setVolume(int $volume) : void{ $this->volume = $volume; }

	public function getStereo() : int{ return $this->stereo; }

	public function setStereo(int $stereo) : void{ $this->stereo = $stereo; }
}