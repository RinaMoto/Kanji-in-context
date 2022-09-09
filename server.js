$(document).ready(function() {
    getKanji();
    $('#nextBtn').click(function() {
        $('#translateBtn').val("English Translation");
        $('#hiraganaBtn').val("Hiragana");
        $('#generateArticlesBtn').val("Show example articles");
        getKanji();
    });
    $('#translateBtn').click(function() {
        $("#examples p[id*='enTranslation']").fadeToggle();
        $("#kanjiWord #engTranslation").fadeToggle();
        var btnvalue = $(this).val();
        $(this).val(btnvalue === "English Translation" ? "Hide English Translation" : "English Translation");
    });
    $('#hiraganaBtn').click(function() {
        $("#examples p[id*='jpTranslation']").fadeToggle();
        $("#kanjiWord #kanjiTranslation").fadeToggle();
        var btnvalue = $(this).val();
        $(this).val(btnvalue === "Hiragana" ? "Hide Hiragana Translation" : "Hiragana");
    });
    $('#generateArticlesBtn').click(function() {
        if ($('#articles h1').val() !== '') {
            getArticles();
        }
        $('#articles').fadeToggle();
        var btnvalue = $(this).val();
        $(this).val(btnvalue === "Show example articles" ? "Hide articles" : "Show example articles");
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
        console.log(articlesObject);
        if (articlesObject === 0) {
            $('#articles').append('<h2>Could not generate articles</h2>');
            $('#articles').fadeIn();
        }
        else {
            $('#articles').append('<h1>Articles</h1>');
            for (i = 0; i < articlesObject.length; i++) {
                let title = $(`<h2 id="articlesTitle${[i]}"></h2>`).text("Title: " + articlesObject[i]['title']);
                let description = $(`<p id="articlesDescription${[i]}"></p>`).text("Description: " + articlesObject[i]['description']);
                $('#articles').append(title, description);
                $('<a>',{
                    text: 'link',
                    title: articlesObject[i]['url'],
                    href: articlesObject[i]['url'],
                    target: '_blank',
                    click: function(){ window.open(articlesObject[i]['url']); return false;}
                }).appendTo('#articles');
                $('#articles').fadeIn();
            }
        }
    })
}

function getKanji() {
    $('#nextBtn').prop('disabled', true);
    $('#translateBtn').prop('disabled', true);
    $('#hiraganaBtn').prop('disabled', true);
    $('#generateArticlesBtn').prop('disabled', true);
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {"getExamples": "1"},
    }).done(function(msg) {
        kanjiObject = JSON.parse(msg);
        $("#kanjiWord").empty();
        $("#kanjiSentence").empty();
        $('#examples').empty();
        $('#articles').empty();
        let kanji = $(`<h2 id="kanji"></h2>`).text(kanjiObject['kanji']);
        let hiragana = $(`<p id="hiragana"></p>`).text(kanjiObject['kanjiHiragana']);

        if (kanji !== null) {
            $('#kanjiWord').append(kanji).hide();
            $('#kanjiWord').fadeIn();
        }

        $('#kanjiWord').append("<div id='kanjiTranslation'></div");
        $("#kanjiWord #kanjiTranslation").append(hiragana);
        $("#kanjiWord #kanjiTranslation").hide();

        $('#kanjiWord').append("<div id='engTranslation'></div");
        for (let j = 0; j < kanjiObject['kanjiMeaning'].length; j++) {
            let meanings = $(`<p id="meaning${[j]}"></p>`).text(kanjiObject['kanjiMeaning'][j]);
            $("#kanjiWord #engTranslation").append(meanings);
        }
        $("#kanjiWord #engTranslation").hide();

        for (let i = 0; i < kanjiObject['exSentence'].length; i++) {          
            let sentence = `<h2 id="sentence${[i]}">` + kanjiObject['exSentence'][i] + '</h2>';
            let jpTranslation = `<p id="jpTranslation${[i]}">` + kanjiObject['jpSentTranslation'][i] + '</p>';
            let enTranslation = `<p id="enTranslation${[i]}">` + kanjiObject['enSentTranslation'][i] + '</p>';
            let kanjiExampleUsage = `<div id="example${i}">` + sentence + jpTranslation + enTranslation + '</div>'; 
            
            $('#examples').append(kanjiExampleUsage).hide();
            $('#examples').fadeIn("slow");
            $(`#examples #jpTranslation${[i]}`).hide();
            $(`#examples #enTranslation${[i]}`).hide();
        }
        $('#nextBtn').prop('disabled', false);
        $('#translateBtn').prop('disabled', false);
        $('#hiraganaBtn').prop('disabled', false);
        $('#generateArticlesBtn').prop('disabled', false);
    }); 
}