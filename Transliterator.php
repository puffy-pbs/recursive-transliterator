<?php

namespace PuffyPBS\Transliterator;

class RecursiveTransliterator
{
    private $transliterations;
	private $currentTranslation;
	private $translations;
	private $currentIndex;
	private $chunkSize;

    public function __construct(array $transliterations)
    {
        $this->transliterations = $transliterations;
        $this->currentTranslation = [];
        $this->translations = [];
        $this->currentIndex = 0;
        $this->chunkSize = 8;
	}

    /**
     * @desc yield translation once it combines such
     * @param int $index
     * @param array $translationBuffer
     * @return Generator|string
     */
    public function generateTranslations(int $index = 0, array $translationBuffer = []): \Generator
    {
        if ($index === $this->currentIndex) {
            $generatedTranslation = join('', $translationBuffer);
            yield $generatedTranslation;
            return;
        }

        foreach ($this->translations[$index] as $translation) {
            array_push($translationBuffer, $translation);
            yield from $this->generateTranslations($index + 1, $translationBuffer);
            array_pop($translationBuffer);
        }
    }

    /**
     * @desc split the word into letters in order to transliterate it into chunks
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
     * @desc makes transliteration of a given string
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
