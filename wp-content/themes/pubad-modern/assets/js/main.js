(function () {
	'use strict';

	var root = document.documentElement;
	var currentAdjust = 0;

	document.querySelectorAll('[data-font]').forEach(function (button) {
		button.addEventListener('click', function () {
			var action = button.getAttribute('data-font');
			if (action === 'up') {
				currentAdjust = Math.min(currentAdjust + 1, 3);
			} else if (action === 'down') {
				currentAdjust = Math.max(currentAdjust - 1, -3);
			} else {
				currentAdjust = 0;
			}
			root.style.setProperty('--font-size-adjust', currentAdjust + 'px');
		});
	});

	var toggle = document.querySelector('.menu-toggle');
	var menu = document.querySelector('.primary-menu');
	if (toggle && menu) {
		toggle.addEventListener('click', function () {
			var isOpen = menu.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			window.setTimeout(updateFixedNav, 0);
		});
	}

	var mainNav = document.querySelector('.main-nav');
	var navOffset = 0;

	function measureFixedNav() {
		if (!mainNav) {
			return;
		}

		mainNav.classList.remove('is-fixed');
		document.body.classList.remove('nav-is-fixed');
		navOffset = mainNav.getBoundingClientRect().top + window.pageYOffset;
		updateFixedNav();
	}

	function updateFixedNav() {
		if (!mainNav) {
			return;
		}

		if (window.pageYOffset >= navOffset) {
			mainNav.classList.add('is-fixed');
			document.body.classList.add('nav-is-fixed');
			document.body.style.setProperty('--fixed-nav-height', mainNav.offsetHeight + 'px');
		} else {
			mainNav.classList.remove('is-fixed');
			document.body.classList.remove('nav-is-fixed');
			document.body.style.removeProperty('--fixed-nav-height');
		}
	}

	if (mainNav) {
		window.addEventListener('scroll', updateFixedNav, { passive: true });
		window.addEventListener('resize', measureFixedNav);
		measureFixedNav();
	}

	var slides = Array.prototype.slice.call(document.querySelectorAll('.hero-slide'));
	var copies = Array.prototype.slice.call(document.querySelectorAll('[data-slide-copy]'));
	var dots = Array.prototype.slice.call(document.querySelectorAll('[data-slide-target]'));
	var prev = document.querySelector('.slider-arrow--prev');
	var next = document.querySelector('.slider-arrow--next');
	var activeSlide = 0;
	var autoplay;

	function showSlide(index) {
		if (!slides.length) {
			return;
		}

		activeSlide = (index + slides.length) % slides.length;
		slides.forEach(function (slide, slideIndex) {
			slide.classList.toggle('is-active', slideIndex === activeSlide);
		});
		copies.forEach(function (copy, copyIndex) {
			copy.classList.toggle('is-active', copyIndex === activeSlide);
		});
		dots.forEach(function (dot, dotIndex) {
			dot.classList.toggle('is-active', dotIndex === activeSlide);
		});
	}

	function restartAutoplay() {
		window.clearInterval(autoplay);
		if (slides.length > 1) {
			autoplay = window.setInterval(function () {
				showSlide(activeSlide + 1);
			}, 6500);
		}
	}

	dots.forEach(function (dot) {
		dot.addEventListener('click', function () {
			showSlide(parseInt(dot.getAttribute('data-slide-target'), 10) || 0);
			restartAutoplay();
		});
	});

	if (prev) {
		prev.addEventListener('click', function () {
			showSlide(activeSlide - 1);
			restartAutoplay();
		});
	}

	if (next) {
		next.addEventListener('click', function () {
			showSlide(activeSlide + 1);
			restartAutoplay();
		});
	}

	restartAutoplay();

	var chat = document.querySelector('[data-ai-chat]');
	if (chat) {
		var launcher = chat.querySelector('.ai-assistant');
		var panel = chat.querySelector('.ai-chat__panel');
		var close = chat.querySelector('.ai-chat__close');
		var messages = chat.querySelector('.ai-chat__messages');
		var form = chat.querySelector('.ai-chat__form');
		var input = chat.querySelector('.ai-chat__form input');
		var suggestions = Array.prototype.slice.call(chat.querySelectorAll('.ai-chat__suggestions button'));

		function setChatOpen(isOpen) {
			panel.hidden = !isOpen;
			launcher.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			if (isOpen) {
				window.setTimeout(function () {
					input.focus();
				}, 50);
			}
		}

		function appendMessage(text, type) {
			var message = document.createElement('div');
			message.className = 'ai-message ai-message--' + type;
			message.textContent = text;
			messages.appendChild(message);
			messages.scrollTop = messages.scrollHeight;
			return message;
		}

		function sendMessage(text) {
			var message = text.trim();
			var data;
			var typing;

			if (!message) {
				return;
			}

			appendMessage(message, 'user');
			input.value = '';
			input.disabled = true;
			typing = appendMessage((window.pubadModernChat && pubadModernChat.i18n.typing) || 'Assistant is typing...', 'bot ai-message--typing');

			data = new window.FormData();
			data.append('action', 'pubad_modern_chat');
			data.append('nonce', window.pubadModernChat ? pubadModernChat.nonce : '');
			data.append('message', message);

			window.fetch(window.pubadModernChat ? pubadModernChat.ajaxUrl : '', {
				method: 'POST',
				credentials: 'same-origin',
				body: data
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (response) {
					var fallback = (window.pubadModernChat && pubadModernChat.i18n.error) || 'Sorry, I could not respond right now.';
					typing.remove();
					appendMessage(response && response.data && response.data.reply ? response.data.reply : fallback, 'bot');
				})
				.catch(function () {
					typing.remove();
					appendMessage((window.pubadModernChat && pubadModernChat.i18n.error) || 'Sorry, I could not respond right now.', 'bot');
				})
				.finally(function () {
					input.disabled = false;
					input.focus();
				});
		}

		launcher.addEventListener('click', function () {
			setChatOpen(panel.hidden);
		});

		close.addEventListener('click', function () {
			setChatOpen(false);
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			sendMessage(input.value);
		});

		suggestions.forEach(function (button) {
			button.addEventListener('click', function () {
				setChatOpen(true);
				sendMessage(button.textContent || '');
			});
		});
	}
}());
