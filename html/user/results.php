<?php
    // show recent results for a host or user

    require_once("../inc/db.inc");
    require_once("../inc/util.inc");
    require_once("../inc/result.inc");

    $results_per_page = 20;

    db_init();
    $hostid = $_GET["hostid"];
    $userid = $_GET["userid"];
    $offset = $_GET["offset"];
    if (!$offset) $offset=0;

    $user = get_logged_in_user();

    if ($hostid) {
        $host = lookup_host($hostid);
        // if (!$host || $host->userid != $user->id) {
        //     echo "No access";
        //     exit();
        // }
        $type = "host";
        $clause = "hostid=$hostid";
    } else {
        if ($userid != $user->id) {
            echo "No access";
            exit();
        }
        $type = "user";
        $clause = "userid=$userid";
    }
    page_head("Results for $type");
    echo "<h3>Results for $type</h3>\n";
    result_table_start(true, false, true);
    $i = 0;
    $query = "select * from result where $clause order by id desc limit $offset,".($results_per_page+1);
    $result = mysql_query($query);
    $number_of_results = mysql_affected_rows();
    while ($res = mysql_fetch_object($result) and $i<$results_per_page) {
        show_result_row($res, true, false, true);
        $i++;
    }
    mysql_free_result($result);
    echo "</table>\n";

    if ($number_of_results > $results_per_page) {
        $offset = $offset+$results_per_page;
        echo "
            <br><center><a href=results.php?$clause&offset=$offset>Next $results_per_page results</a></center>
        ";
    }

    page_tail();
?>
