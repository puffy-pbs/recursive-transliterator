<?php

namespace PuffyPBS\Transliterator;

use Generator;

class RecursiveTransliterator
{
    /** @var int DEFAULT_CHUNK_SIZE */
    private const DEFAULT_CHUNK_SIZE = 8;

    /** @var array $transliterations */
    private $transliterations;

    /** @var array $currentTranslation */
    private $currentTranslation;

    /** @var array $translations */
    private $translations;

    /** @var int $currentIndex */
    private $currentIndex;

    /** @var int $chunkSize */
    private $chunkSize;

    /**
     * @param array $transliterations
     */
    public function __construct(array $transliterations)
    {
        $this->transliterations = $transliterations;
        $this->currentTranslation = [];
        $this->translations = [];
        $this->currentIndex = 0;
        $this->chunkSize = self::DEFAULT_CHUNK_SIZE;
    }

    /**
     * Yields a translation once it combines such
     * @param int $index
     * @param array $translationBuffer
     * @return Generator
     */
    public function generateTranslations(int $index = 0, array $translationBuffer = []): Generator
    {
        if ($index === $this->currentIndex) {
            yield join('', $translationBuffer);
            return;
        }

        foreach ($this->translations[$index] as $translation) {
            array_push($translationBuffer, $translation);
            yield from $this->generateTranslations($index + 1, $translationBuffer);
            array_pop($translationBuffer);
        }
    }

    /**
     * Splits the word into letters in order to transliterate it into chunks
     * @param string $word
     * @return void
     */
    public function processData(string $word): void
    {
        $wordAsArray = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
        $wordIntoChunks = array_chunk($wordAsArray, $this->chunkSize);

        foreach ($wordIntoChunks as $wordIntoChunk) {
            $this->translations[$this->currentIndex] = [];
            $this->transliterate(0, join('', $wordIntoChunk));
            $this->currentIndex++;
        }
    }

    /**
     * Makes transliteration of a given string
     * @param int $position
     * @param string $word
     * @return void
     */
    private function transliterate(int $position, string $word): void
    {
        $wordLength = mb_strlen($word);
        if ($position === $wordLength && count($this->currentTranslation) === $wordLength) {
            $currentTranslation = join('', $this->currentTranslation);
            array_push($this->translations[$this->currentIndex], $currentTranslation);
            return;
        }

        $letter = mb_substr($word, $position, 1);
        $letterTransliterations = [$letter];
        if (array_key_exists($letter, $this->transliterations)) {
            $letterTransliterations = $this->transliterations[$letter];
        }

        foreach ($letterTransliterations as $letterTransliteration) {
            array_push($this->currentTranslation, $letterTransliteration);
            $this->transliterate($position + 1, $word);
            array_pop($this->currentTranslation);
        }
    }

}
