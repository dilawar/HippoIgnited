<?php

require_once BASEPATH . 'autoload.php';

if (!$_POST['response']) {
    // Go back to previous page.
    goToPage('admin_acad.php', 0);
    exit;
} elseif ('submit' == $_POST['response']) {
    $res = updateTable(
        'talks',
        'id',
        'class,host,coordinator,title,description',
        $_POST
    );
    if ($res) {
        echo printInfo('Successfully updated entry');

        // TODO: Update the request or event associated with this entry as well.
        $externalId = getTalkExternalId($_POST);

        $talk = getTableEntry('talks', 'id', $_POST);
        assert($talk);

        $success = true;

        $event = getEventsOfTalkId($_POST['id']);
        $request = getBookingRequestOfTalkId($_POST['id']);

        if ($event) {
            echo printInfo('Updating event related to this talk');
            $event['title'] = talkToEventTitle($talk);
            $event['description'] = $talk['description'];
            $res = updateTable('events', 'gid,eid', 'title,description', $event);
            if ($res) {
                echo printInfo('... Updated successfully');
            } else {
                $success = false;
            }
        } elseif ($request) {
            echo printInfo('Updating booking request related to this talk');
            $request['title'] = talkToEventTitle($talk);
            $request['description'] = $talk['description'];
            $res = updateTable('bookmyvenue_requests', 'gid,eid', 'title,description', $request);
            if ($res) {
                echo printInfo('... Updated successfully');
            } else {
                $success = false;
            }
        }

        if ($success) {
            echo goToPage('admin_acad_manages_talks.php', 0);
            exit;
        }
    } else {
        echo printWarning('Failed to update the talk ');
    }
} else {
    echo printInfo('Unknown operation ' . $_POST['response']);
}

echo goBackToPageLink('admin_acad_manages_talks.php', 'Go back');
exit;
