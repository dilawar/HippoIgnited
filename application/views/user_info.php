<?php

require_once BASEPATH . 'autoload.php';
require_once FCPATH . 'system/extra/me.php';

echo userHTML();

$picPath = getLoginPicturePath(whoAmI());

///////////////////////////////////////////////////////////////////////////
// PICTURE OF SPEAKER
///////////////////////////////////////////////////////////////////////////
$tab = '<table class="">';
$tab .= '<tr><td>';

if (file_exists($picPath)) {
    $tab .= showImage($picPath);
} else {
    $tab .= printInfo('I could not find your picture in my database.  Please upload one.');
}

$tab .= '</td></tr><tr><td>';

// Form to upload a picture
$picAction = '<form action="' . site_url('/user/info/upload_picture') . '" 
    method="post" enctype="multipart/form-data">';

$picAction .= '<p><small>
    This picture will be used in AWS notifications. It will be
    rescaled to fit 5 cm x 5 cm space. I will not accept any picture bigger
    than 1MB in size. Allowed formats (PNG/JPG/GIF/BMP).
    </small></p>
    ';
$picAction .= '<input type="file" name="picture" id="picture" value="" />';
$picAction .= '<button name="Response" title="Upload your picture" value="upload">Upload</button>';
$picAction .= '</form>';
$picAction .= '</td></tr>';
$picAction .= '</table>';
$picAction .= '<br>';

$tab .= $picAction;
echo $tab;

$info = getUserInfo(whoAmI(), true);
$editables = array_keys(getProfileEditables($info));

echo '<h1>Your profile </h1>';

$specializations = array_map(
    function ($x) {
        return $x['specialization'];
    },
    getAllSpecialization()
);

$info['specialization'] = arrayToSelectList(
    'specialization',
    $specializations,
    [],
    false,
    __get__($info, 'specialization', '')
);

// Prepare select list of faculty.
$faculty = getTableEntries('faculty', 'email', "status='ACTIVE'");
$facultyEmails = [];
$facMap = [];
foreach ($faculty as $fac) {
    $facultyEmails[] = $fac['email'];
    $facMap[$fac['email']] = arrayToName($fac, $with_email = true);
}

$info['pi_or_host'] = arrayToSelectList(
    'pi_or_host',
    $facultyEmails,
    $facMap,
    false,
    __get__($info, 'pi_or_host', '')
);

echo '<form method="post" action="' . site_url('/user/info/action') . '" >';
echo dbTableToHTMLTable('logins', $info, implode(',', $editables));
echo '</form>';

if ('NO' == __get__($info, 'eligible_for_aws', 'NO')) {
    echo alertUser(
        'If you are <tt>ELIGIBLE FOR AWS</tt>, please write to academic office to include 
        your name.'
    );
}

// TODO: ENABLE IS LATER.
//echo '<h3>Submit request to academic office</h3>';
//$form = ' <form method="post" action="user_aws_request.php">';
//if( strtoupper( $info['eligible_for_aws'] ) == "YES" )
//    $form .= ' <button type="submit" name="request_to_academic_office"
//        value="remove_me_from_aws_list">Remove me from AWS list</button> ';
//else
//    $form .= ' <button type="submit" name="request_to_academic_office"
//        value="add_me_to_aws_list">Add me to AWS list</button> ';
//
//$form .= '</form>';
//echo $form;

echo '<h2>Advanced settings</h2>';
echo printInfo('This section is related to mobile apps developed by third party.');
$myKeys = getUserKeys(whoAmI());

$table = '<table class="info">';
$table .= '<tr><th>ID</th><th>KEY</th> <th>created on</th> <th></th></tr>';
foreach ($myKeys as $key) {
    $id = $key['id'];
    $revokeForm = '<form method="post" action="' . site_url("/user/revoke_key/$id") . '">';
    $revokeForm .= '<button>Revoke</button>';
    $revokeForm .= '</form>';
    $table .= '<tr>' . arrayToRowHTML($key, 'info', 'level,login,ignore_limits', '', false)
        . "<td> $revokeForm </td></tr>";
}

$table .= '</table>';
echo $table;

// Add a new key
$form = "<form method='post' action='" . site_url('/user/generate_key') . "'>";
$form .= '<button>Generate a New Key</button>';
$form .= '</form>';

echo $form;

echo goBackToPageLink('/user/home', 'Go back');
