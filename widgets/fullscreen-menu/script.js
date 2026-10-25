/**
 * Moose Widgets: accessible fullscreen menu controller.
 */
(function () {
	'use strict';

	const SELECTOR = '.mw-fullscreen-menu';
	const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
	let openMenus = 0;

	class FullscreenMenu {
		constructor(root) {
			if (root.dataset.mwMenuReady === 'true') return;
			this.root = root;
			this.trigger = root.querySelector('.mw-fullscreen-menu__trigger');
			this.overlay = root.querySelector('.mw-fullscreen-menu__overlay');
			this.closeButton = root.querySelector('.mw-fullscreen-menu__close');
			if (!this.trigger || !this.overlay || !this.closeButton) return;

			this.isOpen = false;
			this.lastFocus = null;
			this.hideTimer = null;
			this.onKeydown = this.onKeydown.bind(this);
			this.open = this.open.bind(this);
			this.close = this.close.bind(this);
			this.onOverlayClick = this.onOverlayClick.bind(this);
			this.trigger.addEventListener('click', this.open);
			this.closeButton.addEventListener('click', this.close);
			this.overlay.addEventListener('click', this.onOverlayClick);
			this.root.dataset.mwMenuReady = 'true';
		}

		open() {
			if (this.isOpen) return;
			this.isOpen = true;
			window.clearTimeout(this.hideTimer);
			this.lastFocus = document.activeElement;
			this.overlay.hidden = false;
			// Commit the natively hidden state before starting the transition.
			this.overlay.getBoundingClientRect();
			this.overlay.setAttribute('aria-hidden', 'false');
			this.trigger.setAttribute('aria-expanded', 'true');
			this.overlay.classList.add('is-open');
			document.addEventListener('keydown', this.onKeydown);
			openMenus += 1;
			if (openMenus === 1) {
				const scrollbar = window.innerWidth - document.documentElement.clientWidth;
				document.body.style.paddingRight = scrollbar > 0 ? `${scrollbar}px` : '';
				document.body.classList.add('mw-fullscreen-menu-locked');
			}
			window.requestAnimationFrame(() => this.closeButton.focus());
		}

		close() {
			if (!this.isOpen) return;
			this.isOpen = false;
			this.overlay.classList.remove('is-open');
			this.overlay.setAttribute('aria-hidden', 'true');
			this.trigger.setAttribute('aria-expanded', 'false');
			document.removeEventListener('keydown', this.onKeydown);
			openMenus = Math.max(0, openMenus - 1);
			if (openMenus === 0) {
				document.body.classList.remove('mw-fullscreen-menu-locked');
				document.body.style.paddingRight = '';
			}
			if (this.lastFocus && document.contains(this.lastFocus)) this.lastFocus.focus();
			window.clearTimeout(this.hideTimer);
			this.hideTimer = window.setTimeout(() => {
				if (!this.isOpen) this.overlay.hidden = true;
			}, this.getAnimationDuration());
		}

		getAnimationDuration() {
			const value = window.getComputedStyle(this.overlay).getPropertyValue('--mw-duration').trim();
			const duration = Number.parseFloat(value);
			if (!Number.isFinite(duration)) return 420;
			return value.endsWith('ms') ? duration : duration * 1000;
		}

		onOverlayClick(event) {
			if (event.target === this.overlay && this.overlay.dataset.closeBackdrop === 'true') this.close();
		}

		onKeydown(event) {
			if (event.key === 'Escape') {
				event.preventDefault();
				this.close();
				return;
			}
			if (event.key !== 'Tab') return;
			const items = Array.from(this.overlay.querySelectorAll(FOCUSABLE)).filter((item) => item.offsetParent !== null);
			if (!items.length) {
				event.preventDefault();
				return;
			}
			const first = items[0];
			const last = items[items.length - 1];
			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		}
	}

	function init(scope) {
		const context = scope instanceof Element ? scope : document;
		if (context.matches && context.matches(SELECTOR)) new FullscreenMenu(context);
		context.querySelectorAll(SELECTOR).forEach((root) => new FullscreenMenu(root));
	}

	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => init(document));
	else init(document);

	window.addEventListener('elementor/frontend/init', function () {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/moose-fullscreen-menu.default', function (scope) {
				init(scope && scope[0] ? scope[0] : scope);
			});
		}
	});
})();
