<?php
// Tested on Ubuntu 20.04 and 16.04
$GLOBALS["work_directory"] = "path/to/clinker_server";
$GLOBALS["clinker_path"] = "path/to/clinker.exe";
$GLOBALS["max_time_in_server"] = 2 * 3600; // Max time of temp files before removing from server
$GLOBALS["sessions_directory"] = "clinker_server/clinker_user_sessions";

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function delTree($dir) {
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }

function deleteOldUserDirectories($time_limit = 2 * 3600) {
  $current_time = time();
  $directories = array_diff(scandir($GLOBALS["sessions_directory"]), array('..', '.'));

  if (!empty($directories)) {
    foreach ($directories as $dir) {
      $dir_path = $GLOBALS["sessions_directory"] . "/" . $dir;
      $dir_age = $current_time - filemtime($dir_path);
      if ($dir_age > $time_limit) {
        delTree($dir_path);
      }
    }
  }
}

// Check if the form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Create temp user directory
    $temp_user_id = "temp_user_" . generateRandomString($length = 10);
    $temp_user_dir = $GLOBALS["sessions_directory"] . "/" . $temp_user_id;
    $temp_user_upload = $temp_user_dir . "/upload";
    mkdir($temp_user_dir, $mode = 0777, true);
    chmod($temp_user_dir, 0777);
    mkdir($temp_user_upload, $mode = 0777, true);
    chmod($temp_user_upload, 0777);

    // Delete old user directories
    deleteOldUserDirectories($time_limit = $GLOBALS["max_time_in_server"]);

    // Count total files
    $countfiles = count($_FILES["GBK_files"]["name"]);

    // Looping all files
    for($i=0;$i<$countfiles;$i++){
       $filename = $_FILES["GBK_files"]["name"][$i];
       move_uploaded_file($_FILES["GBK_files"]["tmp_name"][$i], $temp_user_upload . "/" . $filename);
    }

    // Excecute clinker
    $work_dir = $GLOBALS["work_directory"];
    $path_to_clinker = $GLOBALS["clinker_path"];
    $clinker_data_path = $work_dir . "/clinker_user_sessions" . "/" . $temp_user_id;
    exec($path_to_clinker . " " . $clinker_data_path . "/upload/*.gbk -p" . " " . $clinker_data_path . "/plot.html");
  }
?>

<script type="text/javascript">
  localStorage.setItem("temp_user_dir", "<?php echo $temp_user_dir; ?>");
</script>

<html>
 <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="A server embedding to Clinker">
    <meta name="keywords" content="Clinker, Gene Clusters, Alignment, Bioinformatics">
    <meta name="author" content="Semidán Robaina Estévez">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Clinker Alignment Visualization</title>
    <!-- <link rel="icon" type="image/png" sizes="32x32" href="clinker_server/favicon.ico"> -->
    <link rel="stylesheet" href="clinker_server/main.css">
    <link rel="stylesheet" href="clinker_server/clinker-server.css">
 </head>

  <body>

    <div class="page-header">
      <div class="centering-container">
        <div class="title-container">
          <h1 class="title">Clinker</h1>
          <h3 class="subtitle">Run gene cluster comparisons</h3>
        </div>
      </div>
    </div>

    <div class="page-content">

      <div class="centering-container">
        <form action="" method="post" enctype="multipart/form-data">
          <label for="gbk_input"><b>Upload files (gbk):</b></label>
          <input class="buttons" type="file" id="gbk_input" name="GBK_files[]" accept=".gbk,.gb" multiple>
          <input class="buttons" type="submit" value="Run" name="submit">
          <button  class="buttons" id="help-button" type="button" name="button" onclick="showHelp();">About</button>
        </form>
      </div>

      <iframe id="clinker-web" title="Clinker Output Website"></iframe>

      <article id="help-text">
        <div id="hide-help-container">
          <button id="hide-help" type="button" name="button" onclick="showHelp();">Hide</button>
        </div>

        <h2>Using Clinker:</h2>
        <p>
          <a href="https://github.com/gamcil/clinker" target="_blank">Clinker</a> is a pipeline for easily generating publication-quality gene cluster comparison figures. This webpage allows running Clinker on a server so you don't have to install it locally on your machine.
        </p>
        <h3>1. Loading input:</h3>
        <p>
          Clinker's input are GeneBank (.gbk) files. Load the files from your local machine through the <i>Submit GBK files</i> input. Only GeneBank files will be shown, make sure they have the correct file type extension (.gbk or .gb).
        </p>
        <p>
          To refresh the figure with new input files just load new files through the <i>Submit GBK files</i> input.
        </p>
        <h3>2. Displaying gene labels:</h3>
        <p>
          To temporarily display info about a gene just hover over it with your mouse. Clinker's output figure is highly customizable. All the available options are displayed in the <i>Options</i> tab. To display gene labels, scroll down the options field and select the "Show gene labels" field. Loci tags are shown by default, to change the label type, click on the option "Label type" and select the label. For instance, to show the function annotation of the protein select "product".
        </p>
        <h3>3. Saving results:</h3>
        <p>
          The generated figure can be saved in svg format by clinking on the button "Save SVG".
        </p>
      </article>
    </div>

    <div class="centering-container">
      <footer>
        <p>Using <a href="https://github.com/gamcil/clinker">Clinker.</a></p>
        <p><a href="https://github.com/Robaina/clinker_server">Fork me here</a></p>
      </footer>
    </div>


    <script type="text/javascript" src="clinker_server/clinker-server.js"></script>
    <script>
      if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
      }
    </script>

  </body>

</html>
