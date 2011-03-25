<?php 
  $f = get_defined_constants(true);
  $f = $f['user'];
  echo json_encode($f);
?>