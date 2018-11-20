<?php

function month_definer($bulan){
  switch($bulan){
    case 1: return "Januari"; break;
    case 2: return "Februari"; break;
    case 3: return "Maret"; break;
    case 4: return "April"; break;
    case 5: return "Mei"; break;
    case 6: return "Juni"; break;
    case 7: return "Juli"; break;
    case 8: return "Agustus"; break;
    case 9: return "September"; break;
    case 10: return "Oktober"; break;
    case 11: return "November"; break;
    case 12: return "Desember"; break;
  }
}

function date_definer($tanggal){
  /**
   * $tanggal = "YYYY-MM"
   * $tanggal = "2018-07"
  */
  $tanggal = explode("-", $tanggal);
  $tanggal[1] = month_definer($tanggal[1]);
  $tanggal = array_reverse($tanggal);
  return implode(" ", $tanggal);
}
