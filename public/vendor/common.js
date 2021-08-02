count = 0;
wordsArray = ["Storage", "Entertainment", "Creative", "Venue"];
setInterval(function () {
count++;
$("#word").fadeOut(400, function () {
    $(this).text(wordsArray[count % wordsArray.length]).fadeIn(400);
});
}, 5000);
