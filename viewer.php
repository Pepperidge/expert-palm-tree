<!--This File Contains The Formating Code for the Student Accounts-->
<!DOCTYPE HTML>
<html>
<head>
    <title>Account Management Control</title>
    <meta name="author" content="Nicholas Chieppa">
    <meta name="description" content="Viewer for the student stats">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha1.js"></script>
    <link rel="icon" href="./icon.ico"/>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <link rel=stylesheet type="text/css" href="./styles/style.css"></link>
    <link rel=stylesheet type="text/css" href="./styles/button.css"></link>
</head>
<?php //Simple Password Code
    include('./Scripts/uni.php');
    $env = getJson('./settings/env');
    foreach($env as $key=>$value){
        $GLOBALS[$key] = $value;
    }
    unset($env);
    $Password = $GLOBALS['adminPassword'];
    if($Password != ''){
        if(isset($_POST['key'])){
            if($_POST['key']!=$Password)
                keybox('key');
        }else
            keybox('key');
    }
    function keybox($name){
        echo '<div align=center>';
        echo '<h1>A password is required to view this page</h1>';
        echo '<form method=POST>';
        echo "<input name=$name placeholder=password type='password'/>";
        echo '<input type=submit />';
        echo '</form></div>';
        exit;
    }
?>
<body>
    <div id=header class=edge>
        <!--Header-->
        <table id=CommandBar class='left vcenter'>
            <tr>
                <td id='home' onclick='home()'>Home🏠</td>
                <td id='logout' onclick=reload()>Logout 🚪</td>
            </tr>
        </table>
        <table id='' class='right vcenter'>
            <tr>
                <td id=displayName>Admin account control page.</td>
            </tr>
        </table>
    </div>
    <div class="spacer">
        &nbsp;
    </div>
    <div id=content>
        <!--<h2 id=permWarning class='error hcenter extraStats' style='text-align:center'>Warning file viewing permissions not properly set: <a class='yesAccount' style='text-decoration: underline;'>Click here to fix</a></h2>-->
        <div id=Selection class='hcenter content'>
            <h2>Make your selection:</h2>
            <table>
                <div id=sEnv class='selectBoxes'>Environment Settings</div>
                <div id=sStudents class='selectBoxes'>Student Stats</div>
            </table>
        </div>
        <div id=envCont class='hcenter content'>
            <div id=envContTable class='border'></div>
            <div id=studentContTable class='border'></div>
        </div>
        <div id=studentsCont class='hcenter content'>
            <div id=studentTable></div>
        </div>
        <div id=singleStudnet class='hcenter content'>
            <a style='text-decoration: underline;' class='back wcenter'>Go Back</a><br>
            <div id=studentStats class='wcenter'></div><br>
            <div id=DetailedStats class=wcenter></div><br>
            <div id=studentSettings class=wcenter>Personal Single User Settings</div>
        </div>
    </div>
    <div id=errorboxContainer class='shadow edge'>
        <table id='errorbox' class='infoTable hcenter'> 
            <tr>
                <th>An Error Has Occured</th>
            </tr>
            <tr>
                <td>
                    <p id=inputerror class=error></p>
                </td>
            </tr>
            <tr>
                <td>
                    <button id='errorok'>Understood</button>
                </td>
            </tr>
        </table>
    </div>
</body>
<script type="text/javascript" src="./Scripts/shared.js"></script>
<script type="text/javascript">var $;
    var pass = "<?php echo $GLOBALS['adminPassword'] ?>";
    var currentUser;
    hide('Selection');
    
    function home(){
        hide('Selection');
        hcenter();
        // location = './index.php';
    }
    
    $('#sEnv').click(function(){
        currentUser = undefined;
        hide('envCont');
        mgrFunc.getEnvVars();
        mgrFunc.getUserVars(undefined, 'studentContTable');
    });
    $('#sStudents, .back').click(function(){
        hide('studentsCont');
        mgrFunc.getStudentTable(undefined, 'studentTable');
    });
    
    function seeStats(user){
        currentUser = user;
        hide('singleStudnet');
        mgrFunc.getStudentTable(user, 'studentStats');
        mgrFunc.getUserVars(user, 'studentSettings');
        mgrFunc.getStudentStats(user, 'DetailedStatsT');
    }
    
    function removeUser(username){
        mgrFunc.removeUser(username);
    }
    
    var mgrFunc = {
    removeUser: function(username){
        $.ajax({
            data:{
                func: 'daccount',
                password: pass,
                user: username
            },
            success:function(){
                hide('studentsCont');
                mgrFunc.getStudentTable(undefined, 'studentTable');
            }
        });
    },
    getStudentTable: function (username, table){
        $.ajax({
            data:{
                func: 'studentTable',
                password: pass,
                user: username||""
            },
            success:function(data){
                $('#studentInfo').remove();
                $('#' + table).append(data);
                hcenter();
            }
        })
    },
    getStudentStats: function(username, table){
        $.ajax({
            data: {
                func: 'getStudentStats',
                user: username,
                password: pass
            },
            success: function(data){
                $('#' + table).remove();
                $('#DetailedStats').prepend(data);
                hcenter();
            }
        });
    },
    getEnvVars: function (){
        $.ajax({
            data: {
                func: 'getEnvVars',
                password: pass
            },
            success: function(data){
                $('#envTable').remove();
                $('#envContTable').prepend(data);
                $('form').submit(prevent);
                $('#saveEnv').click(function(){mgrFunc.save('setEnvVars', 'envTable');});
                $('#resetEnvVars').click(function(){mgrFunc.resetData('resetEnvVars', 'envTable');});
                hcenter();
            }
        });
    },
    getUserVars: function (username, table){
        $.ajax({
            data: {
                func: 'getUserVars',
                user: username||"",
                password: pass,
                table: 1
            },
            success:function(data){
                $('#studentVars').remove();
                $('#' + table).append(data);
                $('form').submit(prevent);
                $('#saveUser').click(function(){mgrFunc.save('setUserVars', table, currentUser);});
                $('#resetVars').click(function(){mgrFunc.resetData('resetVars', table, currentUser);});
                hcenter();
            }
        });
    },
    save: function (type, table, username){
        var a = {
            func: type,
            password: pass,
            user: username||""
        };
        $('#' + table + " input").each(function (index,call){
            a[call.id] = $(call).val();
        });
        $.ajax({
            data: a,
            success: function(data){
                mgrFunc[data](username, table);
            }
        });
    },
    resetData: function (type, table, username){
        $.ajax({
            data:{
                func: type,
                user: username||"",
                password: pass
            },
            success: function(data){
                mgrFunc[data](username, table);
            }
        });
    }
    }
    setTimeout(reload,600000); //Reload the page to the password screen after a set time
    function reload(){
        location = location.href;
    }
</script>
</html>