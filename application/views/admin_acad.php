<?php
require_once BASEPATH . 'autoload.php';
echo userHTML();

$ref = 'adminacad';
if (isset($controller)) {
    $ref = $controller;
}

$logins = getLoginIds();
$pendingRequests = getPendingAWSRequests();
$pendingScheduleRequest = getTableEntries('aws_scheduling_request', 'status', "status='PENDING'");
$symbDelete = ' <i class="fa fa-trash-o fa-1x"></i>';
$symbEdit = ' <i class="fa fa-pencil fa-1x"></i>';

?>

<!-- Script to autocomplete user -->
<script>
$(function() {
    var logins = <?php echo json_encode($logins); ?>;
    //console.log( logins );
    $( "#autocomplete_user" ).autocomplete( { source : logins });
    $( "#autocomplete_user1" ).autocomplete( { source : logins });
});
</script>

<?php

$symbUpdate = '<i class="fa fa-check"></i>';

echo '<table class="admin table">';
echo '<tr>';
echo '<td><a class="clickable" href="' . site_url('adminacad/upcoming_aws') . '">Manage Upcoming AWSes</a></td>';
echo '<td><a class="clickable" href="' . site_url('adminacad/scheduling_request') . '">
        Manage ' . count($pendingScheduleRequest) . ' Pending Scheduling Requests</a></td>';
echo '</tr></table>';
echo ' <br /> ';

echo '<table class="admin table">
    <tr>
        <td> Update AWS speaker<br />
            <form method="post" action="' . site_url("$ref/update_aws_speaker") . '">
                <input id="autocomplete_user" name="login" placeholder="student login" >
                <button class="btn btn-primary" 
                    title="Add or remove speakers from AWS list"
                    name="response" value="edit">Update</button>
            </form>
        </td>
        <td>
         <form method="post" action="">
            Enter a login and optionally AWS date and you can delete that AWS entry
            from my database.
            <input id="autocomplete_user1" name="login" placeholder="AWS Speaker" type="text" />
            <input class="datepicker" name="date" placeholder="date(optional)" value="" >
            <button class="btn btn-primary" name="response" value="Select">Find AWS</button>
            </form>
        </td>
    </tr>
    </table>';

echo '<br />';
echo '<table class="admin table">
    <tr>
        <td> <a class="clickable_small" href="' . site_url('adminacad/add_aws_entry') . '">Add Missing AWS entry</td>
        <td> <a class="clickable_small" href="' . site_url('adminacad/aws_db') . '">AWS Database</a> </td>
    </tr>
    <tr>
        <td> <a class="clickable_small"
            href="' . site_url('adminacad/aws_edit_requests') . '">Manage ' . count($pendingRequests) .
            ' pending requests</a>
        </td>
        <td> <a class="clickable_small" href="' . site_url('adminacad/email_and_docs') . '">Emails and Documents</td>
    </tr>
    <tr>
        <td> <a class="clickable_small"
            href="' . site_url('adminacad/faculty') . '">Manage Faculty and Course Instructors</a>
        </td>
        <td></td>
    </tr>
    </table>';

$login = null;
$date = null;
if (isset($_POST['response'])) {
    if ('Select' == $_POST['response']) {
        $login = $_POST['login'];
        $date = $_POST['date'];
    }

    $awss = [];
    if ($login and $date) {
        $awss = [getMyAwsOn($login, $date)];
    } elseif ($login) {
        $awss = getAwsOfSpeaker($login);
    }

    /* These AWS are upcoming */
    foreach ($awss as $aws) {
        if (!$aws) {
            continue;
        }

        $speaker = $aws['speaker'];
        $date = $aws['date'];
        echo printInfo("<a>Entry for $speaker (" . loginToText($speaker) . ') on ' .
            date('D M d, Y', strtotime($date)) . '</a>');

        echo arrayToVerticalTableHTML($aws, 'annual_work_seminars');
        echo '<br />';

        /* This forms remain on this page only */
        echo '<form method="post" action="' . site_url('adminacad/updateaws') . '">
            <input type="hidden" name="speaker" value="' . $speaker . '">
            <input type="hidden" name="date" value="' . $date . '" >
            <input type="hidden" name="id" value="' . $aws['id'] . '" >
                <button onclick="AreYouSure(this)" style=\"float:right\" name="response" 
                    value="delete" ><i class="fa fa-trash"></i>
                </button> <button style=\"float:right\" name="response" value="edit" >
                    <i class="fa fa-pencil"></i> </button>
            </form>
            ';
    }
    if (count($awss) > 0) {
        echo goBackToPageLink('adminacad/home', 'Refresh');
    }
}

/*
 * Course work,
 */
echo '<h1 class="div">Courses</h1>';
echo '
  <table class="admin table">
    <tr>
        <td>
            <a class="clickable" href="' . site_url('adminacad/enrollments') . '"
                title="Add/Remove student enrollments from  courses and assign grades"
            >Manage Enrollments</a>
            Add/Remove student enrollments from  courses and assign grades
            <br />

            <a href="' . site_url('adminacad/show_enrollments') . '"
            title="Show enrollments of a student"
            >Show Enrollments</a>
        </td>
        <td>
            <a class="clickable" href="' . site_url('adminacad/grades') . '">Manage Grades</a>
            <p>Add/Remove student enrollments from  courses and assign grades.</p>
        </td>
    </tr>
    <tr>
        <td>
            <a class="clickable" href="' . site_url('adminacad/courses') . '"> Manage Courses</a>
            <br />
            <a class="" href="' . site_url('adminacad/allcourses') . '">(Edit Description)</a>
        </td>
        <td> <a class="clickable"
             href="' . site_url('adminacad/slots') . '">Manage Slots</a> <br />
            Add/Delete or update slot.
        </td>
    </tr>
    <tr>
        <td><a class="clickable" href="' . site_url('adminacad/show_course_feedback') . '">
            Show Feedback on Courses</a> 
            <br /> Course Feedback data.
        </td>
        <td><a class="clickable" href="' . site_url('adminacad/feedbackquestionnaire') . '">
            Update Course-Feedback Questionnaire</a> 
            <br /> Prepare course feedback questionnaire.
        </td>
    </tr>
  </table>
  ';

// Journal clubs.
echo '<h1>Journal Clubs</h1>';
echo '
  <table class="admin table">
    <tr>
        <td><a class="clickable" href="' . site_url('adminacad/jc') . '">Add/Update Journal Clubs</a> </td>
        <td><a class="clickable" href="' . site_url('adminacad/jc_admins') . '">Manage Journal Club Admins</a></td>
    </tr>
  </table>
  ';

echo '<h1>Manage talks and seminars</h1>';
echo '<table class="admin table">';
echo '<tr>
        <td> <a class="clickable" href="' . site_url('adminacad/manages_talks') . '">Manage talks/seminar</td>
        <td> <a class="clickable" href="' . site_url('adminacad/manages_speakers') . '">Manage talk/seminar speakers</td>
    </tr>';
echo '</table>';

echo '<h1>Extra</h1>';
echo '<h2>AWS Housekeeping</h2>';

$badEntries = doAWSHouseKeeping();
if (0 == count($badEntries)) {
    echo printInfo('AWSs are in order.');
} else {
    // can't make two forms on same page with same action. They will merge.
    echo printNote('Following entries could not be moved to main AWS list. Most
        likely these entries have no data. You need to fix them. You need to know
        at least one supervisor to fix them. ');

    echo '<form action="' . site_url('adminacad/update_upcoming_aws') . '" method="post">';
    foreach ($badEntries as $aws) {
        echo alertUser('This AWS is incomplete.', false);
        echo arrayToVerticalTableHTML($aws, 'info', '', 'status,comment');
        echo '<input type="hidden" name="response" value="update" />';
        echo '<button name="id" value="' . $aws['id'] . '">Fix</button>';
    }
    echo '</form>';
}

echo '<h2>Information</h2>';
echo '
  <table border="0" class="admin table">
    <tr>
        <td>AWS summary <small>
            See the summary of all AWSs. You may be able to spot missing AWS entry
            in "Date Wise" list.  </small>
        </td>
        <td>
            <a href="' . site_url('adminacad/summary_user_wise') . '">User wise</a>
            <br />
            <a href="' . site_url('adminacad/summary_date_wise') . '">Date wise</a>
        </td>
    </tr>
    <tr>
        <td>List of AWS speakers</td>
        <td> <a href="' . site_url('adminacad/aws_speakers') . '">AWS speaker</a> </td>
    </tr>
  </table>';

echo goBackToPageLink('adminacad', 'Go back');

?>

