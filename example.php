<?php

require_once('autoload.php');

$transliterator = \PuffyPBS\Transliterator\RecursiveTransliteratorFactory
    ::createFromLanguageFile(\PuffyPBS\Transliterator\Languages::BG_EN);
$transliterator->processData('Вчера какво правихме');
$transliterationGenerator = $transliterator->generateTranslations();
foreach ($transliterationGenerator as $transliteration) {
    echo($transliteration . PHP_EOL);
}