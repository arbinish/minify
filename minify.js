var width = $(window).width();
var height = $(window).height();
var mainWidth = Math.floor(width * 0.3);
var mainHeight = Math.floor(height * .7);

setupFeedback = function(email, bgColor) {
	var ele = $('<a href="mailto:' + email + '">Feedback</a>').css(
		{	
			'position': 'fixed',
			'bottom': 0,
			'right': 0,
			'padding': '0.7em 3em',
			'text-decoration': 'none',
			'font-weight': 'bold',
			'background-color': bgColor,
			'font-size': '1.4em',
			'color': 'black',
			'border-radius': '4px',
			'display':'none'
		}
	);
	$('body').append(ele);
	ele.fadeIn('slow');
}

setupMainContent = function() {
    var textareaHeight = Math.floor(mainHeight * 0.6);
    var textareaWidth = Math.floor(mainWidth * 0.7);
    $('#main').css('width', mainWidth);
    $('#main').css('height', mainHeight);
    $('#hostlist').css('width', mainWidth);
    $('#hostlist').css('height', mainHeight);
    console.log('ta height: ' + textareaHeight);
    console.log('ta width: ' + textareaWidth);
}

setupButton = function() {
        var cw = $('#content').width();
        var bw = $('#btn_cmprs').width();
        var posLeft = Math.floor((cw - 3*bw - bw/2)/2);
        $('#btn_clear').css({'position': 'relative', 'left': posLeft});
        console.log('set up clear button on left: ' + posLeft);
}
setupCloseAction =  function() {
    $('.close').click(function(e) {
        e.preventDefault();
        $(this).fadeOut('slow');
        $('#result').fadeOut('slow');
   });
}

$(document).ready(function(e) {
    $('#result').hide();
    setupMainContent();
	setupFeedback('arbinish@gmail.com', '#BABAFF');
   $('#content #hostlist').focus();
   $('#hostlist').focusin(function(e) {
        $(this).addClass('active');
   });
   $('#hostlist').focusout(function(e) {
        $(this).removeClass('active');
   });
   $('#hostlist').click(function(e){
        $(this).text('');
        $('#result').fadeOut('slow');
        $('#result').remove();
        $('img.close').remove();
   });
   $('#buttons a').click(function(e) {
        e.preventDefault();
        var action = $(this).text();
        $('#result').remove();
        $('img.close').remove();

        if (action.toLowerCase() == "clear") {
            $('#hostlist').val(' ');
            $('#hostlist').focus();
            return false;
        }
        var hostEntry = $('#hostlist').val().trim();
        if (! hostEntry) { 
                $('#hostlist').val('Empty TextBox! Enter a range expression, one host per line');
                return;
        }
        var data = { action: action, hostlist: hostEntry };
        console.log('pressed ' + action + 'with data '); console.dir(data);
        $.post(document.URL, data, function(retdata) {
            console.log('returned ' + retdata);
            console.log('first = ' + retdata[0]);
            var outString = '';
            $.each(retdata, function(index, value) {
               outString += value + '<br>'; 
            });
            $('<div id="result"></div>').insertAfter('#content');
            $('#result').html(outString);
            var resultHeight = $('#result').height();
            console.log('result height is ' +  resultHeight);
            if (resultHeight > mainHeight) {
                console.log('setting result window height from ' + resultHeight + ' to ' + mainHeight);
                $('#result').css('overflow', 'scroll');
                resultHeight = mainHeight;
                $('#result').height(mainHeight);
            } else {
                $('#result').css('overflow', 'visible');
            }
            if (action.toLowerCase() == "expand") {
                $('#result').css('overflow-y', 'auto');
            }
            var resultTop = Math.floor(height/2) - Math.floor(resultHeight/2);
            var resultWidth = $('#result').width();
            var resultLeft = Math.floor(width/2) - Math.floor(resultWidth/2);
            $('#result').css( {top: resultTop, left: resultLeft});
            $('<img class="close" src="close-button.png" />').insertBefore('#result');
            $('img.close').css({'top': resultTop-10, 'left': resultLeft-16 });
            $('#result').fadeIn();
            setupCloseAction();
        }, "json");
   });
    setupButton();
});
