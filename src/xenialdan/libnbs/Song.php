<?php

declare(strict_types=1);

namespace xenialdan\libnbs;

/**
 * Represents a Note Block Studio project
 */
class Song
{
    /** @var array<int,Layer> */
    private array $layers;
    /** @var int */
    private int $songHeight;
    /** @var int */
    private int $length;
    /** @var string */
    private string $title;
    /** @var string */
    private string $path;
    /** @var string */
    private string $author;
    /** @var string */
    private string $description;
    /** @var float */
    private float $speed;
    /** @var float */
    private float $delay;
    /** @var CustomInstrument[] */
    private array $customInstruments = [];
    /** @var int */
    private int $firstCustomInstrumentIndex;

    /**
     * Song constructor.
     * @param float $speed
     * @param array $layers
     * @param int $songHeight
     * @param int $length
     * @param string $title
     * @param string $author
     * @param string $description
     * @param string $path
     * @param int $firstCustomInstrumentIndex
     * @param CustomInstrument[] $customInstruments
     */
    public function __construct(float $speed, array $layers, int $songHeight, int $length, string $title, string $author, string $description, string $path, int $firstCustomInstrumentIndex, array $customInstruments)
    {
        $this->speed = $speed;
        $this->delay = 20 / $speed;
        $this->layers = $layers;
        $this->songHeight = $songHeight;
        $this->length = $length;
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->path = $path;
        $this->firstCustomInstrumentIndex = $firstCustomInstrumentIndex;
        $this->customInstruments = $customInstruments;
    }

    /**
     * Gets all Layers in this Song and their index
     * @return Array of Layers and their index
     */
    public function getLayerHashMap(): array
    {
        return $this->layers;
    }

    /**
     * Gets the Song's height
     * @return int Song height
     */
    public function getSongHeight(): int
    {
        return $this->songHeight;
    }

    /**
     * Gets the length in ticks of this Song
     * @return int length of this Song
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Gets the title / name of this Song
     * @return string title of the Song
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Gets the author of the Song
     * @return string author
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Returns the File from which this Song is sourced
     * @return string file of this Song
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Gets the description of this Song
     * @return string description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Gets the speed (ticks per second) of this Song
     * @return float speed
     */
    public function getSpeed(): float
    {
        return $this->speed;
    }

    /**
     * Gets the delay of this Song
     * @return float delay
     */
    public function getDelay(): float
    {
        return $this->delay;
    }

    /**
     * Gets the CustomInstruments made for this Song
     * @return array|CustomInstrument[] of CustomInstruments
     * @see CustomInstrument
     */
    public function getCustomInstruments(): array
    {
        return $this->customInstruments;
    }

    /**
     * Gets the index of the first custom instrument
     * @return int $index
     */
    public function getFirstCustomInstrumentIndex(): int
    {
        return $this->firstCustomInstrumentIndex;
    }

    public function __toString()
    {
        return "Song {$this->getTitle()} (" . basename($this->path) . "), author {$this->getAuthor()}, description {$this->getDescription()}, length {$this->getLength()}, speed {$this->getSpeed()}, delay {$this->getDelay()}, height {$this->getSongHeight()}, count ".count($this->getLayerHashMap());
    }

}