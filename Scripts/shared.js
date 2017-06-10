$.ajaxSetup({
    url: './Scripts/mgr.php',
    method: 'POST',
    statusCode: {
        400: function(data) {
            errorList(data.responseText);
            console.log(data);
        }
    }
});

window.onbeforeunload = function() { return "This action will leave the site"; };

//visual elements
function hide(show){
    $('.content').toggle(false);
    if(show !== undefined)
        $('#' + show).toggle(true);
    hcenter();
}
function hcenter(){
    $('.hcenter').each(function(){
        $(this).css('position', 'relative');
        $(this).css('left', parseInt($('body').css('width'),10)/2 - parseInt($(this).css('width'), 10)/2 + 'px');
    });
    $('.wcenter').each(function(){
        $(this).css('position', 'relative');
        $(this).css('left', parseInt($(this.parentElement).css('width'),10)/2 - parseInt($(this).css('width'), 10)/2 + 'px');
    });
}

//Resize the page
$(window).on('load resize', function(){//Page load function
    hcenter();
    $(".resized").width(parseInt($('body').css('width'),10) * .9);
});

//Prevent page reloads
$("form, .nolink").submit(prevent); 
function prevent(e){
    e.preventDefault();
}

//errors
$('#errorboxContainer').toggle(false);
var errorTimeLoop = undefined;
function errorList(rawError){
    switch (rawError){
        case 'e0':
            error('That username is already taken');
            break;
        case 'e1':
            error('Username or password is incorrect');
            logout();
            break;
        case 'e2':
            error('Accounts cannot be created at this time');
            break;
        case 'e4':
            error("Please verify your email then sign in again. <a href='#' class='nolink' id='resend'>Resend Verification Email</a>");
            $('#resend').click(function(){
                $.ajax({
                    data:{
                        func: 'sEmail',
                        user: $('#username').val()
                    }
                });
            });
            break;
        case 'e5':
            error('Not all feilds properly submitted');
            break;
        case 'e7':
            console.log('An e7 error has occured. Verification Emails are not being sent!');
            break;
        case 'e9':
            error('An account with this email address already exists')
            break;
        default:
            error('An unknown error has occured! Error Code: ' + rawError);
            break;
    }
}
function error(text){
    $('#inputerror').html(text);
    errorTimeLoop = clearInterval(errorTimeLoop);
    errorTimeLoop = setInterval(function(){
        $('#errorboxContainer').css('top', -1*parseInt($('#content').css('height'),10)-5 + 'px');
        $('#errorboxContainer').toggle(true);
        $('html').css('overflow-y', 'hidden');
        window.scrollTo(0,0);
        hcenter();
    }, 100);
    
}
$('#errorok').on('click', function(){
    clearInterval(errorTimeLoop);
    $('html').css('overflow-y', 'visible');
    $('#errorboxContainer').toggle(false);
});