$(document).ready(function(){

	// === Sidebar Navigation — Modern sidebar with .collapsed state ===

	// Submenu click handler — works in both expanded and collapsed states
	$('.submenu > a').click(function(e)
	{
		e.preventDefault();
		var $link = $(this);
		var $li = $link.closest('li');
		var $submenu = $link.siblings('ul').first();
		var isNested = $li.parent().closest('.submenu').length > 0;

		// In collapsed state, expand the sidebar first
		if ($('#sidebar').hasClass('collapsed')) {
			$('#sidebar').removeClass('collapsed');
			document.body.classList.remove('sidebar-collapsed');
			localStorage.setItem('sidebar-collapsed', 'false');
		}

		if($li.hasClass('open'))
		{
			// Close this submenu
			$submenu.slideUp(200);
			$li.removeClass('open');
			// Also close any open child submenus
			$li.find('li.submenu.open').removeClass('open').find('> ul').slideUp(200);
		} else
		{
			// Close sibling submenus at the same level
			if (!isNested) {
				var $siblings = $li.siblings('li.submenu.open');
				$siblings.removeClass('open').find('> ul').slideUp(200);
				$siblings.find('li.submenu.open').removeClass('open').find('> ul').slideUp(200);
			} else {
				var $parentUl = $li.parent('ul');
				var $siblings = $parentUl.children('li.submenu.open');
				$siblings.not($li).removeClass('open').find('> ul').slideUp(200);
			}

			// Open this submenu
			$submenu.slideDown(200);
			$li.addClass('open');
		}
	});

	// Desktop collapse/expand toggle
	$(document).on('click', '.sidebar-collapse-toggle', function(e) {
		e.preventDefault();
		e.stopPropagation();
		toggleSidebar();
	});

	// Mobile toggle (topbar hamburger)
	$(document).on('click', '#sidebar-toggle-mobile, #sidebar-toggle-mobile-admin', function(e) {
		e.preventDefault();
		toggleSidebar();
	});

	// Toggle sidebar state
	function toggleSidebar() {
		var sidebar = document.getElementById('sidebar');
		if (!sidebar) return;

		var isCollapsing = !sidebar.classList.contains('collapsed');

		if (isCollapsing) {
			sidebar.classList.add('collapsed');
			document.body.classList.add('sidebar-collapsed');
			// Close all open submenus when collapsing
			$('#sidebar li.submenu.open').removeClass('open');
			$('#sidebar li.submenu > ul').slideUp(0);
		} else {
			sidebar.classList.remove('collapsed');
			document.body.classList.remove('sidebar-collapsed');
		}

		localStorage.setItem('sidebar-collapsed', isCollapsing ? 'true' : 'false');
	}

	// Initialize sidebar state from localStorage
	var sidebarEl = document.getElementById('sidebar');
	if (sidebarEl) {
		var savedCollapsed = localStorage.getItem('sidebar-collapsed');
		if (savedCollapsed === 'true') {
			sidebarEl.classList.add('collapsed');
			document.body.classList.add('sidebar-collapsed');
		}

		// MutationObserver to sync body class if sidebar class changes externally
		var observer = new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				if (mutation.attributeName === 'class') {
					var sidebar = document.getElementById('sidebar');
					if (sidebar && sidebar.classList.contains('collapsed')) {
						document.body.classList.add('sidebar-collapsed');
					} else {
						document.body.classList.remove('sidebar-collapsed');
					}
				}
			});
		});
		observer.observe(sidebarEl, { attributes: true });
	}

	// === Resize window === //
	$(window).resize(function()
	{
		if($(window).width() > 479)
		{
			$('#content-header .btn-group').css({width:'auto'});
		}
		if($(window).width() < 479)
		{
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
		fix_position();
	}

	if($(window).width() > 479)
	{
		$('#content-header .btn-group').css({width:'auto'});
	}

	// === Tooltips === //
	$('.tip').tooltip();
	$('.tip-left').tooltip({ placement: 'left' });
	$('.tip-right').tooltip({ placement: 'right' });
	$('.tip-top').tooltip({ placement: 'top' });
	$('.tip-bottom').tooltip({ placement: 'bottom' });

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
			$('#imgbox').html('<img src="' + image_href +'" /><p><i class="icon-remove icon-white"></i></p>');
			$('#lightbox').slideDown(500);
		} else {
			var lightbox =
			'<div id="lightbox" style="display:none;">' +
				'<div id="imgbox"><img src="' + image_href +'" />' +
					'<p><i class="icon-remove icon-white"></i></p>' +
				'</div>' +
			'</div>';
			$('body').append(lightbox);
			$('#lightbox').slideDown(500);
		}

		$(document).on('click', '#lightbox', function() {
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