$(document).ready(function() {
    getKanji();
    $('#nextBtn').click(function() {
        $('#translateBtn').val("English Translation");
        getKanji();
    });
    $('#translateBtn').click(function() {
        $('#examples p').toggle();
        $("#kanjiWord #kanjiTranslation").toggle();
        var btnvalue = $(this).val();
        $(this).val(btnvalue === "English Translation" ? "hide" : "English Translation");
    });
    $('#generateArticlesBtn').click(function() {
        getArticles();
    })
})

function getArticles() {
    let keyword = $('#kanjiWord #kanji').text();
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {"keyword": keyword},
    }).done(function(msg) {
        articlesObject = JSON.parse(msg);
        $('#articles').append('<h1>Articles</h1>');
        for (i = 0; i < articlesObject.length; i++) {
            let title = $(`<h2 id="articlesTitle${[i]}"></h2>`).text(articlesObject[i]['title']);
            let description = $(`<p id="articlesDescription${[i]}"></p>`).text(articlesObject[i]['description']);
          //  let url = $(`<a id="articlesUrl${[i]}"></a>`).text(articlesObject[i]['url']);
            $('#articles').append(title, description);
            $('<a>',{
                text: 'link',
                title: articlesObject[i]['url'],
                href: articlesObject[i]['url'],
                click: function(){ clickFunc( window.open(articlesObject[i]['url']) ); return false;}
            }).appendTo('#articles');
        }
    })
}

function getKanji() {
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {"getExamples": "1"},
    }).done(function(msg) {
        kanjiObject = JSON.parse(msg);
        console.log(kanjiObject);
        $("#kanjiWord").empty();
        $("#kanjiSentence").empty();
        $('#examples').empty();
        let kanji = $(`<h2 id="kanji"></h2>`).text(kanjiObject['kanji']);
        let hiragana = $(`<p id="hiragana"></p>`).text(kanjiObject['kanjiHiragana']);

        if (kanji !== null) {
            $('#kanjiWord').append(kanji);
        }

        $('#kanjiWord').append("<div id='kanjiTranslation'></div");
        $("#kanjiWord #kanjiTranslation").append(hiragana);

        for (let j = 0; j < kanjiObject['kanjiMeaning'].length; j++) {
            let meanings = $(`<p id="meaning${[j]}"></p>`).text(kanjiObject['kanjiMeaning'][j]);
            $("#kanjiWord #kanjiTranslation").append(meanings);
        }
        $("#kanjiWord #kanjiTranslation").hide();

        for (let i = 0; i < kanjiObject['exSentence'].length; i++) {
            
            let sentence = `<h2 id="sentence${[i]}">` + kanjiObject['exSentence'][i] + '</h2>';
            let jpTranslation = `<p id="jpTranslation${[i]}">` + kanjiObject['jpSentTranslation'][i] + '</p>';
            let enTranslation = `<p id="enTranslation${[i]}">` + kanjiObject['enSentTranslation'][i] + '</p>';
            let kanjiExampleUsage = `<div id="example${i}">` + sentence + jpTranslation + enTranslation + '</div>'; 
            
            $('#examples').append(kanjiExampleUsage);
            $(`#examples #jpTranslation${[i]}`).hide();
            $(`#examples #enTranslation${[i]}`).hide();
           
        }
    }); 
}