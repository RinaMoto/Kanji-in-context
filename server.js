$(document).ready(function() {
    getKanji();
    $('#nextBtn').click(function() {
        $('#translateBtn').val("translate");
        getKanji();
    });
    $('#translateBtn').click(function() {
        $('#examples p').toggle();
        $("#kanjiWord #kanjiTranslation").toggle();
        var btnvalue = $(this).val();
        $(this).val(btnvalue === "translate" ? "hide" : "translate");
    })
})

function getKanji() {
    $.ajax({
        type: "POST",
        url: "server.php"
    }).done(function(msg) {
        kanjiObject = JSON.parse(msg);
        console.log(kanjiObject);
        $("#kanjiWord").empty();
        $("#kanjiSentence").empty();
        $('#examples').empty();
        for (let i = 0; i < kanjiObject.length; i++) {
            let kanji = $(`<h2 id="kanji${[i]}"></h2>`).text(kanjiObject[i]['kanji']);
            let sentence = `<h2 id="sentence${[i]}">` + kanjiObject[i]['sentence'] + '</h2>';
            let translation = `<p id="translation${[i]}">` + kanjiObject[i]['translation'] + '</p>';
            let kanjiExampleUsage = `<div id="example${i}">` + sentence + translation + '</div>'; 
            if (kanji !== null) {
                $('#kanjiWord').append(kanji);
            }
            $('#examples').append(kanjiExampleUsage);
            console.log(kanjiExampleUsage);
            $(`#examples #translation${[i]}`).hide();
           
            $('#kanjiWord').append("<div id='kanjiTranslation'></div");
            for (let j = 0; j < kanjiObject[i]['kanjiMeaning'].length; j++) {
                let meanings = $(`<p id="meaning${[j]}"></p>`).text(kanjiObject[i]['kanjiMeaning'][j]);
                $("#kanjiWord #kanjiTranslation").append(meanings);
            }
            $("#kanjiWord #kanjiTranslation").hide();
        }
    }); 
}