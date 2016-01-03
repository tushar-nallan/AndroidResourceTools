<?php
  
  function uuid()
  {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                   // 32 bits for "time_low"
                   mt_rand(0,0xffff),mt_rand(0,0xffff),
                   // 16 bits for "time_mid"
                   mt_rand(0,0xffff),
                   // 16 bits for "time_hi_and_version",
                   
                   // four most significant bits holds version number 4
                   mt_rand(0,0x0fff)|0x4000,
                   // 16 bits, 8 bits for "clk_seq_hi_res",
                   
                   // 8 bits for "clk_seq_low",
                   
                   // two most significant bits holds zero and one for variant DCE1.1
                   mt_rand(0,0x3fff)|0x8000,
                   // 48 bits for "node"
                   mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff));
  }
  
  function generate_drawable($gifFilePath,$gifUuid,$drawableDensity,$drawableName)
  {
    if($drawableName=="")$drawableName="gifdrawable";
    
    $output=shell_exec("mkdir -p outputs/$gifUuid");
    if($output!=="")error_log("1. ".$output);
    $output=shell_exec("mv $gifFilePath outputs/$gifUuid/$drawableName.gif");
    if($output!=="")error_log("2. ".$output);
    $output=shell_exec("./gif2animdraw -i outputs/$gifUuid/$drawableName.gif -o outputs/$gifUuid -d $drawableDensity --oneshot");
    if($output!=="")error_log("3. ".$output);
    $output=shell_exec("cd outputs/$gifUuid ; zip -r $drawableName.zip drawable drawable-$drawableDensity ; cd ../..");
    if($output!=="")error_log("4. ".$output);
    header('Location: outputs/'.$gifUuid.'/'.$drawableName.'.zip');
  }
  
  $target_dir    ="uploads/";
  $targetuuid    =uuid();
  $target_file   =$target_dir.$targetuuid.".gif";
  $errorString   ="";
  $fileNameFromForm="fileToUpload";
  
  $drawableDensity=$_POST["_frm-iconform-sourceDensity"];
  $drawableName=$_POST["drawableName"];
  
  $GO_HOME=". Click here to go back to <a href='http://tusharonweb.in/AndroidTools/GifToAnimationDrawable'>Home</a>";
  // Check if a file was uploaded
  if(!isset($_FILES["$fileNameFromForm"])){
    echo"No file uploaded".$GO_HOME;
    return;
  }
  
  // Check if it was an image
  $imageFileType=pathinfo($target_file,PATHINFO_EXTENSION);
  $check=getimagesize($_FILES["$fileNameFromForm"]["tmp_name"]);
  if($check==false){
    echo"File is not an image".$GO_HOME;
    return;
  }
  
  // Allow certain file formats
  if($imageFileType!="gif"){
    echo"Sorry, only GIF files are allowed".$GO_HOME;
    return;
  }
  
  if(move_uploaded_file($_FILES["$fileNameFromForm"]["tmp_name"],$target_file)){
    generate_drawable($target_file,$targetuuid,$drawableDensity,$drawableName);
  }else{
    echo"An error occurred".$GO_HOME;
  }
  ?>
