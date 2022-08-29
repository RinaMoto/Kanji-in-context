<?php
    if (isset($_POST['getExamples'])) {  
        header("Access-Control-Allow-Origin: http://[cross.domain]");
        header("Access-Control-Allow-Headers: access");
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
        header("Access-Control-Allow-Credentials: true");

        class kanjiObj {
            public $kanji;
            public $kanjiHiragana;
            public $kanjiMeaning;
            public $exSentence;
            public $jpSentTranslation;
            public $enSentTranslation;
        }

        function getHiragana($kanji) {
            $apiID = "xxx";
            $url = "https://labs.goo.ne.jp/api/hiragana";
            $data = array("app_id"=> $apiID, "sentence" => (string)$kanji, "output_type" => "hiragana");
            $data = http_build_query($data);

            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded",
                    'content' => $data
                )
            );
            $context = stream_context_create($options);
            $result = @file_get_contents($url, false, $context);
            if ($result === FALSE) {
                return 0;
            }
            $result = json_decode($result);
            return $result->converted;
        }
        
        $xml = simplexml_load_file("kanji_xml/JMdict_e_examp") or die("Error: cannot create object");
        if ($xml === FALSE) {
            echo "error";
        }
        else {
            // function that returns a random kanji + sentence + translation from xml file
            while (1) {
                $i = rand(0, count($xml->entry));
                if ($xml->entry[$i]->sense->example) {
                    $send = 0;
                    $kanjiObject = new kanjiObj();
                    $pattern = "'/^[^ぁ-んァ-ン]+/i'";

                    $kanjiObject->kanji = (string)$xml->entry[$i]->sense->example->ex_text;
                    if (((bool)preg_match('/^[^ぁ-んァ-ン]+/i', (string)$xml->entry[$i]->sense->example->ex_text)) === FALSE) {
                        $send = 1;
                    }
                    // get hiragana for kanji
                    $kanjiHiragana = getHiragana($kanjiObject->kanji);
                    if ($kanjiHiragana === 0) {
                        $send = 1;
                    }
                    else {
                      $kanjiObject->kanjiHiragana = (string)$kanjiHiragana; 
                    } 
                    
                    $meanings = array();
                    foreach ($xml->entry[$i]->sense->gloss as $meaning) {
                        $meanings[] = (string)$meaning;
                    };
                    $kanjiObject->kanjiMeaning = $meanings; 

                    // iterate through every example sentence
                    $sentences = array();
                    $jpTranslatedSentences = array();
                    $enTranslatedSentences = array();
                    for ($j = 0; $j < count($xml->entry[$i]->sense->example); $j++) {
                        if ($xml->entry[$i]->sense->example[$j]) {
                            $jpSentence = $xml->entry[$i]->sense->example[$j]->ex_sent[0];
                            $sentences[$j] = (string)$jpSentence; 
                            $jpTranslatedSentence = getHiragana($jpSentence);
                            if ($jpTranslatedSentence === 0) {
                                $send = 1;
                            }
                            else {
                               $jpTranslatedSentences[$j] = (string)$jpTranslatedSentence; 
                            } 
                            $enTranslatedSentences[$j] = (string)$xml->entry[$i]->sense->example[$j]->ex_sent[1]; 
                        }
                    }
                    $kanjiObject->exSentence = $sentences;
                    $kanjiObject->jpSentTranslation = $jpTranslatedSentences;
                    $kanjiObject->enSentTranslation = $enTranslatedSentences;
                    if ($send === 0) {
                        echo json_encode($kanjiObject);
                        break;
                    }
                    else {
                        continue;
                    }
                }
                else {
                    continue;
                }
            }
        }    
    }
    else if (isset($_POST['keyword'])) {
        include('./simple_html_dom.php');
        $data = $_POST['keyword'];
        $html = file_get_html("https://www.nhk.jp/search/?q=".urlencode($data));
        foreach ($html->find('div.idb-result') as $element) {
            $heading = $element->find('div.search-result-crawler');
            print_r($element);
        }
    }
?>