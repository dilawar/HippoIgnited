<?php

require_once BASEPATH. 'autoload.php';
echo userHTML( );

$thisSem = getCurrentSemester( ) . ' ' . getCurrentYear( );
// Only show this section if user is eligible for AWS.
$userInfo = getLoginInfo( $_SESSION[ 'user' ] );

$html = '<table class="admin">';
$html .= '<tr>
      <td>
        <i class="fa fa-user fa-3x"></i><a  class="clickable" 
                href="' . site_url( '/user/info' ) . '"> My Profile</a>
        <br /> See and edit (most of) your details.
      </td>
        <td>
            <i class="fa fa-book fa-3x"></i>
            <a class="clickable" href="'. site_url('/user/courses' ) . '">My Courses</a>
            <br /> Manage courses for semester  (' . $thisSem . ' ) 
                <small>Register/deregister courses for this semster. </small>
        </td>
    </tr>';

$html .= '<tr>';
if( __get__($userInfo, 'eligible_for_aws', 'NO' ) == 'YES' )
{
    $html .=  '<td> <i class="fa fa-graduation-cap fa-3x"></i>
        <a class="clickable" href="'. site_url("/user/aws"). '">My AWS</a> <br />
        List of your Annual Work Seminar <br />
        <small> See your previous AWSs and update them. Check
        the details about upcoming AWS and provide preferred dates.
        </small> <br />
        <a href="'. site_url("/user/update/supervisors"). '">Update TCM Members/Supervisors</a>
        </td>';
}
$html .= '<td>
    <i class="fa fa-hand-pointer-o fa-3x"></i>
    <a class="clickable" href="'. site_url("/user/book/venue"). '">Quickbook</a>
    </td>';

$html .= '</tr></table>';
echo $html;

// Journal club entry.
echo ' <h1>Journal clubs</h1> ';
$table = '<table class="admin">
    <tr>
        <td>
            <a class="clickable" href="'. site_url("/user/jc"). '">My Journal Clubs</a> <br />
            Subscribe/Unsubscribe from journal club. See upcoming presentation.
            Vote on presentation requests.
         </td>
        <td>
            <a class="clickable" href="'. site_url("/user/jc/presentation_requests"). '">
                My JC Presentation Requests</a>
            <br />
            Submit a journal paper and a preferred date to present it. You can submit
            one presentations requests for nay date. The community can vote on the
            presentation requests.
         </td>
    </tr>';

if( isJCAdmin( $_SESSION[ 'user' ] ) )
{
    $table .= '<tr>
        <td>
        <i class="fa fa-cogs fa-3x"></i>
        <a class="clickable" href="'. site_url("/user/jc/admin"). '">JC Admin</a> <br />
        Journal club admin</td>
        <td></td>
    </tr>';
}


$table .= '</table>';
echo $table;

echo '<h1>Booking</h1>';

$html = '<table class="admin">';
$html .= '
    <tr>
    <td>
        <i class="fa fa-hand-pointer-o fa-3x"></i>
         <a class="clickable" href="'. site_url("/user/book/venue"). '">Quickbook</a>
         <br />
         Book non-public events i.e. no email needs to be sent to Academic community.
         Otherwise use <tt>BOOK TALK/SEMINAR</tt> link below.
    </td>
    <td>
        <a href="'. site_url("/user/book/show_requests") . '" class="clickable">My booking requests</a> <br />
        You can see your unapproved requests. You can modify their description, and
        cancel them if neccessary.
        <br />
        <a class="clickable" href="'. site_url("/user/book/show_events"). '">My approved events</a> <br />
        Cancels already approved requests.
    </td>
    </tr>
   <tr>
    <td>
        <i class="fa fa-comments fa-3x"></i>
        <a class="clickable" href="'. site_url("/user/book/talk"). '">Book Talk/Seminar</a>
        <br />
        Register a new talk, seminar, or a thesis seminar.
        <small>Keep the email and photograph of speaker handy, not neccessary but highly recommended.</small>
    </td>
    <td>
        <a class="clickable" href="'. site_url("/user/book/talk/edit"). '">Manage my talks</a> <br />
        Edit/update a previously registered talk and book a venue for it
    </td>
   </tr>
   </table>';
echo $html;

// Community services.
echo "<h1>Community services</h1>";
echo '<table class="admin">
    <tr>
        <td> <i class="fa fa-archive fa-3x"></i>
            <a class="clickable" href="'. site_url("/user/inventory/browse"). '">Browse inventory</a> <br />
            You can browse inventory. Items listed here can be borrowed.
        </td>
        <td>
            <a class="clickable" href="'. site_url("/user/inventory/add"). '">My Inventry Items</a>
            <br /> <br />
            Add items to inventory.
            By adding item here, you are letting others know that
            they can borrow this item from you.
        </td>
   </tr>
    <tr>
       <td>
            <i class="fa fa-building fa-3x"></i>
            <a class="clickable" href="'. site_url("/user/tolet/browse"). '"> Browse TO-LET list</a>
        </td>
       <td>
             <a class="clickable" href="'. site_url("/user/tolet/create"). '">My TO-LET and Alerts</a> <br />
            Create email-alerts and create a TO-LET entry for community.
            Email is sent to registered user.
        </td>
   </tr>
   </table>';


if( anyOfTheseRoles( 'ADMIN,BOOKMYVENUE_ADMIN,JOURNALCLUB_ADMIN,AWS_ADMIN' ) )
{
   echo "<h1> <i class=\"fa fa-cogs\"></i>   Admin</h1>";
   $roles =  getRoles( $_SESSION['user'] );

   $html = "<table class=\"admin\">";

   if( in_array( "ADMIN", $roles ) )
       $html .= '<tr>
           <td> All mighty ADMIN </td>
           <td><a class="clickable" href="'. site_url("/admin"). '">Admin</a> </td>
       </tr>';

   if( in_array( "BOOKMYVENUE_ADMIN", $roles ) )
       $html .= '<tr><td>Approve/reject, modify or cancel booking requests.</td>
       <td> <a class="clickable" href="'. site_url("/admin/book"). '">BookMyVenue Admin</a></td> </tr>';

   if( in_array( "AWS_ADMIN", $roles ) )
       $html .= '<tr><td>Schedule AWS. Update AWS speaker list.
       Manage running courses and more ...
       </td>
       <td> <a class="clickable" href="'. site_url("/admin/acad"). '">Academic Admin</a></td> </tr>';

   $html .= "</table>";
   echo $html;
}

?>