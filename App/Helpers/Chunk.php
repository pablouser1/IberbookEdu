<?php
namespace App\Helpers;
/**
 * Chunked upload handler
 */
class Chunk {
  private $filename;
  private $num;
  private $num_chunks;
  private $temp_file;

  public function uploadChunk($tempdir, $element) {
    $tmp_name = $element['tmp_name'];
    $this->filename = $element['name'];
    $this->temp_file = $tempdir."/".$this->filename; // Temp file dir
    $this->num = (int)$_POST['num'];
    $this->num_chunks = (int)$_POST['num_chunks'];
    if (move_uploaded_file($tmp_name, $this->temp_file.$this->num)) {
      return $this->filename;
    }
    else {
      return false;
    }
  }

  public function hasAllChunks() {
    // count ammount of uploaded chunks
    $chunksUploaded = 0;
    for ( $i = 1; $i <= $this->num; $i++ ) {
      if ( file_exists( $this->temp_file.$i ) ) {
        $chunksUploaded++;
      }
    }
    if ($chunksUploaded === $this->num_chunks) {
      return true;
    } else {
      return false;
    }
  }

  public function merge($dir) {
    for ($i = 1; $i <= $this->num_chunks; $i++) {
      $file = fopen($this->temp_file.$i, 'rb');
      $buff = fread($file, 10485760);
      fclose($file);
      $final = fopen($this->temp_file, 'ab');
      fwrite($final, $buff);
      fclose($final);
      unlink($this->temp_file.$i);
    }
    rename($this->temp_file, $dir."/".$this->filename);
  }
}
