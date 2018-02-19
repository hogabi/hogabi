
<?
$oDB = &DB::getInstance();
$query  = "SELECT max(regdate) as last_update_date from xe_documents where module_srl in (108, 220, 251, 253, 280, 1371, 2525, 4722, 4801)";
$result = $oDB->_query($query);
$output_comment = $oDB->_fetch($result);
$LAST_UPDATE_DATE = $output_comment->last_update_date;




echo "UPDATE : ".date('Y-m-d H:i:s', strtotime($LAST_UPDATE_DATE));;
?>
