
$(document).ready(function(){

	
	
	// === Sidebar navigation === //
	
	// === Sidebar navigation - Suporte a submenus aninhados === //

	$('.submenu > a').click(function(e)
	{
		e.preventDefault();
		var $link = $(this);
		var $li = $link.closest('li');
		var $submenu = $link.siblings('ul');
		var isNested = $li.parent().closest('.submenu').length > 0;

		if($li.hasClass('open'))
		{
			// Fecha o submenu atual
			if(($(window).width() > 768) || ($(window).width() < 479)) {
				$submenu.slideUp();
			} else {
				$submenu.fadeOut(250);
			}
			$li.removeClass('open');
			// Fecha todos os submenus filhos também
			$li.find('li.submenu').removeClass('open').find('> ul').slideUp();
		} else
		{
			// Se não for submenu aninhado, fecha outros submenus no mesmo nível
			if (!isNested) {
				var $siblings = $li.siblings('li.submenu');
				$siblings.removeClass('open').find('> ul').slideUp();
				$siblings.find('li.submenu').removeClass('open').find('> ul').slideUp();
			} else {
				// Se for submenu aninhado, fecha apenas os irmãos no mesmo nível
				var $parentUl = $li.parent('ul');
				var $siblings = $parentUl.children('li.submenu');
				$siblings.not($li).removeClass('open').find('> ul').slideUp();
			}

			// Abre o submenu atual
			if(($(window).width() > 768) || ($(window).width() < 479)) {
				$submenu.slideDown();
			} else {
				$submenu.fadeIn(250);
			}
			$li.addClass('open');
		}
	});
	
	var ul = $('#sidebar > ul');
	
	$('#sidebar > a').click(function(e)
	{
		e.preventDefault();
		var sidebar = $('#sidebar');
		if(sidebar.hasClass('open'))
		{
			sidebar.removeClass('open');
			ul.slideUp(250);
		} else 
		{
			sidebar.addClass('open');
			ul.slideDown(250);
		}
	});
	
	// === Resize window related === //
	$(window).resize(function()
	{
		if($(window).width() > 479)
		{
			ul.css({'display':'block'});	
			$('#content-header .btn-group').css({width:'auto'});		
		}
		if($(window).width() < 479)
		{
			ul.css({'display':'none'});
			fix_position();
		}
		if($(window).width() > 768)
		{
			$('#user-nav > ul').css({width:'auto',margin:'0'});
            $('#content-header .btn-group').css({width:'auto'});
		}
	});
	
	if($(window).width() < 468)
	{
		ul.css({'display':'none'});
		fix_position();
	}
	
	if($(window).width() > 479)
	{
	   $('#content-header .btn-group').css({width:'auto'});
		ul.css({'display':'block'});
	}
	
	// === Tooltips === //
	$('.tip').tooltip();	
	$('.tip-left').tooltip({ placement: 'left' });	
	$('.tip-right').tooltip({ placement: 'right' });	
	$('.tip-top').tooltip({ placement: 'top' });	
	$('.tip-bottom').tooltip({ placement: 'bottom' });	
	
	// === Search input typeahead === //
	//$('#search input[type=text]').typeahead({
		//source: ['Dashboard','Form elements','Common Elements','Validation','Wizard','Buttons','Icons','Interface elements','Support','Calendar','Gallery','Reports','Charts','Graphs','Widgets'],
		//items: 4
	//});
	
	// === Fixes the position of buttons group in content header and top user navigation === //
	function fix_position()
	{
		var uwidth = $('#user-nav > ul').width();
		$('#user-nav > ul').css({width:uwidth,'margin-left':'-' + uwidth / 2 + 'px'});
        
        var cwidth = $('#content-header .btn-group').width();
        $('#content-header .btn-group').css({width:cwidth,'margin-left':'-' + uwidth / 2 + 'px'});
	}
	
	// === Style switcher === //
	$('#style-switcher i').click(function()
	{
		if($(this).hasClass('open'))
		{
			$(this).parent().animate({marginRight:'-=190'});
			$(this).removeClass('open');
		} else 
		{
			$(this).parent().animate({marginRight:'+=190'});
			$(this).addClass('open');
		}
		$(this).toggleClass('icon-arrow-left');
		$(this).toggleClass('icon-arrow-right');
	});
	
	$('#style-switcher a').click(function()
	{
		var style = $(this).attr('href').replace('#','');
		$('.skin-color').attr('href','css/maruti.'+style+'.css');
		$(this).siblings('a').css({'border-color':'transparent'});
		$(this).css({'border-color':'#aaaaaa'});
	});
	
	$('.lightbox_trigger').click(function(e) {
		
		e.preventDefault();
		
		var image_href = $(this).attr("href");
		
		if ($('#lightbox').length > 0) {
			
			$('#imgbox').html('<img src="' + image_href + '" /><p><i class="icon-remove icon-white"></i></p>');
		   	
			$('#lightbox').slideDown(500);
		}
		
		else { 
			var lightbox = 
			'<div id="lightbox" style="display:none;">' +
				'<div id="imgbox"><img src="' + image_href +'" />' + 
					'<p><i class="icon-remove icon-white"></i></p>' +
				'</div>' +	
			'</div>';
				
			$('body').append(lightbox);
			$('#lightbox').slideDown(500);
		}

		$('#lightbox').live('click', function() {
			$('#lightbox').hide(200);
		});

	});

	// === Salvar e Restaurar posição do scroll do menu lateral === //
	var menuScrollable = $('.menu-scrollable');

	// Restaurar posição do scroll ao carregar a página
	var savedScrollPos = localStorage.getItem('menuScrollPosition');
	if (savedScrollPos && menuScrollable.length) {
		menuScrollable.scrollTop(parseInt(savedScrollPos));
	}

	// Salvar posição do scroll antes de navegar para outra página
	$(document).on('click', '.menu-scrollable a, .menu-links a', function(e) {
		if (menuScrollable.length) {
			localStorage.setItem('menuScrollPosition', menuScrollable.scrollTop());
		}
	});

});
</script>

