<?php

use PuffyPBS\Transliterator\Languages;
use PuffyPBS\Transliterator\RecursiveTransliteratorFactory;

require_once('autoload.php');

// Create Transliterator
$transliterator = RecursiveTransliteratorFactory
    ::createFromLanguageFile(Languages::BG_EN);

// Process
$transliterator->processData('Вчера какво правихме');

// Generate the translations
$transliterationGenerator = $transliterator->generateTranslations();

// Print
foreach ($transliterationGenerator as $transliteration) {
    echo($transliteration . PHP_EOL);
}
