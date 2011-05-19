<?php

// currently hardcoded

class PHPPorterStemmer {
  private $lang = 'en';

  function __construct() {
    $this->set_lang('ru');
  }

  function __destruct() {

  }

  public function stem_word($word) {
    if ($this->get_lang() == 'ru') {
      require_once(__DIR__ . '/Lingua_Stem_Ru.class.php');
      $stemmer = new Lingua_Stem_Ru();
      return  $stemmer->stem_word($word);
    }
    return FALSE;
  }

  public function set_lang($lang) {
    $this->lang = $lang;
  }

  public function get_lang() {
    return $this->lang;
  }

  function detect_lang($word) {
    return 'ru'; // temporary hardcoded
  }

}
