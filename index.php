<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Welcome to Kanji in context</h1>
    <?php 
        $xml = simplexml_load_file("kanji_xml/JMdict_e_examp") or die("Error: cannot create object");
        if($xml === FALSE) {
            echo "error";
        }
        else {
            while (1) {
                $i = rand(0, count($xml->entry));
                if ($xml->entry[$i]->sense->example)  {
                    for ($j = 0; $j < count($xml->entry[$i]->sense); ++$j) {
                        if ($xml->entry[$i]->sense[$j]->example) {
                            echo "Highlight: ".$xml->entry[$i]->sense[$j]->example->ex_text."<br/>";
                            echo "Kanji: ".$xml->entry[$i]->sense[$j]->example->ex_sent."<br/>"; 
                            $translate[$j] = "Translate: ".$xml->entry[$i]->sense[$j]->example->ex_sent[1]."<br/>"; 
                        }
                    }
                    break;
                }
                else {
                    continue;
                }
            }
        }
    ?>
</body>
</html>