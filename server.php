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
            $apiID = "43e25d41aa196cdf7023da13fbe57516a9dcc905c9d0e8bcb92a8108b0e79e51";
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
                    $kanjiClass = new kanjiObj();
                    $pattern = "'/^[^ぁ-んァ-ン]+/i'";

                    $kanjiClass->kanji = (string)$xml->entry[$i]->sense->example->ex_text;
                    if (((bool)preg_match('/^[^ぁ-んァ-ン]+/i', (string)$xml->entry[$i]->sense->example->ex_text)) === FALSE) {
                        $send = 1;
                    }
                    // get hiragana for kanji
                    $kanjiHiragana = getHiragana($kanjiClass->kanji);
                    if ($kanjiHiragana === 0) {
                        $send = 1;
                    }
                    else {
                      $kanjiClass->kanjiHiragana = (string)$kanjiHiragana; 
                    } 
                    
                    $meanings = array();
                    foreach ($xml->entry[$i]->sense->gloss as $meaning) {
                        $meanings[] = (string)$meaning;
                    };
                    $kanjiClass->kanjiMeaning = $meanings; 

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
                    $kanjiClass->exSentence = $sentences;
                    $kanjiClass->jpSentTranslation = $jpTranslatedSentences;
                    $kanjiClass->enSentTranslation = $enTranslatedSentences;
                    if ($send === 0) {
                        echo json_encode($kanjiClass);
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
        header("Access-Control-Allow-Origin: http://[cross.domain]");
        header("Access-Control-Allow-Headers: access");
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
        header("Access-Control-Allow-Credentials: true");

        class articles {
            public $title;
            public $url;
            public $description;
        }

        $apikey = '219ee9bfb35542ea81c4ecf07ea1de85';
        $data = array(
            "q" => (string)$_POST['keyword'],
            "apiKey" => (string)$apikey
        );
        // $allarticles = $newsapi->getEverything(urlencode((string)$data));
        // var_dump($allarticles);
        $params = http_build_query($data, '', '&');
        $apiUrl = 'https://newsapi.org/v2/everything?' . $params;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$apiUrl);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $articles = curl_exec($ch);
        curl_close($ch);
        $articlesObj = json_decode($articles)->articles;
        $articlesList = array();
        for ($i = 0; $i < 3; $i++) {
            $articlesClass = new articles();
            $articlesClass->title = $articlesObj[$i]->title;
            $articlesClass->url = $articlesObj[$i]->url;
            $articlesClass->description = $articlesObj[$i]->description;
            $articlesList[$i] = $articlesClass;
        }
        echo json_encode($articlesList);
    }
?>