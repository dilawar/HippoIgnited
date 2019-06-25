<script>
$( function() {
    $( "#accordion" ).accordion({
        collapsible: true
        , heightStyle: "fill"
        });
    });
</script>

<?php
require_once BASEPATH.'autoload.php';
echo userHTML( );

$awses = getTableEntries( 'annual_work_seminars', 'date', '' );

// Create various maps from these AWSes.
$awsAsTCMMembers = [];
$awsAsSupervisor = [];
foreach($awses as $aws)
{
    for( $i=1; $i <= 4; $i++)
    {
        $tcmMember = __get__($aws, "tcm_member_$i","");
        if($tcmMember)
            $awsAsTCMMembers[$tcmMember][] = $aws;

    }

    for( $i=1; $i <= 2; $i++)
    {
        $superAWS = __get__($aws, "supervisor_$i","");
        if($superAWS)
            $awsAsSupervisor[$superAWS][] = $aws;
    }
}

ksort($awsAsSupervisor);

$tableA = "<table id='aws_supervisor_wise' class='sortable exportable info'>";
$tableA .= '<tr> 
    <th>Faculty Name</th> <th>Email</th> <th>All AWSes</th> 
    <th>#Active Student</th> <th>Active Student</th>
    <th>#Students</th><th>All students</th>
    </tr>
    ';
foreach( $awsAsSupervisor as $super => $awses )
{
    $row = '<tr>';
    $superName = arrayToName(findAnyoneWithEmail($super), true);
    $students = array_unique(array_map(function($x) { return $x['speaker']; }, $awses));
    ksort($students);
    $activeStudents = array_filter($students, function($x){ return isEligibleForAWS($x); });
    $row .= "<td> $superName </td>";
    $row .= "<td> $super </td>";
    $row .= "<td>" .count($awses) . " </td>";
    $row .= "<td>" . count($activeStudents) . " </td>";
    $row .= "<td>" .implode(", ", $activeStudents) . " </td>";
    $row .= "<td>" .count($students) . " </td>";
    $row .= "<td>" .implode(", ", $students) . " </td>";
    $row .= "</tr> ";
    $tableA .= $row;
}
$tableA .= "</table>";
ksort($awsAsSupervisor);

$tableB = "<table id='aws_tcm_wise' class='sortable exportable info'>";
$tableB .= '<tr> 
    <th>Faculty Name</th> <th>Email</th> 
    <th>#Active TCM</th> <th>Active TCMs</th>
    <th>All TCMs</th> 
    </tr>
    ';
foreach( $awsAsTCMMembers as $tcm => $awses )
{
    $row = '<tr>';
    $tcmName = arrayToName(findAnyoneWithEmail($tcm), true);
    $students = array_unique(array_map(function($x) { return $x['speaker']; }, $awses));
    ksort($students);
    $activeStudents = array_filter($students, function($x){ return isEligibleForAWS($x); });
    $row .= "<td> $tcmName </td>";
    $row .= "<td> $tcm </td>";
    $row .= "<td>" . count($activeStudents) . " </td>";
    $row .= "<td>" .implode(", ", $activeStudents) . " </td>";
    $row .= "<td>" .implode(", ", $students) . " </td>";
    $row .= "</tr> ";
    $tableB .= $row;
}
$tableB .= "</table>";

echo "<div id='accordion'>
    <h1>Faculty wise AWS list</h1>
    <div> $tableA </div>
    <h1>TCM wise AWS list</h1>
    <div> $tableB </div>
    </div>
    ";

echo goBackToPageLink("Go back");

?>

<script src="<?=base_url()?>./node_modules/xlsx/dist/xlsx.core.min.js"></script>
<script src="<?=base_url()?>./node_modules/file-saverjs/FileSaver.min.js"></script>
<script src="<?=base_url()?>./node_modules/tableexport/dist/js/tableexport.min.js"></script>

<script type="text/javascript" charset="utf-8">
TableExport(document.getElementsByClassName("exportable"));
</script>
