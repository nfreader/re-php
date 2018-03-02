<?php 

ini_set('xdebug.var_display_max_depth',-1);
ini_set('xdebug.var_display_max_data',-1);
ini_set('xdebug.var_display_max_children',-1);

$words = [];
$handle = fopen("words",'r');
if($handle){
  while (($line = fgets($handle)) !== false) {
    if(7 < strlen($line)){
      $words[] = trim(rtrim($line));
    }
  }
} else {
  throw new Exception("Unable to locate a words file");
}
fclose($handle);

function make_seed() {
  return abs(random_int(PHP_INT_MIN, PHP_INT_MAX));
}

function getDateFromDay($dayOfYear, $year) {
  $date = DateTime::createFromFormat('z Y', strval($dayOfYear) . ' ' . strval($year));
  return $date;
}

if(!$seed = filter_input(INPUT_POST, 'seed', FILTER_VALIDATE_INT)) {
  $seed = make_seed();
}
$seed = trim(rtrim($seed));
mt_srand($seed);

$order = array_map(create_function('$val', 'return mt_rand();'), range(1, count($words)));
array_multisort($order, $words);

$wordlist = [];
for($i = 0; $i <= 364; $i++){
  $wordlist[$i]['challenge'] = $words[$i];
  $wordlist[$i]['positive'] = $words[$i+368];
  $wordlist[$i]['negative'] = $words[$i+733];
}
unset($words);
$today = date('z');
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    .savedSeed {
      position:absolute;
      left:-9999px
    }
    </style>
    <title>Ruby-Everest</title>
  </head>
  <body>
    <div class="container">
      <h1><samp>Ruby-Everest</samp></h1>
      
      <p class="lead">Remember in <a href="https://www.youtube.com/watch?v=fx77j1vl4d8" target="_blank" rel="noopener noreferrer">The Bourne Ultimatum</a> when Noah Vosen asks Nicky Parsons to verify who she is and that she's not under duress? This tool generates a wordlist that can be used for that.</p>

      <p>Review the wordlist below. If you're satisfied with the words, hit Copy Seed to save the seed. Securely transmit that seed to your buddy. When they input it into the seed field below, they should get the same word list. If you want a new wordlist, hit New Wordlist.</p>

      <p>Great for:</p>
      <ul>
        <li>Verifying that someone is who they say they are over nonsecure channels!</li>
        <li>Looking like a total dork in front of your friends!</li>
        <li>And total strangers!</li>
        <li>Probably getting caught if you're using this in the real-world (please don't)!</li>
        <li><a href="https://www.youtube.com/watch?v=K6Ato2GhU-o" target="_blank" rel="noopener noreferrer"> Mix your words</a> <a href="https://www.youtube.com/watch?v=U5T9cFV-ExI" target="_blank" rel="noopener noreferrer">into everyday conversation!</a></li>
      </ul>

      <p><small>Wordlist from <a target="_blank" rel="noopener noreferrer" href="https://github.com/atebits/words">atebits/words</a>. <a target="_blank" rel="noopener noreferrer" href="https://github.com/nfreader/re-php">Ruby-Everest</a> source on GitHub.</small></p>

      <div class="alert alert-danger">Please don't actually use this for anything truly important.</div>

      <hr>

      <div class="row">
        <div class="col-sm-8">
      <form method="POST">
        <div class="form-group row">
          <label for="seed" class="col-sm-1 col-form-label">Seed</label>
          <div class="col-sm-9">
            <input type="password" class="form-control" id="seed" name='seed' placeholder="Seed" value="<?php echo $seed;?>">
          </div>
          <div class="col-sm-2">
            <button type="submit" class="btn btn-primary mb-2">Generate Wordlist</button>
          </div>
        </div>
      </form>
      </div>
      <div class="col-sm-2">
        <input class="savedSeed" value="<?php echo $seed;?>" />
        <button class="btn-success btn float-right copySeed">Copy Seed</button>
      </div>
      <div class="col-sm-2">
        <a class="btn-danger btn float-right" href="">New Wordlist</a>
      </div>
      </div>

      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>DAY</th>
            <th>CHALLENGE</th>
            <th>DURESS RESPONSE</th>
            <th>NORMAL RESPONSE</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($wordlist as $day => $words):?>
            <?php if($today == $day):?>
              <tr class="table-warning">
            <?php else:?>
              <tr>
            <?php endif;?>
            <?php $day = getDateFromDay($day, date('Y'))->format('F j');?>
              <td><?php echo $day;?></td>
              <td><samp><?php echo strtoupper($words['challenge']);?></samp></td>
              <td class="table-danger"><samp><?php echo strtoupper($words['negative']);?></samp></td>
              <td class="table-success"><samp><?php echo strtoupper($words['positive']);?></samp></td>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script>
      function copy() {
        var copyText = document.querySelector(".savedSeed");
        console.log(copyText);
        copyText.select();
        document.execCommand("copy");
      }

      document.querySelector(".copySeed").addEventListener("click", copy);
    </script>
  </body>
</html>
