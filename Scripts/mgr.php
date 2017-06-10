<?php
//<!--Manage Account control-->
include('./uni.php');

//BUILT IN PAGE DEFUALTS
$GLOBALS['allowSignups'] = true;
$GLOBALS['requireVerifiedEmail'] = 1; //ALL ACCOUNTS CREATED WITH THIS OFF ARE CONSERED VERIFIED (0 - REQUIRE, 1 - AUTOVERIFY)
$GLOBALS['DupeEmail'] = 1;
$GLOBALS['adminPassword'] = 'habibi'; //WARNING SENT PLAIN TEXT OVER NETWORK.
$GLOBALS['accountDir'] = '../settings/info.xml';
$GLOBALS['userDir'] = '../users/';
$GLOBALS['PurgeQuestion'] = 8;

//GET ENVORNMENTAL VARIABLES FROM EXTERNAL FILE
$env = getJson('../settings/env');
foreach($env as $key=>$value){
    $GLOBALS[$key] = $value;
}
unset($env);

$GLOBALS['quest'] = array('types' => 4, 'extra' => 3, 'total' => 50);

if(rfeild('func') != 'resetEnvVars' && rfeild('func') != 'resetGlobalVars'){
    $GLOBALS['xml'] = simplexml_load_file($GLOBALS['accountDir']);
    $GLOBALS['userid'] = userSearch($GLOBALS['xml'], $_POST['user']);
}

// phpinfo();
$_POST['func'](); // call a function based on what the client request
exit;

function reg(){//Resiter a new account
    if(count(rfeild('user')) > 25)
        error(6);
    if($GLOBALS['userid'] != -1)
        error(0);
    if($GLOBALS['allowSignups'] == false)
        error(2);
    if(!$GLOBALS['DupeEmail'] && userSearch($GLOBALS['xml'], rfeild('email'), 'Email') != -1)
        error(9);
    $new = $GLOBALS['xml']->addChild('user');
    $new->addChild('username', rfeild('user'));
    $new->addChild('date', date('r'));
    $new->addChild('pass', rfeild('password'));
    $new->addChild('salt', rfeild('fullhash'));
    $new->addChild('firstName', rfeild('firstName'));
    $new->addChild('lastName', rfeild('lastName'));
    $new->addChild('Email', rfeild('email'));
    $new->addChild('verified', $GLOBALS['requireVerifiedEmail']);
    $new->addChild('Active', 'never');
    resetUserProgress(true);
    save();
    if($GLOBALS['requireVerifiedEmail'] == 0)
        sEmail();
}
function resetUserProgress($reg = false){ //resets the problem progress for a user
    if(reg || (isset($_POST['password']) && $_POST['password'] == $GLOBALS['adminPassword'])){
        $def = array();
        for($i = 0; $i < $GLOBALS['quest']['types']; $i++){
            array_push($def, array());
            for($j = 0; $j < $GLOBALS['quest']['extra']; $j++)
                $def[$i][$j] = 0;
        }
        saveJson($GLOBALS['userDir'] . rfeild('user'), $def);
    }
}

function rsalt(){//request the account hash
    if($GLOBALS['userid'] == -1)
        error(1);
    echo $GLOBALS['xml']->user[$GLOBALS['userid']]->salt;
}
function signIn($call = false){//Checks to see if an accounts password/username are properly suppiled
    if($GLOBALS['userid'] == -1)
        error(1);
    elseif($GLOBALS['xml']->user[$GLOBALS['userid']]->verified == 0)
        error(4);
    if(rfeild('password') == $GLOBALS['xml']->user[$GLOBALS['userid']]->pass){
        if($call)
            return true;
        echo 'success';
    }
    else
        error(1);
}
function sEmail(){//Sends account verification email
    rfeild('user');
    if(!mail($GLOBALS[$i]->user[$GLOBALS['userid']]->Email, 
    'Verification Email', 'Please Click the link below to verify your account. ' +
    $_SERVER['SERVER_NAME']+$_SERVER['PHP_SELF']+"?func=vEmail&akey=" + $GLOBALS[$i]->user[$GLOBALS['userid']]->salt)){
        $GLOBALS['xml']->user[$GLOBALS['userid']]->verified = 1; //Just auto set it
        save();
        error(7);
    }
}
function vEmail(){//Verifies an account
    $id = userSearch($GLOBALS['xml'], $_GET['user']);
    if($id == -1){
        echo '<p>The user account you have requested does not exist</p>';
    }
    else{
        if($GLOBALS['xml']->user[$id]->salt != $_GET['akey'])
            echo "<p>Invalid verification address</p>";
        else{
            $GLOBALS['xml']->user[$id]->verified = 1; 
            save();
            echo "<p>Account successfully validated</p>";
        }
    }
    echo "<br><a href='.'>Return to main website</a>";
}

function prob(){//updates a users stats
    if(signIn(true)){
        if(($GLOBALS['PurgeQuestion'] * 1000 * 60)  < rfeild('time'))
            exit;//The question is past acceptable limits
        $vals = getJson($GLOBALS['userDir'] . $GLOBALS['xml']->user[$GLOBALS['userid']]->username);
        $GLOBALS['xml']->user[$GLOBALS['userid']]->Active = date('r');
        save();
        $feild = rfeild('type') -1;
        $vals[$feild][0] += rfeild('time');
        if(intval(rfeild('correct')))
            $vals[$feild][1]++;
        else
            $vals[$feild][2]++;
        array_push($vals[$feild], array(round(rfeild('time'), 3), intval(rfeild('correct'))));
        if(count($vals[$feild]) > $GLOBALS['quest']['total'])
            array_splice($vals[$feild], $GLOBALS['quest']['extra'],1);
        saveJson($GLOBALS['userDir'] . $GLOBALS['xml']->user[$GLOBALS['userid']]->username, $vals);
    }
}

function setEnvVars(){//set custom Environment variables for the group
    if(rfeild('password') != $GLOBALS['adminPassword'])
        error(1);
    $a = getJson('../settings/env');
    foreach($_POST as $key=>$val)
        if(isset($a[$key]))
            $a[$key] = $val;
    saveJson('../settings/env', $a);
    echo 'getEnvVars';
}
function resetEnvVars(){ //Reset the envornimental variables
    if(rfeild('password') == $GLOBALS['adminPassword']){
        $env['adminPassword'] = 'habibi';
        $env['accountDir'] = '../settings/info.xml';
        $env['allowSignups'] = true;
        $env['userDir'] = '../users/';
        $env['PurgeQuestion'] = 8;
        $env['DupeEmail'] = 1;
        $env['requireVerifiedEmail'] = 1;
        $env['Announcement'] = "";
        // $env['PageEmail'] = 'example@mail.com';
        // $env['Host'] = "stmp://Example.server";
        // $env['Port'] = "465";
        // $env['EmailPassword'] = "Email Password (Stored PlainText!)";
        saveJson('../settings/env', $env);
        echo 'getEnvVars';
    }
    else
        error(1);
}
function getEnvVars(){//Prints a table of Environment Variables
    if(rfeild('password') != $GLOBALS['adminPassword'])
        error(1);
    printTable(getJson('../settings/env'), 'envTable', ['Key', 'Value'], ['saveEnv','resetEnvVars']);
}
function setUserVars(){//set custom question variables for each user or the group
    if(rfeild('password') != $GLOBALS['adminPassword'])
        error(1);
    $path = '../settings/globalVars';
    $create = 0;
    if($GLOBALS['userid'] != -1){
        $path = '../settings/users/' . rfeild('user');
        resetVars($path);
    }
    $a = getJson($path);
    foreach($_POST as $key=>$val)
        if(isset($a[$key]))
            $a[$key] = $val;
    saveJson($path, $a);
    echo 'getUserVars';
}
function resetVars($new = ""){
    if(rfeild('password') != $GLOBALS['adminPassword'])
        error(1);
    if(isset($_POST['user']) && $_POST['user'] != "" && $new == ""){
        if(file_exists('../settings/users/'. $_POST['user']))
            unlink('../settings/users/'. $_POST['user']);
    }
    else{
        $path = '../settings/globalVars';
        if($new != "")
            $path = $new;
        $vars['min']= 1;
        $vars['max']= 25;
        $vars['neg']= .2;
        $vars['frac']= .1;
        saveJson($path, $vars);
    }
    if($new == "")
        echo 'getUserVars';
}
function getUserVars(){//Prints a table of User Variables
    $r;
    if(isset($_POST['user']) && $_POST['user'] != "" && file_exists('../settings/users/'. $_POST['user']))
        $r = getJson('../settings/users/'. $_POST['user']);
    else if(file_exists('../settings/globalVars'))
        $r = getJson('../settings/globalVars');
    else{
        $_POST['password'] = $GLOBALS['adminPassword'];
        resetGlobalVars();
        error(8);
    }
    if(rfeild('table') == 1){
        // var_dump($r);
        printTable($r, 'studentVars', ['Key', 'Value'], ['saveUser','resetVars']);
    }
    else if(rfeild('table') == 2)
        echo json_encode($r);
    else if(rfeild('table') == 3)
        return json_encode($r);
}

function daccount($id = -1){//remove an account (requires admin privilages)
    if(rfeild('password') == $GLOBALS['adminPassword']){
        if($id != -1)
            $user = $id;
        else{
            rfeild('user');
            $user = $GLOBALS['userid'];
        }
        if(file_exists('../settings/users/'. $_POST['user']))
            unlink('../settings/users/'. $_POST['user']);
        unlink($GLOBALS['userDir'] . $GLOBALS['xml']->user[$user]->username);
        unset($GLOBALS['xml']->user[$user]);
        save();
    }
    else 
        error(1);
}
function getStudentStats(){
    if(rfeild('password') != $GLOBALS['adminPassword'])
        error(1);
    if($GLOBALS['userid'] == -1)
        error(6);
    $r = getJson('../users/'. $_POST['user']);
    echo "<table class='infoTable border' id='DetailedStatsT' cellpadding='5' cellspacing='5'>";
    echo "<tr>";
    echo "<th>Problem</th>";
    echo "<th>Total Time</th>";
    echo "<th>Average Time</th>";
    echo "<th>Total Correct</th>";
    echo "<th>Total Incorrect</th>";
    echo "<th>Recent Percent</th>";
    echo "</tr>";
    getStat(0, $r, '+');
    getStat(1, $r, '-');
    getStat(2, $r, '*');
    getStat(3, $r, '/');
    echo "</table>";
}
function getStat($i, $r, $sign){
    if(rfeild('password') != $GLOBALS['adminPassword'])
        error(1);
    $t = array(0,0);
    for($j = $GLOBALS['quest']['extra']; $j < count($r[$i]); $j++){
        $t[0] += $r[$i][$j][0];
        $t[1] += $r[$i][$j][1];
    }
    $div = count($r[$i])-$GLOBALS['quest']['extra'];
    if($div == 0){
        $t[0] = 0;
        $t[1] = 0;
    }
    else{
        $t[0] /= $div;
        $t[1] /= $div;
    }
    echo "<tr>";
    echo "<td>". $sign."</td>";
    echo "<td>". pTime($r[$i][0])." min</td>";
    echo "<td>". pTime($t[0])."min</td>";
    echo "<td>". $r[$i][1]."</td>";
    echo "<td>". $r[$i][2]."</td>";
    echo "<td>". round($t[1], 3)."</td>";
    echo "</tr>";
}
function save(){ // Saves the XML file data
    $GLOBALS['xml']->asXML($GLOBALS['accountDir']);
}

function studentTable(){
    if(rfeild('password') != $GLOBALS['adminPassword'])
        error(1);
    echo "<table class='infoTable border' id='studentInfo' cellpadding='5' cellspacing='5'>";
    echo "<tr>";
    echo "<th>View Additional</th>";
    echo "<th>Student</th>";
    echo "<th>Email</th>";
    echo "<th>Total Time</th>";
    echo "<th>Recent Correct/Total</th>";
    echo "<th>Remove</th>";
    echo "</tr>";
    for($i = 0; $i < count($GLOBALS['xml']->user); $i++){
        if($GLOBALS['userid'] != -1){
            $a = getJson($GLOBALS['userDir']. rfeild('user'));
            $i = $GLOBALS['userid'];
        }
        else
            $a = getJson($GLOBALS['userDir']. $GLOBALS['xml']->user[$i]->username);
        $t;
        for($j = 0; $j < $GLOBALS['quest']['extra']; $j++)
            $t[$j] = 0;
        for($j = 0; $j < count($a); $j++){
            $t[0] += $a[$j][0];
            $t[1] += count($a[$j]) - $GLOBALS['quest']['extra'];
            for($k = $GLOBALS['quest']['extra']; $k < count($a[$j]); $k++)
                if($a[$j][$k][1])
                    $t[2]++;
        }
        if($t[1] != 0)
            $t[2] /= $t[1];
        echo "<tr>";
        echo "<td onclick=\"seeStats('" . $GLOBALS['xml']->user[$i]->username. "')\" title='Last Active: ". $GLOBALS['xml']->user[$i]->Active. "'>+</td>";
        echo "<td title='". $GLOBALS['xml']->user[$i]->username ."'>". $GLOBALS['xml']->user[$i]->firstName. " ". $GLOBALS['xml']->user[$i]->lastName ."</td>";
        echo "<td title='Verified: ". $GLOBALS['xml']->user[$i]->verified ."'>". $GLOBALS['xml']->user[$i]->Email ."</td>";
        echo "<td>" . pTime($t[0]). "min.</td>";
        echo "<td title='Total Questons: " . $t[1]. "'>" . round($t[2], 3). "</td>";
        echo "<td onclick=\"removeUser('" . $GLOBALS['xml']->user[$i]->username. "')\">-</td>";
        
        if($GLOBALS['userid'] != -1){
            //echo "<tr><button onclick='Reset('".rfeild('user')."')'>Reset User Stats</button></tr>";
            break;
        }
        echo "</tr>";
    }
    echo "</table>";
}
function printTable($array, $tname, $aTitles, $idButton){
    $titles = getJson('../settings/title');
    echo "<form id=".$tname." class='infoTable'><table cellpadding='5' cellspacing='5'>";
    echo "<tr><th id=key>".$aTitles[0]."</th><th id=value>".$aTitles[1]."</th></tr>";
    foreach($array as $key=>$value){
        echo '<tr>';
        echo "<td title='" . $titles[$key]. "'>". $key. "</td>";
        echo "<td><input id='". $key ."' value='" . $value . "' required></td>";
        echo '</tr>';
    }
    echo "<tr><td><button id='".$idButton[0]."' class=save>Set</button></td>";
    echo "<td><button id='".$idButton[1]."' class=reset>Reset</button></td></tr>";
    echo '</table></form>';
}

function getAnnouncement(){ //Sends the clients announcement text
    echo $GLOBALS['Announcement'];
    exit;
}

function error($id){ //Stops this page from loading futher data and reports an error back to the user
    /* List of errors
    0 - User already exists and a new one cannot be created
    1 - User does not exist -- Passwords do not match
    2 - new accounts cannot be created
    --REMOVED--3 - submitted password does not match the password stored
    4 - Email Unverified
    5 - Missing required feild
    6 - Code Exicution Error
    7 - Email not sent successfully
    8 - requied Global Vars Do not exist
    9 - Two accounts with the same email is disallowed
    */
    echo 'e'. $id;
    http_response_code(400); // Stop code exicution on client side for success
    exit;
}
function userSearch($xmlfile, $feild, $prop = 'username'){//Reports the location of a user
    $existingUser = -1;
    for($i = 0; $i < count($xmlfile->user) && $existingUser == -1; $i++)
        if(strtolower($xmlfile->user[$i]->$prop) == strtolower($feild))
            $existingUser = $i;
    return $existingUser;
}
function hsalt(){ //generates half a salt
    echo rand(100, 999);
}
function pTime($ms, $inter = 60){
    return round($ms/1000/$inter, 3);
}
function rfeild($feild){//Stops the program if a required feild is not supplied
    if(isset($_POST[$feild]) && $_POST[$feild] != '')
        return $_POST[$feild];
    else{
        error("5");
    }
}

function checkPerms(){
    $check = array(
            '../settings',
            $GLOBALS['userDir'], 
            $GLOBALS['accountDir']
        );
    
    if(rfeild('password') == $GLOBALS['adminPassword']){
        for($i = 0; $i < count($check); $i++)
            if(fileperms($check[$i]) != 44800){
                if(rfeild('fix') == 1){
                    chmod($check[$i], 0700);
                }
                else{
                    echo 'inccorect';
                    echo fileperms($check[$i]);
                    exit;
                }
            }
        echo 'ok';
        exit;
    }
    else
        error(1);
}
?>