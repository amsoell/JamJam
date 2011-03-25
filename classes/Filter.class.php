<?php
//------------------------------------------------------------------
/// \file   Filter.class.php
/// \brief  Track filter object
/// \author A. M. Soell
/// \date   5/10/2008
/// \version  3.0
///
/// This object contains and processes rules about which tracks are
/// allowed to be queued
///
// $Id:$
//-------------------------------------------------------------------


//
///	\brief	Filter class
///
/// Manages all aspects of the request filters
//
  class Filter extends Common {
    private $tracks;
    public $inclusive;
    private $targetfield;
    public $maximum;

//
/// \brief  Constructor method
///
/// Initialize the filter object
///
//      
    public function __construct($id) {
      $this->tracks = Array();
      $this->load($id);
    }
    
    public function getFilters($applicationmask) {
      global $_DB;
      $response = Array();
      
      $rs = $_DB->query("SELECT id FROM #__filter WHERE applicationmask&".$applicationmask);
      while ($rec = $rs->fetch_array()) {
        $response[] = new Filter($rec['id']);
      }
      
      return $response;
    }
    
    public function filter($cmp_track) {
      if ($cmp_track instanceof Track) {
        foreach ($this->tracks as $track) {
          if ($cmp_track->id == $track['id']) {
            return (($track['count']>=$this->maximum) == $this->inclusive);
          }
        }
        return !$this->inclusive;
      } else {
        return !$this->inclusive;
      }
    }
    
    private function load($id) {
      global $_DB;
      
      $rsFilter = $_DB->query("SELECT #__filter.inclusive, #__filter.targetfield, #__filter.maximum, #__subquery.query FROM #__filter INNER JOIN #__subquery ON #__filter.subquery=#__subquery.id WHERE #__filter.id='$id'");
      
      if ($recFilter = $rsFilter->fetch_array()) {
        $this->inclusive = ($recFilter['inclusive']==1?true:false);
        $this->targetfield = $recFilter['targetfield'];
        $this->maximum = $recFilter['maximum'];
        
        $query = $recFilter['query'];
        
        $rsParams = $_DB->query("SELECT #__filter_param.param, #__filter_param.value FROM #__filter_param WHERE #__filter_param.filter='$id'");
        while ($recParam = $rsParams->fetch_array()) {
          $query = str_replace('@'.$recParam['param'], $recParam['value'], $query);
        }
        
        $rs = $_DB->query($query);
        while ($rec = $rs->fetch_array()) {
          $this->tracks[] = $rec;
        }
      }
    }
  }
?>
