<?php
    header("Access-Control-Allow-Origin: http://[cross.domain]");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
    header("Access-Control-Allow-Credentials: true");

    class kanjiObj {
        public $kanji;
        public $kanjiMeaning;
        public $sentence;
        public $translation;
    }
    
    $xml = simplexml_load_file("kanji_xml/JMdict_e_examp") or die("Error: cannot create object");
    if($xml === FALSE) {
        echo "error";
    }
    else {
        // function that returns a random kanji + sentence + translation from xml file
        while (1) {
            $i = rand(0, count($xml->entry));
            if ($xml->entry[$i]->sense->example)  {
                $kanjiExamples = array();
                for ($j = 0; $j < count($xml->entry[$i]->sense); ++$j) {
                    if ($xml->entry[$i]->sense[$j]->example) {
                        $kanjiObject = new kanjiObj();
                        if ($j === 0) {
                            $kanjiObject->kanji = (string)$xml->entry[$i]->sense[$j]->example->ex_text;
                        }
                        $meanings = array();
                        foreach ($xml->entry[$i]->sense[$j]->gloss as $meaning) {
                            $meanings[] = (string)$meaning;
                        };
                        $kanjiObject->kanjiMeaning = $meanings;
                        $kanjiObject->sentence = (string)$xml->entry[$i]->sense[$j]->example->ex_sent[0]; 
                        $kanjiObject->translation = (string)$xml->entry[$i]->sense[$j]->example->ex_sent[1]; 
                        $kanjiExamples[$j] = $kanjiObject;
                    }
                }
                echo json_encode($kanjiExamples);
                break;
            }
            else {
                continue;
            }
        }
    }    
?>