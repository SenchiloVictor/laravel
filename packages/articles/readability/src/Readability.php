<?php

declare(strict_types=1);

namespace Articles\Readability;

class Readability
{
	public function countSyllables(string &$word):int 
	{
		if( ! $_word = preg_replace('`[^a-z]`', '', strtolower(trim($word))))
			return 0;

		$pattern = new Pattern;

		if(isset($pattern->arrProblemWords[$_word]))
			return $pattern->arrProblemWords[$_word];

		$_word = preg_replace($pattern->arrAffix, '', $_word, -1, $intAffixCount);
		$_word = preg_replace($pattern->arrDoubleAffix, '', $_word, -1, $intDoubleAffixCount);
		$_word = preg_replace($pattern->arrTripleAffix, '', $_word, -1, $intTripleAffixCount);

		// Removed non-word characters from word
		$arrWordParts = preg_split('`[^aeiouy]+`', $_word);
		$intWordPartCount = 0;
		foreach ($arrWordParts as $strWordPart) {
			if ($strWordPart <> '')
				++$intWordPartCount;
		}

		// Some syllables do not follow normal rules - check for them
		$intSyllableCount = $intWordPartCount + $intAffixCount + (2 * $intDoubleAffixCount) + (3 * $intTripleAffixCount);

		foreach ($pattern->arrSubSyllables as $strSyllable) {
			$_intSyllableCount = $intSyllableCount;
			$intSyllableCount -= preg_match('`' . $strSyllable . '`', $_word);
		}

		foreach ($pattern->arrAddSyllables as $strSyllable) {
			$_intSyllableCount = $intSyllableCount;
			$intSyllableCount+= preg_match('`' . $strSyllable . '`', $_word);
		}

		$intSyllableCount = ($intSyllableCount == 0) ? 1 : $intSyllableCount;

		return $intSyllableCount;
	}

	public function easeScore(string &$string):float
	{
		if( ! strlen($string))
			return 0;

		$words = explode(' ', $string);
		$syllables = 0;
		$_syllables = 0;

		array_map(function($word) use(&$syllables) {
			$syllables+= $this->countSyllables($word);
		}, $words);

		$sentences = preg_match_all('/([^\.\!\?]+[\.\?\!]*)/', $string);
		$words = str_word_count($string);

		$asl = $words / $sentences;
		$asw = $syllables / $words;

		$score = 206.835 - (1.015 * $asl) - (84.6 * $asw);
		$max = 100;
		$min = 0;

		if($score > $max)
			$score = $max;
		else if($score < $min)
			$score = $min;

		return $score;
	}
}