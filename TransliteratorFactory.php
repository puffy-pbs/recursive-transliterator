<?php

namespace PuffyPBS\Transliterator;

use http\Exception\InvalidArgumentException;

class RecursiveTransliteratorFactory
{
    /**
     * Creating a recursive transliterator from a language file
     * @param string $language
     * @return RecursiveTransliterator
     */
    public static function createFromLanguageFile(string $language): RecursiveTransliterator
    {
        $languagePath = LANGUAGES_FILES_PATH . $language . '.php';
        if (!file_exists($languagePath)) {
            throw new InvalidArgumentException('File does not exist');
        }

        $translations = require_once($languagePath);
        return new RecursiveTransliterator($translations);
    }

    /**
     * Creating a recursive transliterator from a language array
     * @param array $transliterations
     * @return RecursiveTransliterator
     */
    public static function createFromTransliterationsArray(array $transliterations): RecursiveTransliterator
    {
        if (empty($transliterations)) {
            throw new InvalidArgumentException('');
        }

        $isTransliterationsValid = array_filter($transliterations, function ($transliteration, $letter) {
            return is_string($letter) && is_array($transliteration);
        }, ARRAY_FILTER_USE_BOTH);

        if (count($isTransliterationsValid) !== count($transliterations)) {
            throw new InvalidArgumentException('Transliterations are not valid');
        }

        return new RecursiveTransliterator($transliterations);
    }
}
