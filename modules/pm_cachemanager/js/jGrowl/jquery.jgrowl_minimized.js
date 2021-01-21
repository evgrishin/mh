(function($jqPm) {
	$jqPm.jGrowl = function(m, o) {
		if ($jqPm('#jGrowl').size() == 0)
			$jqPm('<div id="jGrowl"></div>').addClass(
					(o && o.position) ? o.position
							: $jqPm.jGrowl.defaults.position).appendTo('body');
		$jqPm('#jGrowl').jGrowl(m, o);
	};
	$jqPm.fn.jGrowl = function(m, o) {
		if ($jqPm.isFunction(this.each)) {
			var args = arguments;
			return this.each(function() {
				var self = this;
				if ($jqPm(this).data('jGrowl.instance') == undefined) {
					$jqPm(this).data('jGrowl.instance',
							$jqPm.extend(new $jqPm.fn.jGrowl(), {
								notifications : [],
								element : null,
								interval : null
							}));
					$jqPm(this).data('jGrowl.instance').startup(this);
				}
				if ($jqPm.isFunction($jqPm(this).data('jGrowl.instance')[m])) {
					$jqPm(this).data('jGrowl.instance')[m].apply($jqPm(this)
							.data('jGrowl.instance'), $jqPm.makeArray(args)
							.slice(1));
				} else {
					$jqPm(this).data('jGrowl.instance').create(m, o);
				}
			});
		}
		;
	};
	$jqPm
			.extend(
					$jqPm.fn.jGrowl.prototype,
					{
						defaults : {
							pool : 0,
							header : '',
							group : '',
							sticky : false,
							position : 'top-right',
							glue : 'after',
							theme : 'default',
							themeState : 'highlight',
							corners : '10px',
							check : 250,
							life : 3000,
							closeDuration : 'normal',
							openDuration : 'normal',
							easing : 'swing',
							closer : true,
							closeTemplate : '&times;',
							closerTemplate : '<div>[ close all ]</div>',
							log : function(e, m, o) {
							},
							beforeOpen : function(e, m, o) {
							},
							afterOpen : function(e, m, o) {
							},
							open : function(e, m, o) {
							},
							beforeClose : function(e, m, o) {
							},
							close : function(e, m, o) {
							},
							animateOpen : {
								opacity : 'show'
							},
							animateClose : {
								opacity : 'hide'
							}
						},
						notifications : [],
						element : null,
						interval : null,
						create : function(message, o) {
							var o = $jqPm.extend({}, this.defaults, o);
							if (typeof o.speed !== 'undefined') {
								o.openDuration = o.speed;
								o.closeDuration = o.speed;
							}
							this.notifications.push({
								message : message,
								options : o
							});
							o.log.apply(this.element, [ this.element, message,
									o ]);
						},
						render : function(notification) {
							var self = this;
							var message = notification.message;
							var o = notification.options;
							var notification = $jqPm(
									'<div class="jGrowl-notification '
											+ o.themeState
											+ ' ui-corner-all'
											+ ((o.group != undefined && o.group != '') ? ' '
													+ o.group
													: '') + '">'
											+ '<div class="jGrowl-close">'
											+ o.closeTemplate + '</div>'
											+ '<div class="jGrowl-header">'
											+ o.header + '</div>'
											+ '<div class="jGrowl-message">'
											+ message + '</div></div>').data(
									"jGrowl", o).addClass(o.theme).children(
									'div.jGrowl-close').bind(
									"click.jGrowl",
									function() {
										$jqPm(this).parent().trigger(
												'jGrowl.close');
									}).parent();
							$jqPm(notification)
									.bind(
											"mouseover.jGrowl",
											function() {
												$jqPm(
														'div.jGrowl-notification',
														self.element).data(
														"jGrowl.pause", true);
											})
									.bind(
											"mouseout.jGrowl",
											function() {
												$jqPm(
														'div.jGrowl-notification',
														self.element).data(
														"jGrowl.pause", false);
											})
									.bind(
											'jGrowl.beforeOpen',
											function() {
												if (o.beforeOpen.apply(
														notification, [
																notification,
																message, o,
																self.element ]) != false) {
													$jqPm(this).trigger(
															'jGrowl.open');
												}
											})
									.bind(
											'jGrowl.open',
											function() {
												if (o.open.apply(notification,
														[ notification,
																message, o,
																self.element ]) != false) {
													if (o.glue == 'after') {
														$jqPm(
																'div.jGrowl-notification:last',
																self.element)
																.after(
																		notification);
													} else {
														$jqPm(
																'div.jGrowl-notification:first',
																self.element)
																.before(
																		notification);
													}
													$jqPm(this)
															.animate(
																	o.animateOpen,
																	o.openDuration,
																	o.easing,
																	function() {
																		if ($jqPm.browser.msie
																				&& (parseInt(
																						$jqPm(
																								this)
																								.css(
																										'opacity'),
																						10) === 1 || parseInt(
																						$jqPm(
																								this)
																								.css(
																										'opacity'),
																						10) === 0))
																			this.style
																					.removeAttribute('filter');
																		if ($jqPm(
																				this)
																				.data(
																						"jGrowl") != null)
																			$jqPm(
																					this)
																					.data(
																							"jGrowl").created = new Date();
																		$jqPm(
																				this)
																				.trigger(
																						'jGrowl.afterOpen');
																	});
												}
											})
									.bind(
											'jGrowl.afterOpen',
											function() {
												o.afterOpen.apply(notification,
														[ notification,
																message, o,
																self.element ]);
											})
									.bind(
											'jGrowl.beforeClose',
											function() {
												if (o.beforeClose.apply(
														notification, [
																notification,
																message, o,
																self.element ]) != false)
													$jqPm(this).trigger(
															'jGrowl.close');
											})
									.bind(
											'jGrowl.close',
											function() {
												$jqPm(this).data(
														'jGrowl.pause', true);
												$jqPm(this)
														.animate(
																o.animateClose,
																o.closeDuration,
																o.easing,
																function() {
																	if ($jqPm
																			.isFunction(o.close)) {
																		if (o.close
																				.apply(
																						notification,
																						[
																								notification,
																								message,
																								o,
																								self.element ]) !== false)
																			$jqPm(
																					this)
																					.remove();
																	} else {
																		$jqPm(
																				this)
																				.remove();
																	}
																});
											}).trigger('jGrowl.beforeOpen');
							if (o.corners != '' && $jqPm.fn.corner != undefined)
								$jqPm(notification).corner(o.corners);
							if ($jqPm('div.jGrowl-notification:parent',
									self.element).size() > 1
									&& $jqPm('div.jGrowl-closer', self.element)
											.size() == 0
									&& this.defaults.closer != false) {
								$jqPm(this.defaults.closerTemplate)
										.addClass(
												'jGrowl-closer ui-state-highlight ui-corner-all')
										.addClass(this.defaults.theme)
										.appendTo(self.element)
										.animate(this.defaults.animateOpen,
												this.defaults.speed,
												this.defaults.easing)
										.bind(
												"click.jGrowl",
												function() {
													$jqPm(this)
															.siblings()
															.trigger(
																	"jGrowl.beforeClose");
													if ($jqPm
															.isFunction(self.defaults.closer)) {
														self.defaults.closer
																.apply(
																		$jqPm(
																				this)
																				.parent()[0],
																		[ $jqPm(
																				this)
																				.parent()[0] ]);
													}
												});
							}
							;
						},
						update : function() {
							$jqPm(this.element)
									.find('div.jGrowl-notification:parent')
									.each(
											function() {
												if ($jqPm(this).data("jGrowl") != undefined
														&& $jqPm(this).data(
																"jGrowl").created != undefined
														&& ($jqPm(this).data(
																"jGrowl").created
																.getTime() + parseInt($jqPm(
																this).data(
																"jGrowl").life)) < (new Date())
																.getTime()
														&& $jqPm(this).data(
																"jGrowl").sticky != true
														&& ($jqPm(this).data(
																"jGrowl.pause") == undefined || $jqPm(
																this).data(
																"jGrowl.pause") != true)) {
													$jqPm(this)
															.trigger(
																	'jGrowl.beforeClose');
												}
											});
							if (this.notifications.length > 0
									&& (this.defaults.pool == 0 || $jqPm(
											this.element).find(
											'div.jGrowl-notification:parent')
											.size() < this.defaults.pool))
								this.render(this.notifications.shift());
							if ($jqPm(this.element).find(
									'div.jGrowl-notification:parent').size() < 2) {
								$jqPm(this.element).find('div.jGrowl-closer')
										.animate(this.defaults.animateClose,
												this.defaults.speed,
												this.defaults.easing,
												function() {
													$jqPm(this).remove();
												});
							}
						},
						startup : function(e) {
							this.element = $jqPm(e).addClass('jGrowl').append(
									'<div class="jGrowl-notification"></div>');
							this.interval = setInterval(function() {
								$jqPm(e).data('jGrowl.instance').update();
							}, parseInt(this.defaults.check));
							if ($jqPm.browser.msie
									&& parseInt($jqPm.browser.version) < 7
									&& !window["XMLHttpRequest"]) {
								$jqPm(this.element).addClass('ie6');
							}
						},
						shutdown : function() {
							$jqPm(this.element).removeClass('jGrowl').find(
									'div.jGrowl-notification').remove();
							clearInterval(this.interval);
						},
						close : function() {
							$jqPm(this.element).find('div.jGrowl-notification')
									.each(
											function() {
												$jqPm(this).trigger(
														'jGrowl.beforeClose');
											});
						}
					});
	$jqPm.jGrowl.defaults = $jqPm.fn.jGrowl.prototype.defaults;
})($jqPm)