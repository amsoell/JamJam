<?php
  class Common {
    private $errorcode;
    private $errordetail;
    
    public function setErrorCode($err) {
      $this->errorcode = $err;
    }
    
    public function setErrorDetail($detail) {
      $this->errordetail = $detail;
    }
  }
?>