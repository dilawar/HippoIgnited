<?php

function booking_expiring_notice_cron()
{
    /* Everyday check for recurrent events. On 7 days before last events send
     * and email to person who booked it.
     */
    $today = 'today';
    $awayFrom = strtotime('now') - strtotime('13:00');

    if ($awayFrom > -1 && $awayFrom < 15 * 60) {
        echo printInfo('1PM. Checking for recurrent events expiring in 7 days');
        echo printInfo('1PM. Checking for recurrent events expirings in future');

        // Get all events which are grouped.
        $groupEvents = getActiveRecurrentEvents('today');

        foreach ($groupEvents as $gid => $events) {
            // Get last event of the group.
            $e = end($events);
            $lastEventOn = $e['date'];
            $createdBy = $e['created_by'];
            $eventHtml = arrayToVerticalTableHTML($e, 'event');
            $template = emailFromTemplate('event_expiring', ['USER' => loginToText($createdBy), 'EVENT_BODY' => $eventHtml]
                    );

            $to = getLoginEmail($createdBy);
            if (!$to) {
                echo printInfo('Invalid email.');

                continue;
            }

            echo printInfo("Group id $gid by $to last event $lastEventOn");

            $cclist = $template['cc'];
            $title = $e['title'];

            if (strtotime($today) + (7 * 24 * 3600) == strtotime($lastEventOn)) {
                $subject = "IMP! Your recurrent booking '$title' is expiring in 7 days";
                echo printInfo($subject);
                sendHTMLEmail($template['email_body'], $subject, $to, $cclist);
            } elseif (strtotime($today) + (1 * 24 * 3600) == strtotime($lastEventOn)) {
                $subject = "ATTN! Your recurrent booking '$title' is expiring tomorrow";
                echo printInfo($subject);
                sendHTMLEmail($template['email_body'], $subject, $to, $cclist);
            }
        }
    }
}
