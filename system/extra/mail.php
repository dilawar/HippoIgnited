<?php

require_once BASEPATH . 'database.php';
require_once BASEPATH . 'extra/methods.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once 'vendor/autoload.php';

// Directory to store the mdsum of sent emails.
$maildir = FCPATH . '/temp/_mails';

if (!is_dir($maildir)) {
    mkdir(trim($maildir), 0700, true);
}

function awsEmailForMonday($monday)
{
    $res = [];

    // Collect all AWSs with full entry and non-completed entry. getUpcomingAWS
    // collects all AWS entries which not filled by AWS. Ideally such is never
    // be a  situation.
    $upcomingAws = getTableEntries('annual_work_seminars', 'date', "date='$monday'");
    $upcomingAws = array_merge($upcomingAws, getUpcomingAWSOnThisMonday($monday));

    $html = '';

    // if there is NO AWS this monday, notify users.
    if (count($upcomingAws) < 1) {
        $html .= p('Greetings,');
        $html .= p('I could not find any annual work seminar scheduled on ' . humanReadableDate($monday) . '.');

        $holiday = getTableEntry('holidays', 'date', ['date' => dbDate($monday)]);

        if ($holiday) {
            $html .= '<p>It is most likely due to following event/holiday: ' .
                        strtoupper($holiday['description']) . '.</p>';
        }

        $html .= '<br>';
        $html .= "<p>That's all I know! </p>";

        $html .= '<br>';
        $html .= '<p>-- Hippo</p>';

        return ['email_body' => $html, 'speakers' => null, 'pdffile' => null, 'subject' => "No AWS found for the next Monday ($monday)",
        ];
    }

    $speakers = [];
    $logins = [];
    $outfile = getDataDir() . 'AWS_' . $monday . '_';

    foreach ($upcomingAws as $aws) {
        $html .= awsToHTML($aws);
        $logins[] = $aws['speaker'];
        $speakers[] = __ucwords__(loginToText($aws['speaker'], false));
    }

    $res['speakers'] = $speakers;

    $firstAws = $upcomingAws[0];
    $venue = venueToShortText(
        $firstAws['venue'],
        $firstAws['vc_url'],
        $firstAws['vc_extra']
    );

    $chair = 'None assigned.';
    if (__get__($firstAws, 'chair', '')) {
        $chair = findAnyoneWithEmail($firstAws['chair']);
        $chair = loginToText($chair);
    }

    $data = [
        'CHAIR' => $chair, 'VENUE' => $venue, 'EMAIL_BODY' => $html, 'DATE' => humanReadableDate($monday), 'TIME' => humanReadableTime($firstAws['time']),
    ];
    $templ = emailFromTemplate('aws_template', $data);

    if (strtotime($monday) == strtotime('this monday')) {
        $templ['subject'] = 'Next week Annual Work Seminar ('
            . humanReadableDate($monday) . ') by ' . implode(', ', $speakers);
    } else {
        $templ['subject'] = 'Annual Work Seminar on ' . humanReadableDate($monday) . ' by ' . implode(', ', $speakers);
    }

    $templ['speakers'] = $speakers;
    return $templ;
}


function mailFooter()
{
    return '
    <hr><br />
    <small> 
        This email is automatically generated by
        <a href="https://ncbs.res.in/hippo">NCBS Hippo</a>.  If you are not an
        intended recipient of this message, please notify hippo@lists.ncbs.res.in.
    </small>
    ';
}

function sendHTMLEmailUnsafe(string $msg, string $subject, string $to, string $cclist = '', string $attachments = '', array $replyto=[]
): array {
    $ret = ['success' => false, 'msg' => ''];
    global $maildir;
    $mail = new PHPMailer(true);
    $conf = getConf();

    if (strlen(trim($msg)) < 1) {
        $ret['msg'] .= p('Message is too small');

        return $ret;
    }

    if (!__get__($conf['global'], 'send_emails', false)) {
        $ret['msg'] .= p('Email service has not been configured or sending email is not allowed.');

        return $ret;
    }

    $mail->isSMTP();
    $mail->Host = $conf['email']['smtp_server'];
    $mail->Port = intval($conf['email']['smtp_port']);
    $mail->Username = 'noreply@ncbs.res.in';
    $mail->Password = '';
    $mail->SMTPSecure = 'tls';

    $mail->setFrom('noreply@ncbs.res.in', 'NCBS Hippo');

    // Check if this email has already been sent.
    $archivefile = $maildir . '/' . md5($subject . $msg) . '.email';
    if (file_exists($archivefile)) {
        $ret['msg'] .= p('This email has already been sent. Doing nothing');
        $ret['msg'] .= p("-> archive file $archivefile ");
        $ret['success'] = true;

        return $ret;
    }

    $timestamp = date('r', strtotime('now'));
    $msg .= mailFooter();

    foreach (explode(',', $to) as $toaddr) {
        if (trim($toaddr)) {
            $mail->addAddress($toaddr);
        }
    }

    foreach (explode(',', $cclist) as $cc) {
        if (trim($cc)) {
            $mail->addCC($cc);
        }
    }
    $mail->addCC('hippologs@lists.ncbs.res.in');

    $mail->isHTML(true);

    foreach (explode(',', $attachments) as $f) {
        if (trim($f)) {
            $mail->addAttachment($f);
        }
    }

    // reply to
    if($replyto) {
        // replyto [ 'email', 'name' ]
        $mail->addReplyTo($replyto[0], $replyto[1]);
    }

    // Send email.
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->send();

    // generate md5 of email. And store it in archive.
    file_put_contents($archivefile, 'SENT');
    $ret['success'] = true;
    $ret['msg'] .= p('Successfully sent.');

    return $ret;
}

function sendHTMLEmail(string $msg, string $sub, string $to, string $cclist = '', string $attachments = '', array $replyto=[]
): array {
    try {
        return sendHTMLEmailUnsafe($msg, $sub, $to, $cclist, $attachments, $replyto);
    } catch (Exception $e) {
        $body = p('Hippo failed to send an email. Fix it soon. Error was <br/>');
        $body .= json_encode($e);
        $body .= p('Content of message:');
        $body .= "TO: $to, <br/>SUBJECT: $sub <br/> MSG: $msg";
        error_log($body);

        return sendHTMLEmailUnsafe($body, 'WARN: Hippo could not send an email', 'hippo@lists.ncbs.res.in');
    }
}

/* --------------------------------------------------------------------------*/
/**
 * @Synopsis Send email as plain text.
 *
 * @Param $msg
 * @Param $sub
 * @Param $to
 * @Param $cclist
 * @Param $attachments (csv list)
 *
 * @Returns
 */
/* ----------------------------------------------------------------------------*/
function sendPlainTextEmail($msg, $sub, $to, $cclist = '', $attachments = '')
{
    global $maildir;
    $conf = getConf();

    if (!is_string($msg)) {
        error_log('Email msg is not in string format');
        echo printInfo('Email msg not in string format');

        return;
    }

    printInfo("Trying to send email to $to, $cclist with subject $sub");
    if (strlen(trim($msg)) < 1) {
        return;
    }

    if (!array_key_exists('send_emails', $conf['global'])) {
        echo printInfo('Email service has not been configured.');
        error_log('Mail service is not configured');

        return;
    }

    if (false == $conf['global']['send_emails']) {
        echo alertUser('<br>Sending emails has been disabled in this installation');

        return;
    }

    // Check if this email has already been sent.
    $archivefile = $maildir . '/' . md5($sub . $msg) . '.email';
    if (file_exists($archivefile)) {
        echo printWarning('This email has already been sent. Doing nothing');

        return;
    }

    // printInfo( "... preparing email" );

    $timestamp = date('r', strtotime('now'));

    $msg .= mailFooter();

    $textMail = html2Markdown($msg, $strip_inline_image = true);

    $msgfile = tempnam('/tmp', 'hippo_msg');
    file_put_contents($msgfile, $textMail);

    $to = implode(' -t ', explode(',', trim($to)));

    // Use \" whenever possible. ' don't escape especial characters in bash.
    $cmd = FCPATH . "scripts/sendmail.py -t $to -s \"$sub\" -i \"$msgfile\" ";

    if ($cclist) {
        $cclist = implode(' -c ', explode(',', trim($cclist)));
        $cmd .= "-c $cclist";
    }

    if ($attachments) {
        foreach (explode(',', $attachments) as $f) {
            $cmd .= " -a \"$f\" ";
        }
    }

    $out = `$cmd`;

    error_log("<pre> $cmd </pre>");
    error_log('... $out');
    error_log('Saving the mail in archive' . $archivefile);

    // generate md5 of email. And store it in archive.
    file_put_contents($archivefile, 'SENT');

    // delete the tmp file.
    unlink($msgfile);

    return true;
}

/* --------------------------------------------------------------------------*/
/**
 * @Synopsis Notify user about Upcoming AWS.
 *
 * @Param $speaker
 * @Param $date
 * @Param $aws_id
 *
 * @Returns
 */
/* ----------------------------------------------------------------------------*/
function notifyUserAboutUpcomingAWS($speaker, $date, $aws_id = -1)
{
    // Now insert a entry into email database.
    $templ = getEmailTemplateById('aws_confirmed_notify_speaker');
    // Replace text in the template.
    $msg = str_replace('%SPEAKER%', loginToText($speaker), $templ['description']);
    $msg = str_replace('%DATE%', humanReadableDate($date), $msg);

    $to = getLoginEmail($speaker);

    // CC to PI as well.
    $pi = getPIOrHost($speaker);
    if ($pi) {
        $templ['cc'] = $templ['cc'] . ",$pi";
    }

    // check if there is any clickable url in queries table.
    if (intval($aws_id) >= 0) {
        $qID = getQueryWithIdOrExtId('upcoming_aws.' . $aws_id);
        if ($qID >= 0) {
            $msg = addClickabelURLToMail($msg, queryToClickableURL($qID, 'Click here to acknowledge'));
        }
    }

    // Append the current user who assigned it.
    $msg .= '<p>This AWS was assigned by ' . whoAmI() . '.</p>';

    return sendHTMLEmail($msg, 'ATTN! Your AWS date has been fixed', $to, $templ['cc']);
}
