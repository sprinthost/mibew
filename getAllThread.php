<?php
header("Content-Type: text/json; charset=utf-8");

switch ($_SERVER['HTTP_ORIGIN']) {
    case 'http://feedback.from.sh':
    header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, tn');

include "libs/common.php";
$link = connect();

$email = (preg_match("^/([a-z0-9_-\.]+)@([a-z0-9_-]+)\.([a-z]{2,6})$/i", $_POST['email'])) ? $_POST['email'] : 0;
$start = (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $_POST['start'])) ? $_POST['start'] : 0;
$end = (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['end'])) ? $_POST['end'] : 0;

$sql = "SELECT operatorid FROM chatoperator WHERE vcemail='".$email."' LIMIT 1";
$operatorid = select_one_row($sql, $link);

$sql = "SELECT count(threadid) AS count FROM chatthread WHERE agentid='".$operatorid['operatorid']."' AND (dtmcreated BETWEEN '".$start." 00:00:00' AND '".$end." 23:59:59') ";
$count_mibew = select_one_row($sql, $link);

echo $count_mibew['count'];

mysql_close($link);
    break;
}
?>