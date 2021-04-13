<?php
namespace App\Helpers;
/**
 * Chunked upload handler
 */
class Chunk {
  private $num;
  private $num_chunks;
  private $target_file;

  public function uploadChunk($dir, $element) {
    $tmp_name = $element['tmp_name'];
    $filename = $element['name'];
    $this->target_file = $dir."/".$filename;
    $this->num = (int)$_POST['num'];
    $this->num_chunks = (int)$_POST['num_chunks'];
    if (move_uploaded_file($tmp_name, $this->target_file.$this->num)) {
      return $filename;
    }
    else {
      return false;
    }
  }

  public function hasAllChunks() {
    // count ammount of uploaded chunks
    $chunksUploaded = 0;
    for ( $i = 1; $i <= $this->num; $i++ ) {
      if ( file_exists( $this->target_file.$i ) ) {
        $chunksUploaded++;
      }
    }
    if ($chunksUploaded === $this->num_chunks) {
      return true;
    }
    else {
      return false;
    }
  }

  public function merge() {
    for ($i = 1; $i <= $this->num_chunks; $i++) {
      $file = fopen($this->target_file.$i, 'rb');
      $buff = fread($file, 10485760);
      fclose($file);
      $final = fopen($this->target_file, 'ab');
      fwrite($final, $buff);
      fclose($final);
      unlink($this->target_file.$i);
    }
  }
}
