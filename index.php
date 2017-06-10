<!DOCTYPE html>
<html>
    <head>
        <title>Arithmetic Practice</title>
        <meta name="author" content="Nicholas Chieppa">
        <meta name="description" content="Practice">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha1.js"></script>
        <link rel="icon" href="./icon.ico"/>
        <meta name=viewport content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <link rel=stylesheet type="text/css" href="./styles/style.css"></link>
        <link rel=stylesheet type="text/css" href="./styles/button.css"></link>
    </head>
    <body>
        <div id=header class=edge>
            <!--Header-->
            <table id=CommandBar class='left vcenter'>
            <tr>
                    <td id='home' onclick='home()'>Home🏠</td>
                </tr>
            </table>
            <table id='UserDisplay' class='right vcenter'>
                <tr>
                    <td id=displayName></td>
                    <td id=logout onclick='logout()'>Logout</td>
                </tr>
            </table>
            <table id='loginButtons' class='right vcenter'>
                <tr>
                    <td id=login>Login🚪</td>
                    <td id=register>Register👤</td>
                </tr>
            </table>
        </div>
        <div class="spacer">
            &nbsp;
        </div>
        <div id=content>
            <!--Body-->
            <div id="announcement" class='hcenter resized' style=display:none><br><a id=hideA class=right>[hide this message]</a></div>
            <form action='#' method=POST>
                <table id=ExistingUser class='hcenter content infoTable'>
                    <tr>
                        <th>Sign in:</th>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id='username' placeholder='Username' required/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="password" id='password' placeholder='Password' autocomplete="current-password" required/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button id=signin required>Login</button>
                            <!--<button id=remember type='button'>Remember</button>-->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href=# class='nolink noAccount'>Don't Have an account? Click Here!</a>
                        </td>
                    </tr>
                </table>
            </form>
            <form action='#' method=POST>
                <table id=new-user class='hcenter content infoTable'>
                    <tr>
                        <th>Register:</th>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id=regUser placeholder='Username' required/>
                            <input type="text" id="email" placeholder='Email' required/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="password" id="regPass" placeholder='Password' name=password autocomplete="new-password" required/>
                            <input type="password" id="regPassR" placeholder='Re-Enter Password' name=password autocomplete="new-password" required/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id="firstName" placeholder='First Name' required/>
                            <input type="text" id="lastName" placeholder='Last Name' required/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button id=signup>Sign up</button>
                        </td>
                    </tr>
                    <tr>
                        <td><a href=# class='nolink yesAccount'>Have an account? Click Here!</a></td>
                    </tr>
                </table>
            </form>
            <div id=Selection class='hcenter content'>
                <h2 class='error accountWarning'>Warning you are not <a class='yesAccount' style='text-decoration: underline;'>signed in</a> to an account!</h2>
                <h2>Make your selection:</h2>
                <table>
                    <div id=sRandom class='selectBoxes'>Random</div>
                    <div id=sAddition class='selectBoxes'>Addition</div>
                    <div id=sSubtraction class='selectBoxes'>Subtraction</div>
                    <div id=sMultiplaction class='selectBoxes'>Multiplication</div>
                    <div id=sDivision class='selectBoxes'>Division</div>
                </table>
            </div>
            <div id=Problem class='hcenter content'>
                <h2 class='error accountWarning wcenter'>Warning you are not <a class='yesAccount' style='text-decoration: underline;'>signed in</a> to an account!</h2>
                <div class=wcenter>
                    <h3>Simplify Completely</h3>
                    <div class='number'>
                        <div id='a0a0'>1</div>
                        <div id='a0a1'>1</div>
                    </div>
                    <div class='number' id=sign>+</div>
                    <div class='number'>
                        <div id='a1a0'>1</div>
                        <div id='a1a1'></div>
                    </div>
                    <div class=number>=</div>
                    <div class='number'>
                        <form action='#' method='POST' id='solutionBox'>
                            <div><input id='a2a0' type="number" max='9999' required/></div>
                            <div><input id='a2a1' type="number" max='9999'/></div>
                        </form>
                    </div>
                </div><br>
                <div class=wcenter>
                    <button id=answer form='solutionBox'>Submit Response</button>
                    <!--<button id=Give title='counted as incorrect'>Give Up</button>-->
                    <button id=next>Next Question</button>
                </div><br>
                <div class='wcenter number' id=answerBox>
                    <div>answer:</div>
                    <div class='number'>
                        <div id='a3a0'>1</div>
                        <div id='a3a1'>1</div>
                    </div>
                </div>
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
    <script type="text/javascript"> var $;
        
        //On load
        var announcement = "", aTimer = setInterval(getAnnouncment, 300000);
        $(window).on('load', function(){
            signInValidate(true, true);
            getAnnouncment();
        });
        function getAnnouncment(){
            $.ajax({
                data:{
                    func: 'getAnnouncement'
                },
                success: function(data){
                    if(data == "")
                        $("#announcement").toggle(false);
                    else if(announcement != data){
                        $("#announcement code").remove();
                        $("#announcement").prepend("<code>" + data + "</code>");
                        $("#announcement").toggle(true);
                        hcenter();
                    }
                    announcement = data;
                }
            });
        }
        
        //Switch account menus
        $('.noAccount, #register').on('click', function(){
            hide('new-user');
        });
        $('.yesAccount, #login').on('click', function(){
            hide('ExistingUser');
        });
        $('#remember').on('click', function(){
            if($('#remember').css('background-color') != 'rgb(0, 128, 0)')
                $('#remember').css('background-color', 'rgb(0, 128, 0)');
            else
                $('#remember').css('background-color', 'rgb(221, 221, 221)');
        });
        $('#hideA').click(function(){
            $('#announcement').toggle(false);
        });
        
        function home(){
            hide('Selection');
        }
        
        //Form vaildation
        $('#email').blur(function(){
            if($('#email').val() != '' && $('#email').val().indexOf('@') == -1)
                $('#email').css('background-color', '#ff7c7c');
            else
                $('#email').css('background-color', 'white');
        });
        $('#regPass, #regPassR').blur(function(){
            if($('#regPassR').val() != "" && $('#regPass').val() != ""){
                if($('#regPassR').val() != $('#regPass').val()){
                    $('#regPass, #regPassR').css('background-color', 'white');
                    $(this).css('background-color', '#ff7c7c');
                }
                else{
                    $('#regPass, #regPassR').css('background-color', 'white');
                }
            }
            else
                $('#regPass, #regPassR').css('background-color', 'white');
        });
        $('#regPass, #regPassR, #email, #solutionBox div input').focus(function(){
            $('#regPass, #regPassR').css('background-color', 'white');
            $(this).css('background-color', '#ffecb2');
        });
        
        //Account Control
        $('#signup').click(function(){
            if($('#regPass').val() != $('#regPassR').val()){
                error('Password feilds do not match')
                return;
            }
            if($('#regPass').val().length < 8){
                error('Password Should be at least 8 Charaters long');
                return;
            }
            if($('#email').val().lastIndexOf('@') == -1){
                error('Invalid mail address');
                return;
            }
            if($('#username').val().length > 25){
                error('Username is too long');
                return;
            }
            // Send random data to client from server -> generate more random data on client -> concat and hash password -> send random data + hashed password back to server
            // $('#new-user').prop('disabled');
            $.ajax({//Get a random number from the server
                data:{
                    func: 'hsalt'
                },
                success: function(data){
                    var ran = Math.floor(Math.random()*999)+500;
                    $.ajax({
                        data:{
                            func: 'reg',
                            user: $('#regUser').val(),
                            password: CryptoJS.SHA1(ran.toString() + data.toString() + $('#regPass').val()).toString(),
                            fullhash: ran.toString() + data.toString(),
                            firstName: $('#firstName').val(),
                            lastName: $('#lastName').val(),
                            email: $('#email').val()
                        },
                        complete: function(textStatus){
                            if(textStatus.status == 200 || textStatus.responseText == 'e7'){
                                $('#username').val($('#regUser').val());
                                $('#password').val($('#regPass').val());
                                login();
                            }
                        }
                    });
                }
            });
        });
        $('#signin').click(function(){
            login();
        });
        function login(){
            if($('#username').val()=="" || $('#password').val()=="")
                return;
            $.ajax({//Request hash
                data:{
                    func: 'rsalt',
                    user: $('#username').val()
                },
                success: function(data){
                    document.cookie = 'username=' + $('#username').val();
                    document.cookie = 'password=' + CryptoJS.SHA1(data.toString() + $('#password').val().toString()).toString();
                    signInValidate(true);
                }
            });
        }
        function signInValidate(hideSignIn, pageLoad){
            if(getCookie('username') == "")
                return hide('ExistingUser');
            $.ajax({
                data:{
                    func: 'signIn',
                    user: getCookie('username'),
                    password: getCookie('password')
                },
                complete: function(textStatus){
                    if(textStatus.responseText != 'success'){
                        logout();
                        if(pageLoad != undefined)
                            $('#errorboxContainer').toggle(false);
                        return false;
                    }
                    else{//Successfull signin
                        $('#displayName').html(getCookie('username'));
                        $('#loginButtons').toggle(false);
                        $('#UserDisplay').toggle(true);
                        $('.accountWarning').toggle(false);
                    }
                    if(hideSignIn){
                        hide('Selection');
                        $('#errorboxContainer').toggle(false);
                    }
                    return true;
                }
            });
        }
        function logout(){
            $('input').val("");
            delete_cookie('username');
            delete_cookie('password');
            $('#loginButtons').toggle(true);
            $('#UserDisplay').toggle(false);
            $('.accountWarning').toggle(true);
            hide('ExistingUser');
        }
        
        function delete_cookie(name){
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }
        function getCookie(cname){
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
        
        //Problem generation code
        var selection = Array(2);
        var problemTypes = {
            'sRandom': 0,
            'sAddition': 1,
            'sSubtraction': 2,
            'sMultiplaction': 3,
            'sDivision': 4
        };
        var signs = {
            1: '+',
            2: '-',
            3: '*',
            4: '÷'
        };
        var vals = {
                min: 1,
                max: 25,
                neg: .2,
                frac: .1
            };
        var chosen;
        var openingTime; 
        
        $('.selectBoxes').click(function(){
            selection[0] = problemTypes[$(this)[0].id];
            $.ajax({
                data: {
                    func: 'getUserVars',
                    user: getCookie('username'),
                    table: 2
                },
                success: function(data){
                    vals = JSON.parse(data);
                },
                complete: function(){
                    newProblem();
                }
            });
            hide('Problem');
        });
        $('#answer').click(function(){
            if(!isNaN(parseInt($('#a2a0').val()))){
                $('#next').toggle(true);
                $('#next').focus();
                $('#answer').toggle(false);
                var anws = Array(2), s = selection[1];
                switch (selection[1]){
                    case 1: case 2:
                        chosen[0][0] *= chosen[1][1];
                        chosen[1][0] *= chosen[0][1];
                        anws[0] = solution([chosen[0][0], chosen[1][0]]);
                        anws[1] = chosen[0][1] * chosen[1][1];
                        break;
                    case 4:
                        var temp = chosen[1][1];
                        chosen[1][1] = chosen[1][0];
                        chosen[1][0] = temp;
                        selection[1] = 3;
                    case 3: 
                        anws[0] = solution([chosen[0][0], chosen[1][0]]);
                        anws[1] = solution([chosen[0][1], chosen[1][1]]);
                        break;
                    default:
                        error('Something went wrong!');
                        return;
                }
                selection[1] = s;
                anws = simple(anws);
                var submitted = [$('#a2a0').val(), (isNaN(parseInt($('#a2a1').val())))?1:parseInt($('#a2a1').val())];
                wprob(anws, 3);
                if([Math.abs(anws[0])*Neg(anws),Math.abs(anws[1])].join() == [Math.abs(submitted[0])*Neg(submitted),Math.abs(submitted[1])].join()){
                    //correct
                    $('#answerBox').css('color', '#2f9407');
                    report(1);
                }
                else{
                    //incorrect
                    $('#answerBox').css('color', '#dc1919');
                    report(0);
                }
                $('#answer').toggle(false);
                $('#next').toggle(true);
                $('#answerBox').toggle(true);
                hcenter();
            }
        });
        $('#next').click(function(){
            newProblem();
        });
        
        function newProblem(){//Generates a new problem
            selection[1] = selection[0]||randomChoice(0,4);
            chosen = Array(genNumber(), genNumber());
            $('#answer').toggle(true);
            $('#answerBox').toggle(false);
            $('#sign').html(signs[selection[1]]);
            $('#next').toggle(false);
            $('#a2a1').toggle(false);
            $('#a2a0').focus();
            $('#a2a0').val("");
            $('#a2a1').val("");
            $('.number div').css('border-top', 'none');
            $('.number div').css('border-bottom', 'none');
            chosen.forEach(function(content, index){
                wprob(content, index);
            });
            if(selection[1] == 4)
                $('#a2a1').toggle(true);
            openingTime = performance.now();
            hcenter();
        }
        function wprob(content, index){//Writes the fractions/numbers
            $('#a' + index + 'a0').html(content[0]);
            if(content[1] != 1){
                $('#a' + index + 'a1').html(content[1]);
                $('#a2a1').toggle(true);
            }
            else
                $('#a' + index + 'a1').html("");
            if(Math.abs(content[0]) > Math.abs(content[1]) && content[1] != 1)
                $('#a' + index + 'a0').css('border-bottom', '1px solid black');
            else
                $('#a' + index + 'a1').css('border-top', '1px solid black');
        }
        
        function genNumber(){//Generates a random number
            var n = Array(1,1);
            n[0] = randomChoice(vals.min, vals.max); n[0] *= (prop(vals.neg))?-1:1;
            if(prop(vals.frac)){
                n[1] = randomChoice(vals.min||1, vals.max); n[1] *= (prop(vals.neg))?-1:1;
            }
            return n;
        }
        function randomChoice(min, max){
            return Math.floor(Math.random() * parseInt(max)) + parseInt(min);
        }
        function prop(chance){
            return Math.random() <= parseFloat(chance);
        }
        
        function Neg(num){//Decides if the fraction is negitive
            if(num[0]/num[1]>0)
                return 1;
            return -1;
        }
        function gcd(x, y){//finds the greatest common demoninator of 2 numbers
            if(x % y)
                return gcd(y, x % y);
            return y;
        }
        function simple(number){
            var l = gcd(number[0], number[1]);
            return [number[0]/l, number[1]/l];
        }
        function solution(submit){//preforms an opperation depending on the problem type
            var answer;
            switch (selection[1]){
                case 1:
                    answer = submit[0] + submit[1];
                    break;
                case 2:
                    answer = submit[0] - submit[1];
                    break;
                case 3:
                    answer = submit[0] * submit[1];
                    break;
                case 4:
                    answer = submit[0] / submit[1];
                    break;
                default:
                    error('Something went wrong!')
                    break;
            }
            return answer;
        }
        
        function report(state){
            if(getCookie('username') == "")
                return;
            $.ajax({
                data:{
                    func: 'prob',
                    user: getCookie('username'),
                    password: getCookie('password'),
                    correct: state, 
                    type: selection[1],
                    time: performance.now() - openingTime
                },
                success: function (data){
                    console.log(data);
                }
            });
        }
        
    </script>
</html>