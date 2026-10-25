/** Moose Widgets: accessible AJAX search. */
(function () {
	'use strict';

	class AjaxSearch {
		constructor(root) {
			if (root.dataset.mwSearchReady === 'true') return;
			this.root = root;
			this.trigger = root.querySelector('.mw-ajax-search__trigger');
			this.panel = root.querySelector('.mw-ajax-search__panel');
			this.form = root.querySelector('.mw-ajax-search__form');
			this.input = root.querySelector('.mw-ajax-search__input');
			this.results = root.querySelector('.mw-ajax-search__results');
			if (!this.panel || !this.form || !this.input || !this.results) return;

			try { this.config = JSON.parse(root.dataset.config || '{}'); }
			catch (error) { this.config = {}; }
			this.timer = null;
			this.controller = null;
			this.hostTimer = null;
			this.panelTimer = null;
			this.isInline = root.classList.contains('mw-ajax-search--inline');
			this.isBar = root.classList.contains('mw-ajax-search--bar');
			this.host = null;
			this.panelInner = this.panel.querySelector('.mw-ajax-search__panel-inner');
			this.resizeObserver = null;
			this.onDocumentClick = this.onDocumentClick.bind(this);
			this.onKeydown = this.onKeydown.bind(this);
			this.onInput = this.onInput.bind(this);

			if (this.trigger) {
				this.trigger.addEventListener('click', () => this.toggle());
				if (this.isBar) {
					this.trigger.addEventListener('pointerenter', () => this.setupBarRow(), { once: true, passive: true });
					this.trigger.addEventListener('focus', () => this.setupBarRow(), { once: true });
				}
			}
			this.input.addEventListener('input', this.onInput);
			this.root.addEventListener('keydown', this.onKeydown);
			document.addEventListener('click', this.onDocumentClick);
			this.root.dataset.mwSearchReady = 'true';
		}

		open() {
			if (this.isBar && !this.host) this.setupBarRow();
			window.clearTimeout(this.hostTimer);
			window.clearTimeout(this.panelTimer);
			this.panel.hidden = false;
			if (this.host) {
				this.host.classList.add('is-open');
				this.updatePanelHeight();
				// Commit the newly displayed row before starting its panel transition.
				this.host.getBoundingClientRect();
			}
			this.root.classList.add('is-open');
			this.panel.setAttribute('aria-hidden', 'false');
			if (this.trigger) this.trigger.setAttribute('aria-expanded', 'true');
			// Start visual expansion on the next frame, after the browser has
			// committed the zero-height row. This avoids a snapped first frame.
			window.requestAnimationFrame(() => {
				this.panel.classList.add('is-open');
				this.focusInput();
			});
			window.setTimeout(() => {
				if (document.activeElement !== this.input) this.focusInput();
			}, 60);
		}

		focusInput() {
			try { this.input.focus({ preventScroll: true }); }
			catch (error) { this.input.focus(); }
			if (this.input.value) this.input.select();
		}

		close() {
			if (this.isInline) return;
			this.root.classList.remove('is-open');
			this.panel.classList.remove('is-open');
			this.panel.setAttribute('aria-hidden', 'true');
			this.input.setAttribute('aria-expanded', 'false');
			if (this.trigger) this.trigger.setAttribute('aria-expanded', 'false');
			this.hideResults();
			if (this.host) {
				window.clearTimeout(this.hostTimer);
				this.hostTimer = window.setTimeout(() => this.host.classList.remove('is-open'), 250);
			}
			if (!this.isInline) {
				window.clearTimeout(this.panelTimer);
				this.panelTimer = window.setTimeout(() => {
					if (!this.root.classList.contains('is-open')) this.panel.hidden = true;
				}, 250);
			}
		}

		toggle() { this.root.classList.contains('is-open') ? this.close() : this.open(); }

		onDocumentClick(event) {
			if (this.config.closeOutside && !this.root.contains(event.target) && !this.panel.contains(event.target)) this.close();
		}

		setupBarRow() {
			if (!this.isBar || this.host || !this.panel.isConnected) return;
			const widget = this.root.closest('.elementor-element');
			let row = this.root.closest('.e-con, .elementor-section');
			if (!widget || !row || !row.parentNode) return;

			// Walk out of nested columns to the containing Elementor row. A search
			// icon is commonly inside several nested header containers; inserting
			// beside the nearest column is what caused the old right-edge clipping.
			const region = this.root.closest('header, footer, .elementor-location-header, .elementor-location-footer, [data-elementor-type="header"], [data-elementor-type="footer"]');
			while (row.parentElement) {
				const parentRow = row.parentElement.closest('.e-con, .elementor-section');
				if (!parentRow || parentRow === row || (region && !region.contains(parentRow))) break;
				row = parentRow;
			}

			const oldHost = document.querySelector(`[data-mw-search-bar-host-for="${this.panel.id}"]`);
			if (oldHost) oldHost.remove();

			const host = document.createElement('div');
			host.className = 'mw-ajax-search__bar-host';
			Array.from(widget.classList).forEach((className) => {
				if (className === 'elementor-element' || className.indexOf('elementor-element-') === 0) host.classList.add(className);
			});
			host.setAttribute('data-mw-search-bar-host-for', this.panel.id);
			row.insertAdjacentElement('afterend', host);
			host.appendChild(this.panel);
			this.host = host;
			if ('ResizeObserver' in window && this.panelInner) {
				this.resizeObserver = new ResizeObserver(() => this.updatePanelHeight());
				this.resizeObserver.observe(this.panelInner);
			}
		}

		updatePanelHeight() {
			if (!this.isBar || !this.panelInner) return;
			this.panel.style.setProperty('--mw-search-panel-height', `${this.panelInner.scrollHeight}px`);
		}

		onKeydown(event) {
			if (event.key === 'Escape') {
				this.close();
				if (this.trigger) this.trigger.focus();
				return;
			}

			if (!['ArrowDown', 'ArrowUp'].includes(event.key)) return;
			const links = Array.from(this.results.querySelectorAll('a'));
			if (!links.length) return;
			event.preventDefault();
			const current = links.indexOf(document.activeElement);
			let next = event.key === 'ArrowDown' ? current + 1 : current - 1;
			if (next >= links.length) next = 0;
			if (next < 0) next = links.length - 1;
			links[next].focus();
		}

		onInput() {
			window.clearTimeout(this.timer);
			const query = this.input.value.trim();
			if (!this.config.enabled || query.length < Number(this.config.minChars || 2)) {
				if (this.controller) this.controller.abort();
				this.hideResults();
				return;
			}
			this.timer = window.setTimeout(() => this.search(query), Number(this.config.debounce || 300));
		}

		async search(query) {
			if (this.controller) this.controller.abort();
			this.controller = new AbortController();
			this.showStatus('<span class="mw-ajax-search__spinner" aria-hidden="true"></span><span class="screen-reader-text">Loading results</span>', true);

			const body = new URLSearchParams();
			body.append('action', 'moose_widgets_search');
			body.append('nonce', this.config.nonce || '');
			body.append('query', query);
			body.append('limit', String(this.config.limit || 5));
			(this.config.postTypes || ['post']).forEach((type) => body.append('post_types[]', type));

			try {
				const response = await fetch(this.config.ajaxUrl, {
					method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body, signal: this.controller.signal
				});
				if (!response.ok) throw new Error('Search request failed');
				const payload = await response.json();
				if (!payload.success) throw new Error('Invalid search response');
				this.renderResults(payload.data.items || [], query);
			} catch (error) {
				if (error.name !== 'AbortError') this.showStatus(this.config.noResults || 'No results found.');
			}
		}

		renderResults(items, query) {
			this.results.replaceChildren();
			if (!items.length) {
				this.showStatus(this.config.noResults || 'No results found.');
				return;
			}

			const list = document.createElement('ul');
			list.className = 'mw-ajax-search__results-list';
			items.forEach((item) => {
				const row = document.createElement('li');
				const link = document.createElement('a');
				link.className = 'mw-ajax-search__result-link';
				link.href = item.url;
				link.setAttribute('role', 'option');

				if (this.config.showImage && item.thumbnail) {
					const image = document.createElement('img');
					image.className = 'mw-ajax-search__result-image';
					image.src = item.thumbnail;
					image.alt = '';
					image.loading = 'lazy';
					link.appendChild(image);
				}

				const content = document.createElement('div');
				content.className = 'mw-ajax-search__result-content';
				const title = document.createElement('h3');
				title.className = 'mw-ajax-search__result-title';
				title.textContent = item.title;
				content.appendChild(title);

				const metaValues = [];
				if (this.config.showType && item.type) metaValues.push(item.type);
				if (this.config.showDate && item.date) metaValues.push(item.date);
				if (metaValues.length) {
					const meta = document.createElement('div');
					meta.className = 'mw-ajax-search__result-meta';
					meta.textContent = metaValues.join(' · ');
					content.appendChild(meta);
				}
				if (this.config.showExcerpt && item.excerpt) {
					const excerpt = document.createElement('p');
					excerpt.className = 'mw-ajax-search__result-excerpt';
					excerpt.textContent = item.excerpt;
					content.appendChild(excerpt);
				}
				link.appendChild(content);
				row.appendChild(link);
				list.appendChild(row);
			});
			this.results.appendChild(list);

			if (this.config.viewAll) {
				const all = document.createElement('a');
				all.className = 'mw-ajax-search__view-all';
				all.href = `${this.form.action}?s=${encodeURIComponent(query)}`;
				all.textContent = this.config.viewAll;
				this.results.appendChild(all);
			}
			this.showResults();
		}

		showStatus(content, isHtml) {
			this.results.replaceChildren();
			const status = document.createElement('p');
			status.className = 'mw-ajax-search__status';
			if (isHtml) status.innerHTML = content;
			else status.textContent = content;
			this.results.appendChild(status);
			this.showResults();
		}

		showResults() {
			this.results.hidden = false;
			this.input.setAttribute('aria-expanded', 'true');
		}

		hideResults() {
			this.results.hidden = true;
			this.results.replaceChildren();
			this.input.setAttribute('aria-expanded', 'false');
		}
	}

	function init(scope) {
		const context = scope instanceof Element ? scope : document;
		if (context.matches && context.matches('.mw-ajax-search')) new AjaxSearch(context);
		context.querySelectorAll('.mw-ajax-search').forEach((root) => new AjaxSearch(root));
	}

	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => init(document));
	else init(document);

	window.addEventListener('elementor/frontend/init', function () {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/moose-ajax-search.default', function (scope) {
				init(scope && scope[0] ? scope[0] : scope);
			});
		}
	});
})();
