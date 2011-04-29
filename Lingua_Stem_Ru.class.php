<?php

/**
 * @file
 * The class from
 * This class was initially get from http://forum.dklab.ru/php/advises/HeuristicWithoutTheDictionaryExtractionOfARootFromRussianWord.html
 *
 * @author andyceo http://andyceo.ruware.com/
 */

class Lingua_Stem_Ru {
  const VERSION = "0.01";

  const VOWEL = '/аеиоуыэюя/';
  const PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/';
  const REFLEXIVE = '/(с[яь])$/';
  const ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/';
  const PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/';
  const VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
  const NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/';
  const RVRE = '/^(.*?[аеиоуыэюя])(.*)$/';
  const DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/';

  private $Stem_Caching = 0;
  private $Stem_Cache = array();

  /**
   *
   */
  private function stem_caching($parm_ref) {
    $caching_level = @$parm_ref['-level'];
    if ($caching_level) {
      if (!$this->m($caching_level, '/^[012]$/')) {
        die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
      }
      $this->Stem_Caching = $caching_level;
    }
    return $this->Stem_Caching;
  }

  /**
   *
   */
  private function clear_stem_cache() {
    $this->Stem_Cache = array();
  }

  /**
   *
   */
  private function s(&$s, $re, $to) {
    $orig = $s;
    $s = preg_replace($re, $to, $s);
    return $orig !== $s;
  }

  /**
   *
   */
  private function m($s, $re) {
    return preg_match($re, $s);
  }

  /**
   *
   */
  public function stem_word($word) {
    $word = mb_strtolower($word);
    $word = str_replace('ё', 'е', $word);

    // Check against cache of stemmed words
    if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
      return $this->Stem_Cache[$word];
    }

    $stem = $word;
    do {
      if (!preg_match(self::RVRE, $word, $p)) break;
      $start = $p[1];
      $RV = $p[2];
      if (!$RV) break;

      // Step 1
      if (!$this->s($RV, self::PERFECTIVEGROUND, '')) {
        $this->s($RV, self::REFLEXIVE, '');
        if ($this->s($RV, self::ADJECTIVE, '')) {
          $this->s($RV, self::PARTICIPLE, '');
        }
        else {
          if (!$this->s($RV, self::VERB, '')) {
            $this->s($RV, self::NOUN, '');
          }
        }
      }

      // Step 2
      $this->s($RV, '/и$/', '');

      // Step 3
      if ($this->m($RV, self::DERIVATIONAL)) {
        $this->s($RV, '/ость?$/', '');
      }

      // Step 4
      if (!$this->s($RV, '/ь$/', '')) {
        $this->s($RV, '/ейше?/', '');
        $this->s($RV, '/нн$/', 'н');
      }

      $stem = $start.$RV;
    } while(false);

    if ($this->Stem_Caching) {
      $this->Stem_Cache[$word] = $stem;
    }

    return $stem;
  }
}
