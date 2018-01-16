/* =========================================================
 * bootstrap-tabdrop.js
 * http://www.eyecon.ro/bootstrap-tabdrop
 * =========================================================
 * Copyright 2012 Stefan Petre
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */

!function( $ ) {

	var TabDrop = function(element, options) {
		var self = this,
				timer;
		this.ignore = false;
		this.element = $(element);
		this.dropdown = $('<li class="dropdown hide pull-right tabdrop">' +
				'<a class="dropdown-toggle" data-toggle="dropdown" href="#">' +
				options.text + ' <b class="caret"></b></a>' +
				'<ul class="dropdown-menu"></ul></li>').prependTo(this.element);
		this.iframe = $('<iframe style="display: block; overflow: hidden; border: 0px; margin: 0px; top: 0px; left: 0px; bottom: 0px; right: 0px; height: 100%; width: 100%; position: absolute; pointer-events: none; z-index: -1;"></iframe>')
										.prependTo(this.element);
		if (this.element.parent().is('.tabs-below')) {
			this.dropdown.addClass('dropup');
		}
		
		$(this.iframe[0].contentWindow).on('resize', function() {
			if (!self.ignore) {
				clearTimeout(timer);
				timer = setTimeout($.proxy(self.layout, self), 100);
			}
		});
		this.layout();
	};

	TabDrop.prototype = {
		constructor: TabDrop,

		layout: function() {
			this.ignore = true;
			var collection = [], needed = false;
			this.dropdown.addClass('hide');
			this.element
				.append(this.dropdown.find('li'))
				.find('>li')
				.not('.tabdrop')
				.each(function(){
					if(this.offsetTop > 0) {
						needed = true;
						return false;
					}
				});
			if (needed) {
				this.dropdown.removeClass('hide');
				this.element
					.find('>li')
					.not('.tabdrop')
					.each(function(){
						if(this.offsetTop > 0) {
							collection.push(this);
						}
					});
			}
			if (collection.length > 0) {
				collection = $(collection);
				this.dropdown
					.find('ul')
					.empty()
					.append(collection);
				if (this.dropdown.find('.active').length == 1) {
					this.dropdown.addClass('active');
				} else {
					this.dropdown.removeClass('active');
				}
			}
			this.element.trigger('tabdrop.layout.complete');
			var self = this;
			setTimeout(function() {
				self.ignore = false;
			}, 100)
		}
	};

	$.fn.tabdrop = function ( option ) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data('tabdrop'),
				options = typeof option === 'object' && option;
			if (!data)  {
				$this.data('tabdrop', (data = new TabDrop(this, $.extend({},
											$.fn.tabdrop.defaults,options))));
			}
			if (typeof option == 'string') {
				data[option]();
			}
		});
	};

	$.fn.tabdrop.defaults = {
		text: '<i class="icon-align-justify"></i>'
	};

	$.fn.tabdrop.Constructor = TabDrop;

}(window.jQuery);
