// function([string1, string2],target)    
consoleText(["Réinitialisation du mot de passe"], 'text');

function consoleText(words, id) {
  var visible = true;
  var con = document.getElementById('console');
  var letterCount = 1;
  var x = 1;
  var waiting = false;
  var target = document.getElementById(id)
  window.setInterval(function() {

    if (letterCount === 0 && waiting === false) {
      waiting = true;
      target.innerHTML = words[0].substring(0, letterCount)
      window.setTimeout(function() {
        var usedWord = words.shift();
        words.push(usedWord);
        x = 1;
        letterCount += x;
        waiting = false;
      }, 5500)
    } else if (waiting === false) {
      target.innerHTML = words[0].substring(0, letterCount)
      letterCount += x;
    }
  }, 60)
}