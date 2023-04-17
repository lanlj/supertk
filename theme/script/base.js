(function(c) {
	var b = "Fileupload";
	var d = {
		url: document.URL,
		method: "POST",
		extraData: {},
		maxFileSize: 0,
		allowedTypes: "*",
		extFilter: null,
		dataType: null,
		fileName: "file",
		onInit: function() {},
		onFallbackMode: function() {
			message
		},
		onNewFile: function(g, f) {},
		onBeforeUpload: function(f) {},
		onComplete: function() {},
		onUploadProgress: function(g, f) {},
		onUploadSuccess: function(g, f) {},
		onUploadError: function(g, f) {},
		onFileTypeError: function(f) {},
		onFileSizeError: function(f) {},
		onFileExtError: function(f) {}
	};
	var a = function(g, f) {
			this.element = c(g);
			this.settings = c.extend({}, d, f);
			if (!this.checkBrowser()) {
				return false
			}
			this.init();
			return true
		};
	a.prototype.checkBrowser = function() {
		if (window.FormData === undefined) {
			this.settings.onFallbackMode.call(this.element, "Browser doesn't support From API");
			return false
		}
		if (this.element.find("input[type=file]").length > 0) {
			return true
		}
		if (!this.checkEvent("drop", this.element) || !this.checkEvent("dragstart", this.element)) {
			this.settings.onFallbackMode.call(this.element, "Browser doesn't support Ajax Drag and Drop");
			return false
		}
		return true
	};
	a.prototype.checkEvent = function(f, h) {
		var h = h || document.createElement("div");
		var f = "on" + f;
		var g = f in h;
		if (!g) {
			if (!h.setAttribute) {
				h = document.createElement("div")
			}
			if (h.setAttribute && h.removeAttribute) {
				h.setAttribute(f, "");
				g = typeof h[f] == "function";
				if (typeof h[f] != "undefined") {
					h[f] = undefined
				}
				h.removeAttribute(f)
			}
		}
		h = null;
		return g
	};
	a.prototype.init = function() {
		var f = this;
		f.queue = new Array();
		f.queuePos = -1;
		f.queueRunning = false;
		f.element.on("drop", function(g) {
			g.preventDefault();
			var h = g.originalEvent.dataTransfer.files;
			f.queueFiles(h)
		});
		f.element.find("input[type=file]").on("change", function(g) {
			var h = g.target.files;
			f.queueFiles(h);
			c(this).val("")
		});
		this.settings.onInit.call(this.element)
	};
	a.prototype.queueFiles = function(m) {
		var g = this.queue.length;
		for (var k = 0; k < m.length; k++) {
			var h = m[k];
			if ((this.settings.maxFileSize > 0) && (h.size > this.settings.maxFileSize)) {
				this.settings.onFileSizeError.call(this.element, h);
				continue
			}
			if ((this.settings.allowedTypes != "*") && !h.type.match(this.settings.allowedTypes)) {
				this.settings.onFileTypeError.call(this.element, h);
				continue
			}
			if (this.settings.extFilter != null) {
				var n = this.settings.extFilter.toLowerCase().split(";");
				var l = h.name.toLowerCase().split(".").pop();
				if (c.inArray(l, n) < 0) {
					this.settings.onFileExtError.call(this.element, h);
					continue
				}
			}
			this.queue.push(h);
			var f = this.queue.length - 1;
			this.settings.onNewFile.call(this.element, f, h)
		}
		if (this.queueRunning) {
			return false
		}
		if (this.queue.length == g) {
			return false
		}
		this.processQueue();
		return true
	};
	a.prototype.processQueue = function() {
		var h = this;
		h.queuePos++;
		if (h.queuePos >= h.queue.length) {
			h.settings.onComplete.call(h.element);
			h.queuePos = (h.queue.length - 1);
			h.queueRunning = false;
			return
		}
		var g = h.queue[h.queuePos];
		var f = new FormData();
		f.append(h.settings.fileName, g);
		c.each(h.settings.extraData, function(i, j) {
			f.append(i, j)
		});
		h.settings.onBeforeUpload.call(h.element, h.queuePos);
		h.queueRunning = true;
		c.ajax({
			url: h.settings.url,
			type: h.settings.method,
			dataType: h.settings.dataType,
			data: f,
			cache: false,
			contentType: false,
			processData: false,
			forceSync: false,
			xhr: function() {
				var i = c.ajaxSettings.xhr();
				if (i.upload) {
					i.upload.addEventListener("progress", function(m) {
						var l = 0;
						var j = m.loaded || m.position;
						var k = m.total || e.totalSize;
						if (m.lengthComputable) {
							l = Math.ceil(j / k * 100)
						}
						h.settings.onUploadProgress.call(h.element, h.queuePos, l)
					}, false)
				}
				return i
			},
			success: function(j, i, k) {
				h.settings.onUploadSuccess.call(h.element, h.queuePos, j)
			},
			error: function(k, i, j) {
				h.settings.onUploadError.call(h.element, h.queuePos, j)
			},
			complete: function(i, j) {
				h.processQueue()
			}
		})
	};
	c.fn.Fileupload = function(f) {
		return this.each(function() {
			if (!c.data(this, b)) {
				c.data(this, b, new a(this, f))
			}
		})
	};
	c(document).on("dragenter", function(f) {
		f.stopPropagation();
		f.preventDefault()
	});
	c(document).on("dragover", function(f) {
		f.stopPropagation();
		f.preventDefault()
	});
	c(document).on("drop", function(f) {
		f.stopPropagation();
		f.preventDefault()
	})
})(jQuery);
(function($) {
	$.fn.tipTip = function(options) {
		var defaults = {
			activation: "hover",
			keepAlive: false,
			maxWidth: "200px",
			edgeOffset: 3,
			defaultPosition: "top",
			delay: 200,
			fadeIn: 200,
			fadeOut: 200,
			attribute: "data-tip",
			content: false,
			enter: function() {},
			exit: function() {}
		};
		var opts = $.extend(defaults, options);
		if ($("#tiptip_holder").length <= 0) {
			var tiptip_holder = $('<div id="tiptip_holder" style="max-width:' + opts.maxWidth + ';"></div>');
			var tiptip_content = $('<div id="tiptip_content"></div>');
			var tiptip_arrow = $('<div id="tiptip_arrow"></div>');
			$("body").append(tiptip_holder.html(tiptip_content).prepend(tiptip_arrow.html('<div id="tiptip_arrow_inner"></div>')))
		} else {
			var tiptip_holder = $("#tiptip_holder");
			var tiptip_content = $("#tiptip_content");
			var tiptip_arrow = $("#tiptip_arrow")
		}
		return this.each(function() {
			var org_elem = $(this);
			if (opts.content) {
				var org_title = opts.content
			} else {
				var org_title = org_elem.attr(opts.attribute)
			}
			if (org_title != "") {
				if (!opts.content) {
					org_elem.removeAttr(opts.attribute)
				}
				var timeout = false;
				if (opts.activation == "hover") {
					org_elem.hover(function() {
						active_tiptip()
					}, function() {
						if (!opts.keepAlive) {
							deactive_tiptip()
						}
					});
					if (opts.keepAlive) {
						tiptip_holder.hover(function() {}, function() {
							deactive_tiptip()
						})
					}
				} else if (opts.activation == "focus") {
					org_elem.focus(function() {
						active_tiptip()
					}).blur(function() {
						deactive_tiptip()
					})
				} else if (opts.activation == "click") {
					org_elem.click(function() {
						active_tiptip();
						return false
					}).hover(function() {}, function() {
						if (!opts.keepAlive) {
							deactive_tiptip()
						}
					});
					if (opts.keepAlive) {
						tiptip_holder.hover(function() {}, function() {
							deactive_tiptip()
						})
					}
				}

				function active_tiptip() {
					opts.enter.call(this);
					tiptip_content.html(org_title);
					tiptip_holder.hide().removeAttr("class").css("margin", "0");
					tiptip_arrow.removeAttr("style");
					var top = parseInt(org_elem.offset()['top']);
					var left = parseInt(org_elem.offset()['left']);
					var org_width = parseInt(org_elem.outerWidth());
					var org_height = parseInt(org_elem.outerHeight());
					var tip_w = tiptip_holder.outerWidth();
					var tip_h = tiptip_holder.outerHeight();
					var w_compare = Math.round((org_width - tip_w) / 2);
					var h_compare = Math.round((org_height - tip_h) / 2);
					var marg_left = Math.round(left + w_compare);
					var marg_top = Math.round(top + org_height + opts.edgeOffset);
					var t_class = "";
					var arrow_top = "";
					var arrow_left = Math.round(tip_w - 12) / 2;
					if (opts.defaultPosition == "bottom") {
						t_class = "_bottom"
					} else if (opts.defaultPosition == "top") {
						t_class = "_top"
					} else if (opts.defaultPosition == "left") {
						t_class = "_left"
					} else if (opts.defaultPosition == "right") {
						t_class = "_right"
					}
					var right_compare = (w_compare + left) < parseInt($(window).scrollLeft());
					var left_compare = (tip_w + left) > parseInt($(window).width());
					if ((right_compare && w_compare < 0) || (t_class == "_right" && !left_compare) || (t_class == "_left" && left < (tip_w + opts.edgeOffset + 5))) {
						t_class = "_right";
						arrow_top = Math.round(tip_h - 13) / 2;
						arrow_left = -12;
						marg_left = Math.round(left + org_width + opts.edgeOffset);
						marg_top = Math.round(top + h_compare)
					} else if ((left_compare && w_compare < 0) || (t_class == "_left" && !right_compare)) {
						t_class = "_left";
						arrow_top = Math.round(tip_h - 13) / 2;
						arrow_left = Math.round(tip_w);
						marg_left = Math.round(left - (tip_w + opts.edgeOffset + 5));
						marg_top = Math.round(top + h_compare)
					}
					var top_compare = (top + org_height + opts.edgeOffset + tip_h + 8) > parseInt($(window).height() + $(window).scrollTop());
					var bottom_compare = ((top + org_height) - (opts.edgeOffset + tip_h + 8)) < 0;
					if (top_compare || (t_class == "_bottom" && top_compare) || (t_class == "_top" && !bottom_compare)) {
						if (t_class == "_top" || t_class == "_bottom") {
							t_class = "_top"
						} else {
							t_class = t_class + "_top"
						}
						arrow_top = tip_h;
						marg_top = Math.round(top - (tip_h + 5 + opts.edgeOffset))
					} else if (bottom_compare | (t_class == "_top" && bottom_compare) || (t_class == "_bottom" && !top_compare)) {
						if (t_class == "_top" || t_class == "_bottom") {
							t_class = "_bottom"
						} else {
							t_class = t_class + "_bottom"
						}
						arrow_top = -12;
						marg_top = Math.round(top + org_height + opts.edgeOffset)
					}
					if (t_class == "_right_top" || t_class == "_left_top") {
						marg_top = marg_top + 5
					} else if (t_class == "_right_bottom" || t_class == "_left_bottom") {
						marg_top = marg_top - 5
					}
					if (t_class == "_left_top" || t_class == "_left_bottom") {
						marg_left = marg_left + 5
					}
					tiptip_arrow.css({
						"margin-left": arrow_left + "px",
						"margin-top": arrow_top + "px"
					});
					tiptip_holder.css({
						"margin-left": marg_left + "px",
						"margin-top": marg_top + "px"
					}).attr("class", "tip" + t_class);
					if (timeout) {
						clearTimeout(timeout)
					}
					timeout = setTimeout(function() {
						tiptip_holder.stop(true, true).fadeIn(opts.fadeIn)
					}, opts.delay)
				}

				function deactive_tiptip() {
					opts.exit.call(this);
					if (timeout) {
						clearTimeout(timeout)
					}
					tiptip_holder.fadeOut(opts.fadeOut)
				}
			}
		})
	}
})(jQuery);
(function() {
	var a = jQuery,
		b = function() {
			function a() {
				this.fadeDuration = 500, this.fitImagesInViewport = !0, this.resizeDuration = 700, this.positionFromTop = 50, this.showImageNumberLabel = !0, this.alwaysShowNavOnTouchDevices = !1, this.wrapAround = !1
			}
			return a.prototype.albumLabel = function(a, b) {
				return "当前第 " + a + " 张，共 " + b + " 张"
			}, a
		}(),
		c = function() {
			function b(a) {
				this.options = a, this.album = [], this.currentImageIndex = void 0, this.init()
			}
			return b.prototype.init = function() {
				this.enable(), this.build()
			}, b.prototype.enable = function() {
				var b = this;
				a("body").on("click", "a[rel^=lightbox], area[rel^=lightbox], a[data-lightbox], area[data-lightbox]", function(c) {
					return b.start(a(c.currentTarget)), !1
				})
			}, b.prototype.build = function() {
				var b = this;
				a("<div id='lightboxOverlay' class='lightboxOverlay'></div><div id='lightbox' class='lightbox'><div class='lb-outerContainer'><div class='lb-closeContainer'><a class='lb-close'></a></div><div class='lb-container'><img class='lb-image' src='' /><div class='lb-nav'><a class='lb-prev' href='' ></a><a class='lb-next' href='' ></a></div><div class='lb-loader'><a class='lb-cancel'></a></div></div></div><div class='lb-dataContainer'><div class='lb-data'><div class='lb-avatar'></div><div class='lb-details'><div class='lb-name'></div><span class='lb-caption'></span><span class='lb-number'></span></div></div></div></div>").appendTo(a("body")), this.$lightbox = a("#lightbox"), this.$overlay = a("#lightboxOverlay"), this.$outerContainer = this.$lightbox.find(".lb-outerContainer"), this.$container = this.$lightbox.find(".lb-container"), this.containerTopPadding = parseInt(this.$container.css("padding-top"), 10), this.containerRightPadding = parseInt(this.$container.css("padding-right"), 10), this.containerBottomPadding = parseInt(this.$container.css("padding-bottom"), 10), this.containerLeftPadding = parseInt(this.$container.css("padding-left"), 10), this.$overlay.hide().on("click", function() {
					return b.end(), !1
				}), this.$lightbox.hide().on("click", function(c) {
					return "lightbox" === a(c.target).attr("id") && b.end(), !1
				}), this.$outerContainer.on("click", function(c) {
					return "lightbox" === a(c.target).attr("id") && b.end(), !1
				}), this.$lightbox.find(".lb-prev").on("click", function() {
					return b.changeImage(0 === b.currentImageIndex ? b.album.length - 1 : b.currentImageIndex - 1), !1
				}), this.$lightbox.find(".lb-next").on("click", function() {
					return b.changeImage(b.currentImageIndex === b.album.length - 1 ? 0 : b.currentImageIndex + 1), !1
				}), this.$lightbox.find(".lb-loader, .lb-close").on("click", function() {
					return b.end(), !1
				})
			}, b.prototype.start = function(b) {
				function c(a) {
					d.album.push({
						link: a.attr("href"),
						title: a.attr("data-title") || a.attr("title"),
						avatar: a.attr("data-avatar"),
						name: a.attr("data-name")
					})
				}
				var d = this,
					e = a(window);
				e.on("resize", a.proxy(this.sizeOverlay, this)), a("select, object, embed").css({
					visibility: "hidden"
				}), this.sizeOverlay(), this.album = [];
				var f, g = 0,
					h = b.attr("data-lightbox");
				if (h) {
					f = a(b.prop("tagName") + '[data-lightbox="' + h + '"]');
					for (var i = 0; i < f.length; i = ++i) c(a(f[i])), f[i] === b[0] && (g = i)
				} else if ("lightbox" === b.attr("rel")) c(b);
				else {
					f = a(b.prop("tagName") + '[rel="' + b.attr("rel") + '"]');
					for (var j = 0; j < f.length; j = ++j) c(a(f[j])), f[j] === b[0] && (g = j)
				}
				var k = e.scrollTop() + this.options.positionFromTop,
					l = e.scrollLeft();
				this.$lightbox.css({
					top: k + "px",
					left: l + "px"
				}).fadeIn(this.options.fadeDuration), this.changeImage(g)
			}, b.prototype.changeImage = function(b) {
				var c = this;
				this.disableKeyboardNav();
				var d = this.$lightbox.find(".lb-image");
				this.$overlay.fadeIn(this.options.fadeDuration), a(".lb-loader").fadeIn("slow"), this.$lightbox.find(".lb-image, .lb-nav, .lb-prev, .lb-next, .lb-dataContainer, .lb-numbers, .lb-caption").hide(), this.$outerContainer.addClass("animating");
				var e = new Image;
				e.onload = function() {
					var f, g, h, i, j, k, l;
					d.attr("src", c.album[b].link), f = a(e), d.width(e.width), d.height(e.height), c.options.fitImagesInViewport && (l = a(window).width(), k = a(window).height(), j = l - c.containerLeftPadding - c.containerRightPadding - 20, i = k - c.containerTopPadding - c.containerBottomPadding - 120, (e.width > j || e.height > i) && (e.width / j > e.height / i ? (h = j, g = parseInt(e.height / (e.width / h), 10), d.width(h), d.height(g)) : (g = i, h = parseInt(e.width / (e.height / g), 10), d.width(h), d.height(g)))), c.sizeContainer(d.width(), d.height())
				}, e.src = this.album[b].link, this.currentImageIndex = b
			}, b.prototype.sizeOverlay = function() {
				this.$overlay.width(a(window).width()).height(a(document).height())
			}, b.prototype.sizeContainer = function(a, b) {
				function c() {
					d.$lightbox.find(".lb-dataContainer").width(g), d.$lightbox.find(".lb-prevLink").height(h), d.$lightbox.find(".lb-nextLink").height(h), d.showImage()
				}
				var d = this,
					e = this.$outerContainer.outerWidth(),
					f = this.$outerContainer.outerHeight(),
					g = a + this.containerLeftPadding + this.containerRightPadding,
					h = b + this.containerTopPadding + this.containerBottomPadding;
				e !== g || f !== h ? this.$outerContainer.animate({
					width: g,
					height: h
				}, this.options.resizeDuration, "swing", function() {
					c()
				}) : c()
			}, b.prototype.showImage = function() {
				this.$lightbox.find(".lb-loader").hide(), this.$lightbox.find(".lb-image").fadeIn("slow"), this.updateNav(), this.updateDetails(), this.preloadNeighboringImages(), this.enableKeyboardNav()
			}, b.prototype.updateNav = function() {
				var a = !1;
				try {
					document.createEvent("TouchEvent"), a = this.options.alwaysShowNavOnTouchDevices ? !0 : !1
				} catch (b) {}
				this.$lightbox.find(".lb-nav").show(), this.album.length > 1 && (this.options.wrapAround ? (a && this.$lightbox.find(".lb-prev, .lb-next").css("opacity", "1"), this.$lightbox.find(".lb-prev, .lb-next").show()) : (this.currentImageIndex > 0 && (this.$lightbox.find(".lb-prev").show(), a && this.$lightbox.find(".lb-prev").css("opacity", "1")), this.currentImageIndex < this.album.length - 1 && (this.$lightbox.find(".lb-next").show(), a && this.$lightbox.find(".lb-next").css("opacity", "1"))))
			}, b.prototype.updateDetails = function() {
				var b = this;
				"undefined" != typeof this.album[this.currentImageIndex].title && "" !== this.album[this.currentImageIndex].title && this.$lightbox.find(".lb-caption").html(this.album[this.currentImageIndex].title).fadeIn("fast") && this.$lightbox.find(".lb-name").html(this.album[this.currentImageIndex].name).fadeIn("fast") && this.$lightbox.find(".lb-avatar").html("<img src='" + this.album[this.currentImageIndex].avatar + "'/>").fadeIn("fast").find("a").on("click", function() {
					location.href = a(this).attr("href")
				}), this.album.length > 1 && this.options.showImageNumberLabel ? this.$lightbox.find(".lb-number").text(this.options.albumLabel(this.currentImageIndex + 1, this.album.length)).fadeIn("fast") : this.$lightbox.find(".lb-number").hide(), this.$outerContainer.removeClass("animating"), this.$lightbox.find(".lb-dataContainer").fadeIn(this.options.resizeDuration, function() {
					return b.sizeOverlay()
				})
			}, b.prototype.preloadNeighboringImages = function() {
				if (this.album.length > this.currentImageIndex + 1) {
					var a = new Image;
					a.src = this.album[this.currentImageIndex + 1].link
				}
				if (this.currentImageIndex > 0) {
					var b = new Image;
					b.src = this.album[this.currentImageIndex - 1].link
				}
			}, b.prototype.enableKeyboardNav = function() {
				a(document).on("keyup.keyboard", a.proxy(this.keyboardAction, this))
			}, b.prototype.disableKeyboardNav = function() {
				a(document).off(".keyboard")
			}, b.prototype.keyboardAction = function(a) {
				var b = 27,
					c = 37,
					d = 39,
					e = a.keyCode,
					f = String.fromCharCode(e).toLowerCase();
				e === b || f.match(/x|o|c/) ? this.end() : "p" === f || e === c ? 0 !== this.currentImageIndex ? this.changeImage(this.currentImageIndex - 1) : this.options.wrapAround && this.album.length > 1 && this.changeImage(this.album.length - 1) : ("n" === f || e === d) && (this.currentImageIndex !== this.album.length - 1 ? this.changeImage(this.currentImageIndex + 1) : this.options.wrapAround && this.album.length > 1 && this.changeImage(0))
			}, b.prototype.end = function() {
				this.disableKeyboardNav(), a(window).off("resize", this.sizeOverlay), this.$lightbox.fadeOut(this.options.fadeDuration), this.$overlay.fadeOut(this.options.fadeDuration), a("select, object, embed").css({
					visibility: "visible"
				})
			}, b
		}();
	a(function() {
		{
			var a = new b;
			new c(a)
		}
	})
}).call(this);
(function() {
	(function(factory) {
		if (typeof define === 'function' && define.amd) {
			return define(['jquery'], factory)
		} else {
			return factory(window.jQuery)
		}
	})(function($) {
		"use strict";
		var EditableCaret, InputCaret, Mirror, Utils, methods, pluginName;
		pluginName = 'caret';
		EditableCaret = (function() {
			function EditableCaret($inputor) {
				this.$inputor = $inputor;
				this.domInputor = this.$inputor[0]
			}
			EditableCaret.prototype.setPos = function(pos) {
				return this.domInputor
			};
			EditableCaret.prototype.getIEPosition = function() {
				return $.noop()
			};
			EditableCaret.prototype.getPosition = function() {
				return $.noop()
			};
			EditableCaret.prototype.getOldIEPos = function() {
				var preCaretTextRange, textRange;
				textRange = document.selection.createRange();
				preCaretTextRange = document.body.createTextRange();
				preCaretTextRange.moveToElementText(this.domInputor);
				preCaretTextRange.setEndPoint("EndToEnd", textRange);
				return preCaretTextRange.text.length
			};
			EditableCaret.prototype.getPos = function() {
				var clonedRange, pos, range;
				if (range = this.range()) {
					clonedRange = range.cloneRange();
					clonedRange.selectNodeContents(this.domInputor);
					clonedRange.setEnd(range.endContainer, range.endOffset);
					pos = clonedRange.toString().length;
					clonedRange.detach();
					return pos
				} else if (document.selection) {
					return this.getOldIEPos()
				}
			};
			EditableCaret.prototype.getOldIEOffset = function() {
				var range, rect;
				range = document.selection.createRange().duplicate();
				range.moveStart("character", -1);
				rect = range.getBoundingClientRect();
				return {
					height: rect.bottom - rect.top,
					left: rect.left,
					top: rect.top
				}
			};
			EditableCaret.prototype.getOffset = function(pos) {
				var clonedRange, offset, range, rect;
				offset = null;
				if (window.getSelection && (range = this.range())) {
					if (range.endOffset - 1 < 0) {
						return null
					}
					clonedRange = range.cloneRange();
					clonedRange.setStart(range.endContainer, range.endOffset - 1);
					clonedRange.setEnd(range.endContainer, range.endOffset);
					rect = clonedRange.getBoundingClientRect();
					offset = {
						height: rect.height,
						left: rect.left + rect.width,
						top: rect.top
					};
					clonedRange.detach();
					offset
				} else if (document.selection) {
					this.getOldIEOffset()
				}
				return Utils.adjustOffset(offset, this.$inputor)
			};
			EditableCaret.prototype.range = function() {
				var sel;
				if (!window.getSelection) {
					return
				}
				sel = window.getSelection();
				if (sel.rangeCount > 0) {
					return sel.getRangeAt(0)
				} else {
					return null
				}
			};
			return EditableCaret
		})();
		InputCaret = (function() {
			function InputCaret($inputor) {
				this.$inputor = $inputor;
				this.domInputor = this.$inputor[0]
			}
			InputCaret.prototype.getIEPos = function() {
				var endRange, inputor, len, normalizedValue, pos, range, textInputRange;
				inputor = this.domInputor;
				range = document.selection.createRange();
				pos = 0;
				if (range && range.parentElement() === inputor) {
					normalizedValue = inputor.value.replace(/\r\n/g, "\n");
					len = normalizedValue.length;
					textInputRange = inputor.createTextRange();
					textInputRange.moveToBookmark(range.getBookmark());
					endRange = inputor.createTextRange();
					endRange.collapse(false);
					if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
						pos = len
					} else {
						pos = -textInputRange.moveStart("character", -len)
					}
				}
				return pos
			};
			InputCaret.prototype.getPos = function() {
				if (document.selection) {
					return this.getIEPos()
				} else {
					return this.domInputor.selectionStart
				}
			};
			InputCaret.prototype.setPos = function(pos) {
				var inputor, range;
				inputor = this.domInputor;
				if (document.selection) {
					range = inputor.createTextRange();
					range.move("character", pos);
					range.select()
				} else if (inputor.setSelectionRange) {
					inputor.setSelectionRange(pos, pos)
				}
				return inputor
			};
			InputCaret.prototype.getIEOffset = function(pos) {
				var h, range, textRange, x, y;
				textRange = this.domInputor.createTextRange();
				if (pos) {
					textRange.move('character', pos)
				} else {
					range = document.selection.createRange();
					textRange.moveToBookmark(range.getBookmark())
				}
				x = textRange.boundingLeft;
				y = textRange.boundingTop;
				h = textRange.boundingHeight;
				return {
					left: x,
					top: y,
					height: h
				}
			};
			InputCaret.prototype.getOffset = function(pos) {
				var $inputor, offset, position;
				$inputor = this.$inputor;
				if (document.selection) {
					return Utils.adjustOffset(this.getIEOffset(pos), $inputor)
				} else {
					offset = $inputor.offset();
					position = this.getPosition(pos);
					return offset = {
						left: offset.left + position.left,
						top: offset.top + position.top,
						height: position.height
					}
				}
			};
			InputCaret.prototype.getPosition = function(pos) {
				var $inputor, at_rect, format, html, mirror, start_range;
				$inputor = this.$inputor;
				format = function(value) {
					return value.replace(/</g, '&lt').replace(/>/g, '&gt').replace(/`/g, '&#96').replace(/"/g, '&quot').replace(/\r\n|\r|\n/g, "<br />")
				};
				if (pos === void 0) {
					pos = this.getPos()
				}
				start_range = $inputor.val().slice(0, pos);
				html = "<span>" + format(start_range) + "</span>";
				html += "<span id='caret'>|</span>";
				mirror = new Mirror($inputor);
				return at_rect = mirror.create(html).rect()
			};
			InputCaret.prototype.getIEPosition = function(pos) {
				var h, inputorOffset, offset, x, y;
				offset = this.getIEOffset(pos);
				inputorOffset = this.$inputor.offset();
				x = offset.left - inputorOffset.left;
				y = offset.top - inputorOffset.top;
				h = offset.height;
				return {
					left: x,
					top: y,
					height: h
				}
			};
			return InputCaret
		})();
		Mirror = (function() {
			Mirror.prototype.css_attr = ["overflowY", "height", "width", "paddingTop", "paddingLeft", "paddingRight", "paddingBottom", "marginTop", "marginLeft", "marginRight", "marginBottom", "fontFamily", "borderStyle", "borderWidth", "wordWrap", "fontSize", "lineHeight", "overflowX", "text-align"];

			function Mirror($inputor) {
				this.$inputor = $inputor
			}
			Mirror.prototype.mirrorCss = function() {
				var css, _this = this;
				css = {
					position: 'absolute',
					left: -9999,
					top: 0,
					zIndex: -20000,
					'white-space': 'pre-wrap'
				};
				$.each(this.css_attr, function(i, p) {
					return css[p] = _this.$inputor.css(p)
				});
				return css
			};
			Mirror.prototype.create = function(html) {
				this.$mirror = $('<div></div>');
				this.$mirror.css(this.mirrorCss());
				this.$mirror.html(html);
				this.$inputor.after(this.$mirror);
				return this
			};
			Mirror.prototype.rect = function() {
				var $flag, pos, rect;
				$flag = this.$mirror.find("#caret");
				pos = $flag.position();
				rect = {
					left: pos.left,
					top: pos.top,
					height: $flag.height()
				};
				this.$mirror.remove();
				return rect
			};
			return Mirror
		})();
		Utils = {
			adjustOffset: function(offset, $inputor) {
				if (!offset) {
					return
				}
				offset.top += $(window).scrollTop() + $inputor.scrollTop();
				offset.left += +$(window).scrollLeft() + $inputor.scrollLeft();
				return offset
			},
			contentEditable: function($inputor) {
				return !!($inputor[0].contentEditable && $inputor[0].contentEditable === 'true')
			}
		};
		methods = {
			pos: function(pos) {
				if (pos) {
					return this.setPos(pos)
				} else {
					return this.getPos()
				}
			},
			position: function(pos) {
				if (document.selection) {
					return this.getIEPosition(pos)
				} else {
					return this.getPosition(pos)
				}
			},
			offset: function(pos) {
				return this.getOffset(pos)
			}
		};
		$.fn.caret = function(method) {
			var caret;
			caret = Utils.contentEditable(this) ? new EditableCaret(this) : new InputCaret(this);
			if (methods[method]) {
				return methods[method].apply(caret, Array.prototype.slice.call(arguments, 1))
			} else {
				return $.error("Method " + method + " does not exist on jQuery.caret")
			}
		};
		$.fn.caret.EditableCaret = EditableCaret;
		$.fn.caret.InputCaret = InputCaret;
		$.fn.caret.Utils = Utils;
		return $.fn.caret.apis = methods
	})
}).call(this);
(function() {
	var __slice = [].slice;
	(function(factory) {
		if (typeof define === 'function' && define.amd) {
			return define(['jquery'], factory)
		} else {
			return factory(window.jQuery)
		}
	})(function($) {
		var $CONTAINER, Api, App, Atwho, Controller, DEFAULT_CALLBACKS, KEY_CODE, Model, View;
		App = (function() {
			function App(inputor) {
				this.current_flag = null;
				this.controllers = {};
				this.$inputor = $(inputor);
				this.listen()
			}
			App.prototype.controller = function(key) {
				return this.controllers[key || this.current_flag]
			};
			App.prototype.set_context_for = function(key) {
				this.current_flag = key;
				return this
			};
			App.prototype.reg = function(flag, setting) {
				var controller, _base;
				controller = (_base = this.controllers)[flag] || (_base[flag] = new Controller(this, flag));
				if (setting.alias) {
					this.controllers[setting.alias] = controller
				}
				controller.init(setting);
				return this
			};
			App.prototype.listen = function() {
				var _this = this;
				return this.$inputor.on('keyup.atwho', function(e) {
					return _this.on_keyup(e)
				}).on('keydown.atwho', function(e) {
					return _this.on_keydown(e)
				}).on('scroll.atwho', function(e) {
					var _ref;
					return (_ref = _this.controller()) != null ? _ref.view.hide() : void 0
				}).on('blur.atwho', function(e) {
					var c;
					if (c = _this.controller()) {
						return c.view.hide(c.get_opt("display_timeout"))
					}
				})
			};
			App.prototype.dispatch = function() {
				var _this = this;
				return $.map(this.controllers, function(c) {
					if (c.look_up()) {
						return _this.set_context_for(c.key)
					}
				})
			};
			App.prototype.on_keyup = function(e) {
				var _ref;
				switch (e.keyCode) {
				case KEY_CODE.ESC:
					e.preventDefault();
					if ((_ref = this.controller()) != null) {
						_ref.view.hide()
					}
					break;
				case KEY_CODE.DOWN:
				case KEY_CODE.UP:
					$.noop();
					break;
				default:
					this.dispatch()
				}
			};
			App.prototype.on_keydown = function(e) {
				var view, _ref;
				view = (_ref = this.controller()) != null ? _ref.view : void 0;
				if (!(view && view.visible())) {
					return
				}
				switch (e.keyCode) {
				case KEY_CODE.ESC:
					e.preventDefault();
					view.hide();
					break;
				case KEY_CODE.UP:
					e.preventDefault();
					view.prev();
					break;
				case KEY_CODE.DOWN:
					e.preventDefault();
					view.next();
					break;
				case KEY_CODE.TAB:
				case KEY_CODE.ENTER:
					if (!view.visible()) {
						return
					}
					e.preventDefault();
					view.choose();
					break;
				default:
					$.noop()
				}
			};
			return App
		})();
		Controller = (function() {
			var uuid, _uuid;
			_uuid = 0;
			uuid = function() {
				return _uuid += 1
			};

			function Controller(app, key) {
				this.app = app;
				this.key = key;
				this.at = this.key;
				this.$inputor = this.app.$inputor;
				this.id = this.$inputor[0].id || uuid();
				this.setting = null;
				this.query = null;
				this.pos = 0;
				this.cur_rect = null;
				this.range = null;
				$CONTAINER.append(this.$el = $("<div id='atwho-ground-" + this.id + "'></div>"));
				this.model = new Model(this);
				this.view = new View(this)
			}
			Controller.prototype.init = function(setting) {
				this.setting = $.extend({}, this.setting || $.fn.atwho["default"], setting);
				return this.model.reload(this.setting.data)
			};
			Controller.prototype.call_default = function() {
				var args, func_name;
				func_name = arguments[0], args = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
				try {
					return DEFAULT_CALLBACKS[func_name].apply(this, args)
				} catch (error) {
					return $.error("" + error + " Or maybe At.js doesn't have function " + func_name)
				}
			};
			Controller.prototype.trigger = function(name, data) {
				var alias, event_name;
				data.push(this);
				alias = this.get_opt('alias');
				event_name = alias ? "" + name + "-" + alias + ".atwho" : "" + name + ".atwho";
				return this.$inputor.trigger(event_name, data)
			};
			Controller.prototype.callbacks = function(func_name) {
				return this.get_opt("callbacks")[func_name] || DEFAULT_CALLBACKS[func_name]
			};
			Controller.prototype.get_opt = function(key, default_value) {
				try {
					return this.setting[key]
				} catch (e) {
					return null
				}
			};
			Controller.prototype.content = function() {
				if (this.$inputor.is('textarea, input')) {
					return this.$inputor.val()
				} else {
					return this.$inputor.text()
				}
			};
			Controller.prototype.catch_query = function() {
				var caret_pos, content, end, query, start, subtext;
				content = this.content();
				caret_pos = this.$inputor.caret('pos');
				subtext = content.slice(0, caret_pos);
				query = this.callbacks("matcher").call(this, this.key, subtext, this.get_opt('start_with_space'));
				if (typeof query === "string" && query.length <= this.get_opt('max_len', 20)) {
					start = caret_pos - query.length;
					end = start + query.length;
					this.pos = start;
					query = {
						'text': query.toLowerCase(),
						'head_pos': start,
						'end_pos': end
					};
					this.trigger("matched", [this.key, query.text])
				} else {
					this.view.hide()
				}
				return this.query = query
			};
			Controller.prototype.rect = function() {
				var c, scale_bottom;
				if (!(c = this.$inputor.caret('offset', this.pos - 1))) {
					return
				}
				if (this.$inputor.attr('contentEditable') === 'true') {
					c = (this.cur_rect || (this.cur_rect = c)) || c
				}
				scale_bottom = document.selection ? 0 : 2;
				return {
					left: c.left,
					top: c.top,
					bottom: c.top + c.height + scale_bottom
				}
			};
			Controller.prototype.reset_rect = function() {
				if (this.$inputor.attr('contentEditable') === 'true') {
					return this.cur_rect = null
				}
			};
			Controller.prototype.mark_range = function() {
				return this.range = this.get_range() || this.get_ie_range()
			};
			Controller.prototype.clear_range = function() {
				return this.range = null
			};
			Controller.prototype.get_range = function() {
				return this.range || (window.getSelection ? window.getSelection().getRangeAt(0) : void 0)
			};
			Controller.prototype.get_ie_range = function() {
				return this.range || (document.selection ? document.selection.createRange() : void 0)
			};
			Controller.prototype.insert_content_for = function($li) {
				var data, data_value, tpl;
				data_value = $li.data('value');
				tpl = this.get_opt('insert_tpl');
				if (this.$inputor.is('textarea, input') || !tpl) {
					return data_value
				}
				data = $.extend({}, $li.data('item-data'), {
					'atwho-data-value': data_value,
					'atwho-at': this.at
				});
				return this.callbacks("tpl_eval").call(this, tpl, data)
			};
			Controller.prototype.insert = function(content, $li) {
				var $inputor, $insert_node, class_name, content_node, insert_node, pos, range, sel, source, start_str, text;
				$inputor = this.$inputor;
				if ($inputor.attr('contentEditable') === 'true') {
					class_name = "atwho-view-flag atwho-view-flag-" + (this.get_opt('alias') || this.at);
					content_node = "" + content + "<span contenteditable='false'>&nbsp;<span>";
					insert_node = "<span contenteditable='false' class='" + class_name + "'>" + content_node + "</span>";
					$insert_node = $(insert_node).data('atwho-data-item', $li.data('item-data'));
					if (document.selection) {
						$insert_node = $("<span contenteditable='true'></span>").html($insert_node)
					}
				}
				if ($inputor.is('textarea, input')) {
					content = '' + content;
					source = $inputor.val();
					start_str = source.slice(0, Math.max(this.query.head_pos - this.at.length, 0));
					text = "" + start_str + content + " " + (source.slice(this.query['end_pos'] || 0));
					$inputor.val(text);
					$inputor.caret('pos', start_str.length + content.length + 1)
				} else if (range = this.get_range()) {
					pos = range.startOffset - (this.query.end_pos - this.query.head_pos) - this.at.length;
					range.setStart(range.endContainer, Math.max(pos, 0));
					range.setEnd(range.endContainer, range.endOffset);
					range.deleteContents();
					range.insertNode($insert_node[0]);
					range.collapse(false);
					sel = window.getSelection();
					sel.removeAllRanges();
					sel.addRange(range)
				} else if (range = this.get_ie_range()) {
					range.moveStart('character', this.query.end_pos - this.query.head_pos - this.at.length);
					range.pasteHTML($insert_node[0]);
					range.collapse(false);
					range.select()
				}
				$inputor.focus();
				return $inputor.change()
			};
			Controller.prototype.render_view = function(data) {
				var search_key;
				search_key = this.get_opt("search_key");
				data = this.callbacks("sorter").call(this, this.query.text, data.slice(0, 1001), search_key);
				return this.view.render(data.slice(0, this.get_opt('limit')))
			};
			Controller.prototype.look_up = function() {
				var query, _callback;
				if (!(query = this.catch_query())) {
					return
				}
				_callback = function(data) {
					if (data && data.length > 0) {
						return this.render_view(data)
					} else {
						return this.view.hide()
					}
				};
				this.model.query(query.text, $.proxy(_callback, this));
				return query
			};
			return Controller
		})();
		Model = (function() {
			var _storage;
			_storage = {};

			function Model(context) {
				this.context = context;
				this.key = this.context.key
			}
			Model.prototype.saved = function() {
				return this.fetch() > 0
			};
			Model.prototype.query = function(query, callback) {
				var data, search_key, _ref;
				data = this.fetch();
				search_key = this.context.get_opt("search_key");
				callback(data = this.context.callbacks('filter').call(this.context, query, data, search_key));
				if (!(data && data.length > 0)) {
					return (_ref = this.context.callbacks('remote_filter')) != null ? _ref.call(this.context, query, callback) : void 0
				}
			};
			Model.prototype.fetch = function() {
				return _storage[this.key] || []
			};
			Model.prototype.save = function(data) {
				return _storage[this.key] = this.context.callbacks("before_save").call(this.context, data || [])
			};
			Model.prototype.load = function(data) {
				if (!(this.saved() || !data)) {
					return this._load(data)
				}
			};
			Model.prototype.reload = function(data) {
				return this._load(data)
			};
			Model.prototype._load = function(data) {
				var _this = this;
				if (typeof data === "string") {
					return $.ajax(data, {
						dataType: "json"
					}).done(function(data) {
						return _this.save(data)
					})
				} else {
					return this.save(data)
				}
			};
			return Model
		})();
		View = (function() {
			function View(context) {
				this.context = context;
				this.key = this.context.key;
				this.id = this.context.get_opt("alias") || ("at-view-" + (this.key.charCodeAt(0)));
				this.$el = $("<div id='" + this.id + "' class='atwho-view'><ul id='" + this.id + "-ul' class='atwho-view-url'></ul></div>");
				this.timeout_id = null;
				this.context.$el.append(this.$el);
				this.bind_event()
			}
			View.prototype.bind_event = function() {
				var $menu, _this = this;
				$menu = this.$el.find('ul');
				$menu.on('mouseenter.atwho-view', 'li', function(e) {
					$menu.find('.cur').removeClass('cur');
					return $(e.currentTarget).addClass('cur')
				}).on('click', function(e) {
					_this.choose();
					return e.preventDefault()
				});
				return this.$el.on('mouseenter.atwho-view', 'ul', function(e) {
					return _this.context.mark_range()
				}).on('mouseleave.atwho-view', 'ul', function(e) {
					return _this.context.clear_range()
				})
			};
			View.prototype.visible = function() {
				return this.$el.is(":visible")
			};
			View.prototype.choose = function() {
				var $li, content;
				$li = this.$el.find(".cur");
				content = this.context.insert_content_for($li);
				this.context.insert(this.context.callbacks("before_insert").call(this.context, content, $li), $li);
				this.context.trigger("inserted", [$li]);
				return this.hide()
			};
			View.prototype.reposition = function(rect) {
				var offset;
				if (rect.bottom + this.$el.height() - $(window).scrollTop() > $(window).height()) {
					rect.bottom = rect.top - this.$el.height()
				}
				offset = {
					left: rect.left,
					top: rect.bottom
				};
				this.$el.offset(offset);
				return this.context.trigger("reposition", [offset])
			};
			View.prototype.next = function() {
				var cur, next;
				cur = this.$el.find('.cur').removeClass('cur');
				next = cur.next();
				if (!next.length) {
					next = this.$el.find('li:first')
				}
				return next.addClass('cur')
			};
			View.prototype.prev = function() {
				var cur, prev;
				cur = this.$el.find('.cur').removeClass('cur');
				prev = cur.prev();
				if (!prev.length) {
					prev = this.$el.find('li:last')
				}
				return prev.addClass('cur')
			};
			View.prototype.show = function() {
				var rect;
				if (!this.visible()) {
					this.$el.show()
				}
				if (rect = this.context.rect()) {
					return this.reposition(rect)
				}
			};
			View.prototype.hide = function(time) {
				var callback, _this = this;
				if (isNaN(time && this.visible())) {
					this.context.reset_rect();
					return this.$el.hide()
				} else {
					callback = function() {
						return _this.hide()
					};
					clearTimeout(this.timeout_id);
					return this.timeout_id = setTimeout(callback, time)
				}
			};
			View.prototype.render = function(list) {
				var $li, $ul, item, li, tpl, _i, _len;
				if (!$.isArray(list || list.length <= 0)) {
					this.hide();
					return
				}
				this.$el.find('ul').empty();
				$ul = this.$el.find('ul');
				tpl = this.context.get_opt('tpl');
				for (_i = 0, _len = list.length; _i < _len; _i++) {
					item = list[_i];
					item = $.extend({}, item, {
						'atwho-at': this.context.at
					});
					li = this.context.callbacks("tpl_eval").call(this.context, tpl, item);
					$li = $(this.context.callbacks("highlighter").call(this.context, li, this.context.query.text));
					$li.data("item-data", item);
					$ul.append($li)
				}
				this.show();
				return $ul.find("li:first").addClass("cur")
			};
			return View
		})();
		KEY_CODE = {
			DOWN: 40,
			UP: 38,
			ESC: 27,
			TAB: 9,
			ENTER: 13
		};
		DEFAULT_CALLBACKS = {
			before_save: function(data) {
				var item, _i, _len, _results;
				if (!$.isArray(data)) {
					return data
				}
				_results = [];
				for (_i = 0, _len = data.length; _i < _len; _i++) {
					item = data[_i];
					if ($.isPlainObject(item)) {
						_results.push(item)
					} else {
						_results.push({
							name: item
						})
					}
				}
				return _results
			},
			matcher: function(flag, subtext, should_start_with_space) {
				var match, regexp;
				flag = flag.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
				if (should_start_with_space) {
					flag = '(?:^|\\s)' + flag
				}
				regexp = new RegExp(flag + '([A-Za-z0-9_\+\-]*)$|' + flag + '([^\-\ÿ]*)$', 'gi');
				match = regexp.exec(subtext);
				if (match) {
					return match[2] || match[1]
				} else {
					return null
				}
			},
			filter: function(query, data, search_key) {
				var item, _i, _len, _results;
				_results = [];
				for (_i = 0, _len = data.length; _i < _len; _i++) {
					item = data[_i];
					if (~item[search_key].toLowerCase().indexOf(query)) {
						_results.push(item)
					}
				}
				return _results
			},
			remote_filter: null,
			sorter: function(query, items, search_key) {
				var item, _i, _len, _results;
				if (!query) {
					return items
				}
				_results = [];
				for (_i = 0, _len = items.length; _i < _len; _i++) {
					item = items[_i];
					item.atwho_order = item[search_key].toLowerCase().indexOf(query);
					if (item.atwho_order > -1) {
						_results.push(item)
					}
				}
				return _results.sort(function(a, b) {
					return a.atwho_order - b.atwho_order
				})
			},
			tpl_eval: function(tpl, map) {
				try {
					return tpl.replace(/\$\{([^\}]*)\}/g, function(tag, key, pos) {
						return map[key]
					})
				} catch (error) {
					return ""
				}
			},
			highlighter: function(li, query) {
				var regexp;
				if (!query) {
					return li
				}
				regexp = new RegExp(">\\s*(\\w*)(" + query.replace("+", "\\+") + ")(\\w*)\\s*<", 'ig');
				return li.replace(regexp, function(str, $1, $2, $3) {
					return '> ' + $1 + '<strong>' + $2 + '</strong>' + $3 + ' <'
				})
			},
			before_insert: function(value, $li) {
				return value
			}
		};
		Api = {
			load: function(key, data) {
				var c;
				if (c = this.controller(key)) {
					return c.model.load(data)
				}
			},
			getInsertedItemsWithIDs: function(key) {
				var c, ids, items;
				if (!(c = this.controller(key))) {
					return [null, null]
				}
				if (key) {
					key = "-" + (c.get_opt('alias') || c.at)
				}
				ids = [];
				items = $.map(this.$inputor.find("span.atwho-view-flag" + (key || "")), function(item) {
					var data;
					data = $(item).data('atwho-data-item');
					if (ids.indexOf(data.id) > -1) {
						return
					}
					if (data.id) {
						ids.push = data.id
					}
					return data
				});
				return [ids, items]
			},
			getInsertedItems: function(key) {
				return Api.getInsertedItemsWithIDs.apply(this, [key])[1]
			},
			getInsertedIDs: function(key) {
				return Api.getInsertedItemsWithIDs.apply(this, [key])[0]
			},
			run: function() {
				return this.dispatch()
			}
		};
		Atwho = {
			init: function(options) {
				var $this, app;
				app = ($this = $(this)).data("atwho");
				if (!app) {
					$this.data('atwho', (app = new App(this)))
				}
				app.reg(options.at, options);
				return this
			}
		};
		$CONTAINER = $("<div id='atwho-container'></div>");
		$.fn.atwho = function(method) {
			var result, _args;
			_args = arguments;
			$('body').append($CONTAINER);
			result = null;
			this.filter('textarea, input, [contenteditable=true]').each(function() {
				var app;
				if (typeof method === 'object' || !method) {
					return Atwho.init.apply(this, _args)
				} else if (Api[method]) {
					if (app = $(this).data('atwho')) {
						return result = Api[method].apply(app, Array.prototype.slice.call(_args, 1))
					}
				} else {
					return $.error("Method " + method + " does not exist on jQuery.caret")
				}
			});
			return result || this
		};
		return $.fn.atwho["default"] = {
			at: void 0,
			alias: void 0,
			data: null,
			tpl: "<li data-value='${atwho-at}${name}'>${name}</li>",
			insert_tpl: "<span>${atwho-data-value}</span>",
			callbacks: DEFAULT_CALLBACKS,
			search_key: "name",
			start_with_space: true,
			limit: 5,
			max_len: 20,
			display_timeout: 300
		}
	})
}).call(this);
!(function($) {
	var defaults = {
		'swipeTolerance': 40
	};
	var touchStatus = function(target, touch) {
			this.target = $(target);
			this.touch = touch;
			this.startX = this.currentX = touch.screenX;
			this.startY = this.currentY = touch.screenY;
			this.eventType = null;
		}
	touchStatus.options = {};
	touchStatus.latestTap = null;
	touchStatus.prototype.move = function(touch) {
		this.currentX = touch.screenX;
		this.currentY = touch.screenY;
	}

	touchStatus.prototype.process = function() {
		var offsetX = this.currentX - this.startX;
		var offsetY = this.currentY - this.startY;
		if (offsetX == 0 && offsetY == 0) {
			this.checkForDoubleTap();
		} else if (Math.abs(offsetY) > touchStatus.options.swipeTolerance && Math.abs(offsetY) > Math.abs(offsetX)) {
			this.eventType = offsetY > 0 ? 'swipedown' : 'swipeup';
			this.target.trigger('swipe', [this])
		} else if (Math.abs(offsetX) > touchStatus.options.swipeTolerance) {
			this.eventType = offsetX > 0 ? 'swiperight' : 'swipeleft';
			this.target.trigger('swipe', [this])
		}
		if (this.eventType) this.target.trigger(this.eventType, [this])
		this.target.trigger('touch', [this])
	}

	touchStatus.prototype.checkForDoubleTap = function() {
		if (touchStatus.latestTap) {
			if ((new Date() - touchStatus.latestTap) < 400) this.eventType = 'doubletap'
		}
		if (!this.eventType) this.eventType = 'tap'
		touchStatus.latestTap = new Date()
	}

	var swipeEvents = function(elements, options) {
			touchStatus.options = $.extend(defaults, options);
			elements.on('touchstart', this.touchStart);
			elements.on('touchmove', this.touchMove);
			elements.on('touchcancel', this.touchCancel);
			elements.on('touchend', this.touchEnd);
		}

	swipeEvents.prototype.touchStart = function(evt) {
		var target = this;
		swipeEvents.eachTouch(evt, function(touch) {
			swipeEvents.touches[touch.identifier] = new touchStatus(target, touch);
		})
	}

	swipeEvents.prototype.touchMove = function(evt) {
		swipeEvents.eachTouch(evt, function(touch) {
			var loc = swipeEvents.touches[touch.identifier]
			if (loc) loc.move(touch)
		})
	}

	swipeEvents.prototype.touchCancel = function(evt) {
		swipeEvents.eachTouch(evt, function(touch) {
			swipeEvents.purge(touch, true)
		})
	}

	swipeEvents.prototype.touchEnd = function(evt) {
		swipeEvents.eachTouch(evt, function(touch) {
			swipeEvents.purge(touch)
		})
	}

	swipeEvents.touches = {}
	swipeEvents.purge = function(touch, cancelled) {
		if (!cancelled) {
			var loc = swipeEvents.touches[touch.identifier]
			if (loc) loc.process()
		}
		delete swipeEvents.touches[touch.identifier]
	}

	swipeEvents.eachTouch = function(evt, callback) {
		var evt = evt.originalEvent;
		var num = evt.changedTouches.length;
		for (var i = 0; i < num; i++) {
			callback(evt.changedTouches[i])
		}
	}
	$.fn.addSwipeEvents = function(options, callback) {
		if (!callback && jQuery.isFunction(options)) {
			callback = options;
			options = null;
		}
		new swipeEvents(this, options);
		if (callback) this.on('touch', callback);
		return this;
	}
})(jQuery);

$(function() {

	var ua = navigator.userAgent;

	var ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
		isIphone = !ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
		isAndroid = ua.match(/(Android)\s+([\d.]+)/),
		isIE9 = ua.match(/msie 9\.0/i),
		isIE8 = ua.match(/msie 8\.0/i),
		isMobile = isIphone || isAndroid || ipad;
	if (isMobile) {
		window.location.href.indexOf("myfeed") != -1 ? $('#tab-my-m').addClass('tab-active') : $('#tab-home-m').addClass('tab-active');
	} else {
		window.location.href.indexOf("myfeed") != -1 ? $('#tab-my').addClass('tab-active') : $('#tab-home').addClass('tab-active');
	}

	if (isIE9 || isIE8) {
		$('body,html').append('<div class="mise-tip">您正在使用的<strong>IE浏览器版本过低</strong>推荐：Chrome,Opera,360等体验最佳效果。</div>');
	}

	var isTouch = ('ontouchstart' in window),
		click = isTouch ? 'touchstart' : 'click';

	if ($("body").data("autoload") == true && $('#tweet-list').data('have') != '0') {

		var page = 2,

			more = false,

			moreload = $('.get-data'),

			offsetList = $('#tweet-list').offset().top,

			waphead = $("#wap-head").html(),

			request = window.location.search,

			url = request != '' ? "?app=tweet&action=index" + request.replace('?', '&') + "&more&p=" : "?app=tweet&action=index&more&p=",

			load = "<div id=\"load\" style=\"text-align:center;line-height:70px;\"><img src=\"" + Path + "theme/style/loading.gif\"/></div>";


		$(window).scroll(function() {

			var h = $(document).height() - $(window).scrollTop() - document.documentElement.clientHeight;

			if (isMobile) {

				if (offsetList < $(window).scrollTop()) {

					$('#wap-head').html('全部话题');

				} else if (offsetList > $(window).scrollTop()) {

					$('#wap-head').html(waphead);
				}
			}

			if (h < 120) {

				if (page > 1 && more == false) {

					more = true;

					$.ajax({
						type: 'GET',

						url: Path + url + page,

						beforeSend: function() {

							moreload.append(load);

						},
						success: function(result) {


							if (result === '') {

								more = true;

							} else {

								moreload.append(result);
								page++;
								more = false;
							}

						},
						complete: function() {
							$('#load').remove();
						}
					})
				}
			}
		});
	}
	if (isMobile) {


		if ($('#bind').data("menu") == 'user') {

			var user_name = $('#bind').data("name");

			$('#wap-head').html(user_name);

			$('.wap-menu-btn').removeClass('wap-menu-list').addClass('wap-menu-back').html('<i class="icon icon-zuojiantou"></i>');

			$('.wap-menu-back').addSwipeEvents().on('touch', function() {

				history.back();

			});

		}

		$('.emoji').addSwipeEvents().on('touch', function() {

			if ($(this).find('.emot-put').css("display") == "none") {

				$(this).find('i').removeClass("icon-biaoqing").addClass('icon-jianpan');

				$(this).find(".emot-put").fadeIn(500);

				return false;

			} else {

				var that = $(this);

				setTimeout(function() {
					that.find(".emot-put").hide()
				}, 800);

				$(this).find('i').removeClass("icon-jianpan").addClass('icon-biaoqing');

				return false;
			}

		});

		$('.set').addSwipeEvents().on('touch', function() {

			$('.wap-set').slideToggle(200);

		});

		$(document).on("click", ".data-box-reply", function(e) {

			e.preventDefault();

			var tid = $(this).attr("data-id");

			var name = $(this).attr("data-name");

			var child = $(this).attr("data-child");

			var loc = $(this).attr('data-loc');

			var nid = $(this).attr('data-nid');

			add_wap_reply(tid, name, child, loc, nid);

		});

		var isMenuOpen = false;

		var Menuspeed = 150;

		$('.wap-menu-list').on('touchstart', function(e) {

			if (isMenuOpen == false) {

				$(".wap-menu").clearQueue().animate({
					width: '250px'
				}, Menuspeed);

				$(this).hide();

				$(".wap-menu-close").show();

				isMenuOpen = true;
			}

		});

		$('.wap-menu-close').on('click', function(e) {

			if (isMenuOpen == true) {

				$(".wap-menu").clearQueue().animate({
					width: '0px'
				}, Menuspeed);

				$(this).hide();

				$(".wap-menu-list").show();

				isMenuOpen = false;
			}
		});

		if (isLogin()) {
			var not = $('.user_info').data('not');
			if (not > 0) {
				$('html,body').append('<a class="wap-not" href="' + Path + '?app=tweet&action=notice&do=get&isread=1&read">您有' + not + '条未读提醒</a>');
			}
		}

	} else { // PC javascript

		$(document).on("click", ".emoji", function(e) {

			e.preventDefault();
			e.stopPropagation();

			$(this).find(".emot-put").fadeIn();

			$(document).on("click", function() {

				$(".emot-put").hide();
			});

		});

		$(document).on("mouseover", ".reply-tip,.liker,.edit-tool-box", function() {

			$('.reply-tip,.liker,.edit-tool-box').tipTip();

		});

		var onMenu = $('#bind').attr("data-menu");

		if (onMenu) $('#menu-' + onMenu).addClass("menu-active");

		scrolltop();

		//comment event

		$(document).on("click", ".data-box-reply", function(e) {

			e.preventDefault();

			var tid = $(this).attr("data-id");

			var name = $(this).attr("data-name");

			var child = $(this).attr("data-child");

			var loc = $(this).attr('data-loc');

			var nid = $(this).attr('data-nid');

			var template = '<li id="reply-form" class="tweet-inner"><div class="reply-form"><div class="reply-content"><div class="reply-name">回应 ' + name + ':</div>' + '<input type="text" maxlength="105" id="reply-content" class="reply-text" onkeydown="if(event.ctrlKey&&event.keyCode==13 || event.keyCode==10){document.getElementById(\'reply-submit\').click();return false}"/>' + '</div>' + '<div class="pr"><div class="emoji" style="margin-top:3px;"><i class="icon icon-biaoqing"></i><div class="emot-put"><div id="In_rep" class="emot-in"></div><div class="arrow"></div></div></div>' + '<input type="submit" id="reply-submit" class="btn btn-primary reply-submit" value="回应">' + '<div class="clear"></div></div>' + '</div></li>';

			$('#reply-form').remove();

			if (child == 0) {

				$(this).parent().next().find('.reply-list').append(template);

			} else if (nid) {

				$(this).parent().next().find('.reply-list').append(template);

			} else {

				$(this).parent().after(template);
			}

			var Namewidth = $("#reply-content").parent().find(".reply-name").width() + 10;

			$(".reply-text").focus();

			$("#reply-content").css({
				'padding-left': Namewidth
			});

			$("#In_rep").html(emoji).find("img").each(function(i) {

				$(this).on("click", function(e) {

					e.preventDefault();

					editorInsert('[e:' + i + ']', 'reply-content');

				});

			});

			$("#reply-submit").on("click", function(e) {

				e.preventDefault();

				var content = $.trim($("#reply-content").val());
				var data = {
					content: content,
					tid: tid,
					parent_id: child
				};

				loc == 1 ? postReply(data, tid, loc, 1) : postReply(data, tid, loc);


			});

		}); //comment end

		var atNotice = false;

		$("#GetNotice").off("mouseenter").on("mouseenter", function() {

			$.get(Path + '?app=tweet&action=notice&do=get&isread=1&json', function(json) {
				var html = '';

				//console.log(json);

				for (var i in json.result) {
					html += noticeList(json.result[i]);
				}

				$('.no').html(html);

				$(".notice-more").fadeIn().off("mouseenter").on("mouseenter", function() {

					atNotice = true;

				}).off("mouseleave").on("mouseleave", function() {

					atNotice = false;

					$(this).hide();
				});

			}, "json");

		}).off("mouseleave").on("mouseleave", function() {

			setTimeout(function() {
				if (!atNotice) {
					$('.notice-more').hide();
					atNotice = false;
				}
			}, 200);

		});

		// desk notifications

		if (isLogin()) {

			if (window.webkitNotifications) {

				var perm = window.webkitNotifications.checkPermission();

				if (perm != 0) {

					$('#GetNotice').on("click", function(e) {

						e.preventDefault();

						window.Notification.requestPermission(function(status) {

							location.href = "/?app=tweet&action=notice&do=get&isread=1&read";

							return false;

						});

					});

				} else if (perm == 0) {

					setInterval(function() {

						$.get(Path + '?app=tweet&action=notice&do=get&isread=1&json', function(json) {

							if (json.result) {

								for (var i in json.result) {

									if (json.result[i].flag == 0) {
										flag = "提到了你";
									} else if (json.result[i].flag == 1) {
										flag = "评论了我的动态";
									} else if (json.result[i].flag == 2) {
										flag = "回复了我的评论";
									}
									var n = new Notification(json.result[i].user_name + " " + flag, {
										body: json.result[i].content,
										tag: "notice" + i,
										icon: "" + json.result[i].user_avatar + ""
									});
									//n.onshow = function () {setTimeout(this.close.bind(this), 3000);}

								}
							}

						});

					}, 1000 * 60 * 4);
				}

			} // notification end

		}

		if ($.trim($('#tweet-content').val()).length < 1) {

			if ($("#tweet-submit").attr("disabled") != "disabled") $("#tweet-submit").attr("disabled", "disabled");
		}

		if ($('#bind').data("menu") == 'user') {

			var _year = 0,
				old_top = $("#tweet-list").offset().top;
			$(window).scroll(function() {

				var _this = $(this),
					_top = _this.scrollTop();

				if (_top >= old_top - 50) {
					$(".timeline").css({
						top: 60
					})
				} else {
					$(".timeline").css({
						top: old_top - _top - 36
					})
				}
				$(".tweet").each(function() {
					var _this = $(this),
						_date = _this.data('time'),
						_newyear = parseInt(_date.replace(/(\d*)-\d*/, "$1")),
						_offtop = _this.offset().top,
						_oph = _offtop + _this.height();
					if (_top >= _offtop && _top < _oph) {
						if (_newyear != _year) {
							$(".year").removeClass("year-active");
							$("#year-" + _newyear).addClass("year-active");
							_year = _newyear;
						}
						$('.mon').removeClass("selected");
						$('#mon-' + _date).addClass("selected");
					}

				});
			});

		}

	} // ua end

	$.get(Path + '?app=tweet&action=my_friend', function(json) {

		if (json.result) {

			var data = json.result,

				b = $.map(data, function(data) {

					return {
						id: data.id,
						name: data.name
					}

				});

			$("textarea").atwho({

				at: "@",

				data: b,

				tpl: "<li data-value='@${name}'>${name}</li>"

			});

		}

	}, 'json');

	$('.follow-on').on(click, function() {
		var that = $(this);
		var user_id = that.data("id");
		$.get(Path + '?app=tweet&action=follow_on&user_id=' + user_id, function(json) {
			if (json.result == '0') {
				window.location.href = Path + "signup";
			}
			if (json.result == '1') {
				that.html("取消关注");
				that.removeClass("follow-on").addClass("folllow-off");
			}
		}, 'json');
	});

	$('.follow-off').on(click, function() {
		var that = $(this);
		var user_id = that.data("id");
		$.get(Path + '?app=tweet&action=follow_off&user_id=' + user_id, function(json) {
			if (json.result == '0') {
				window.location.href = Path + "signup";
			}
			if (json.result == '1') {
				that.html("关注");
				that.removeClass("follow-off").addClass("folllow-on");
			}
		}, 'json');
	});
	$("#b-tool").on("click", function(e) {

		e.preventDefault();

		editorInsert('[b]这里写加粗文本[/b]', 'tweet-content');

	});
	$("#format-tool").on("click", function(e) {

		e.preventDefault();

		format('tweet-content');

	});
	$("#quote-tool").on("click", function(e) {

		e.preventDefault();

		editorInsert('[quote]这里写引用文本[/quote]', 'tweet-content');

	});
	$('.add-title').on(click, function() {

		$('#tweet-title,.edit-tool').fadeToggle();

	});

	$("#In_dex").html(emoji).find("img").each(function(i) {

		$(this).on("click", function(e) {

			e.preventDefault();

			editorInsert('[e:' + i + ']', 'tweet-content');

		});

	});

	$("#In_te").html(emoji).find("img").each(function(i) {

		$(this).on("click", function(e) {

			e.preventDefault();

			editorInsert('[e:' + i + ']', 'reply-this-content');

		});

	});

	$("#tweet-reply-this").on(click, function(e) {

		e.preventDefault();

		var tid = $(this).attr("data-id");

		var content = $.trim($("#reply-this-content").val());

		var data = {
			content: content,
			tid: tid
		};

		postReply(data, tid, 1);

	});

	$(document).on(click, ".like", function(e) {

		e.preventDefault();

		var id = $(this).attr("data-id");

		like(id);

	});

	reply_hash();

	var search = $("#search"),

		keyword = $.trim($("#search-text").val());

	if (keyword != '') {

		$('#search-text').click(function() {

			$(this).val('');

		});

	}

	var searchSubmit = function() {

			var keyword = $.trim($("#search-text").val());

			if (keyword == '') {

				tip('<i class="icon icon-xinxi"></i>关键字不能为空');

				$("#search-text").focus();

				return false;
			}

			keyword = keyword.replace(/\'/gi, "");
			keyword = keyword.replace(/\"/gi, "");
			keyword = keyword.replace(/\?/gi, "");
			keyword = keyword.replace(/\%/gi, "");
			keyword = keyword.replace(/\./gi, "");
			keyword = keyword.replace(/\*/gi, "");

			location.href = Path + "index.php?app=tweet&action=tag&keywords=" + encodeURI(keyword);
		};

	$(".search-btn").on("click", function(e) {

		e.preventDefault();
		searchSubmit();

	});

	$("#search-text").on("keydown", function(e) {

		if (e.keyCode == 13) {

			searchSubmit();
		}

	});

	var $content = $('#tweet-content'),

		$file = $('#tweet-file');

	$(".at-user").on("click", function(e) {

		e.preventDefault();

		var name = $(this).data("name");

		if (isLogin()) {
			$content.val($content.val().replace("@" + name + " ", " ") + "@" + name + " ");

			$(this).html('已添加');

		} else {

			tip('<i class="icon icon-xinxi"></i> 请先登录~');
		}

	});

	$('#upload-img').on(click, function(e) {

		var that = $(this);
		if ($('#fileList .file-list').length < 5) {

			$(this).Fileupload({

				url: Path + '?app=tweet&action=upload&do=put',

				maxFileSize: 4326000,

				allowedTypes: 'image/*',

				dataType: 'json',

				onBeforeUpload: function(id) {

					$('#uploadFile' + id).find('.file-name').html('照片正在上传中...');

				},
				onNewFile: function(id, file) {

					add_file(id, file);

					var images = getObjectURL(file);

					if (images) $('#uploadFile' + id).find('.file-image').attr("src", images);

				},
				onUploadProgress: function(id, percent) {

					var percentStr = percent + '%';

					update_file_progress(id, percentStr);

				},
				onUploadSuccess: function(id, data) {

					$file.val($file.val().replace(data.message + ",", "") + data.message + ",");

					update_file_progress(id, '100%');

					$('#uploadFile' + id).find('.file-name').html('照片上传完成！');

					$('#uploadFile' + id).find('.file-image').attr("src", Path + "upload/files/" + data.message);

					$('#uploadFile' + id).find('.file-del,.file-rotate').fadeIn(500);

					$('#uploadFile' + id).find('.file-rotate').attr("data-id", data.message);

					$('#uploadFile' + id).find('.file-del').attr("data-id", data.message + ",");

					$('#uploadFile' + id).find('.file-del').click(function() {

						var picfile = $(this).attr("data-id");

						$file.val($file.val().replace(picfile, ""));

						$('#uploadFile' + id).remove();

					});

					$('#uploadFile' + id).find('.file-rotate').on(click, function() {

						var turl = Path + '?app=tweet&action=upload&do=rotate';

						var picfile = 'upload/files/' + $(this).attr("data-id");

						$.post(turl, {
							'image': picfile
						}, function(json) {

							$('#uploadFile' + id).find('.file-image').attr("src", json.message + "?" + Math.random());

							tip('旋转成功!');

						}, "json");

					});

				},
				onFileTypeError: function(file) {

					tip('<i class="icon icon-xinxi"></i>抱歉~图片格式不被允许！');

				},
				onFileSizeError: function(file) {

					tip('<i class="icon icon-xinxi"></i>照片太大了哦~');

				},
				onFallbackMode: function(message) {

					tip('您的浏览器版本过旧，建议使用谷歌浏览器');
				}

			});

		} else {
			tip('<i class="icon icon-xinxi"></i>最大上传数5张');

			that.find('input').attr("disabled", "disabled");
		}
	});

	$content.keyup(function() {

		var len = $.trim($content.val()).length;

		if (len < 1) {

			if ($("#tweet-submit").attr("disabled") != "disabled") {

				$("#tweet-submit").attr("disabled", "disabled");

			}

		} else {

			if ($("#tweet-submit").attr("disabled") == "disabled") {

				$("#tweet-submit").removeAttr("disabled");

			}

		}

	});

	$("#tweet-submit").on(click, function(e) {

		e.preventDefault();

		var title = $.trim($("#tweet-title").val());

		var content = $("#tweet-content").val();

		var file = $.trim($("#tweet-file").val());

		var status = $("#tb-status:checked").val();

		var wb = $("#tb-wb:checked").val();


		if (content == '' || content.length < 3) {

			tip('<i class="icon icon-xinxi"></i>内容太少了哦！');

			$("#tweet-content").focus();

			return false;
		}
		var url = Path + "?app=tweet&action=push";
		var data = {
			title: title,
			content: content,
			file: file,
			wb: wb,
			status: status
		};
		var that = $(this);

		that.attr("disabled", "disabled");

		that.html("提交中...");

		$('#update_tweet_num,#update_album_num').removeClass('am-animation-scale-down');

		$.post(url, data, function(json) {

			if (json.result.error == 0) {

				tip("<i class='icon icon-xinxi'></i>请先登录~");

				return false;
			}
			if (json.result.error == 1) {

				tip("<i class='icon icon-xinxi'></i>内容太少了哦！");

				that.removeAttr("disabled");

				return false;
			}
			if (json.result.error == 2) {
				tip("<i class='icon icon-xinxi'></i>您输入的内容含有敏感词汇！！");

				that.removeAttr("disabled");

				that.html("修改重发");

				return false;
			}
			if (json.result.error == 3) {

				tip("<i class='icon icon-xinxi'></i>您被禁言，请与网站管理员联系！");

				that.html("发送失败");

				return false;
			}
			if (json.success) {

				var html = '<li id="tweet-' + json.result.id + '" class="tweet am-animation-slide-top">';

				html += '<div class="tweet-inner"><span class="tweet-time">刚刚';

				if (json.result.status == 1) {
					html += '<i class="icon icon-suoding"></i>';
				}

				html += '</span><a class="tweet-user" href="' + Path + 'index.php?app=tweet&action=user&id=' + json.result.user_id + '">';

				if (json.result.user_avatar != '') {

					html += '<img class="tweet-avatar" src="' + json.result.user_avatar + '"/>';

					var user_avatar = json.result.user_avatar;

				} else {

					html += '<img class="tweet-avatar" src="' + Path + 'theme/style/avatar.jpg"/>';

					var user_avatar = Path + 'theme/style/avatar.jpg';

				}

				html += '<div class="tweet-name">' + json.result.user_name + '</div></a></div>'; //头部结束

				html += '<div class="tweet-inner tweet-text clearleft">';

				if (json.result.title != '') {

					html += '<h3 class="post-title">提问题说:<a class="a" href="' + Path + 'index.php?app=tweet&action=view&id=' + json.result.id + '">' + json.result.title + '</a></h3>';

				} else {

					html += '<p>' + json.result.content + '</p>';

				}

				html += '</div>';

				if (json.result.title == '') {

					if (json.result.images != '') {

						var n = json.result.images.img.length - 1;

						if (n >= 2) {

							html += '<div class="tweet-pic tweet-inner">';

						} else {

							html += '<div class="tweet-pic">';

						}
						for (var i = 0; i < n; i++) {
							if (n == 1) {

								html += '<a class="one-pic" rel="lightbox" href="' + Path + 'upload/images/' + json.result.images.img[i] + '" data-avatar="' + user_avatar + '" data-name="' + json.result.user_name + '" data-title="' + json.result.description + '" data-lightbox="tweet-pic-' + json.result.id + '" style="background-image:url(' + Path + 'thumb.php&#63;src=' + Path + 'upload/images/' + json.result.images.img[i] + '&#38;w=350&#38;h=250&#38;zc=1&#38;q=100)">';
								if (json.result.images.ext[i] == 'gif') html += '<span class="img_intros"><em class="giftag"></em></span>';

							} else {

								html += '<a class="mut-pic" href="' + Path + "upload/images/" + json.result.images.img[i] + '" rel="lightbox" data-name="' + json.result.user_name + '" data-avatar="' + user_avatar + '" data-title="' + json.result.description + '" data-lightbox="tweet-pic-3"><img src="' + Path + 'thumb.php&#63;src=' + Path + 'upload/images/' + json.result.images.img[i] + '&#38;w=180&#38;h=180&#38;zc=1&#38;q=100"/>';
								if (json.result.images.ext[i] == 'gif') html += '<span class="img_intros"><em class="giftag"></em></span>';
							}

							html += '</a>';
						}

						html += '</div>';

					}

				}

				html += '<div class="tweet-inner tweet-oter clearleft">';

				if (json.result.ip != '')

				html += '<span><i class="icon icon-locationfill"></i>' + json.result.ip + '</span>';

				if (json.result.agent != '')

				html += '<span>' + json.result.agent + '</span>';

				html += '</div>';

				html += '<div class="tweet-data">';

				html += '<div class="data-box data-box-line data-box-reply" data-id="' + json.result.id + '" data-name="' + json.result.user_name + '" data-child="0" data-loc="0"><i class="icon icon-comment"></i>评论</div>';

				html += '<div id="like-' + json.result.id + '" class="data-box like" data-tip="喜欢" data-id="' + json.result.id + '"><i class="icon icon-like"></i>喜欢</div>';

				html += '<div class="data-box data-box-line_l" onClick="TweetDel(' + json.result.id + ')"><i class="icon icon-delete"></i>删除</div>';

				html += '</div>';

				html += '<div class="reply-hook">';

				html += '<ul id="reply-' + json.result.id + '" class="reply-list">';

				html += '<li class="tweet-inner" id="tweet-hook-' + json.result.id + '" style="display:none"></li>';

				html += '</ul>';

				html += '</li>';

				html += '</div>';

				$('#tweet-list').prepend(html);

				if ($('#tweet-list').data('have') == '0') $('.no-content').remove();

				tip('<i class="icon icon-zhengque1"></i>发送成功!');

				$('#update_tweet_num').addClass("am-animation-scale-down").html(json.result.tweet_num);

				if (json.result.album_num > $('#update_album_num').html()) $('#update_album_num').addClass("am-animation-scale-down").html(json.result.album_num);

				$('#tweet-content,#tweet-file,#tweet-title').val('');

				$('#fileList').html('');

				$('#upload-img input').removeAttr("disabled");

				that.html("发送");
			}
		});
	});

});

function noticeList(json) {

	var html = '<li class="notice-list"><a class="notice-ava" href="' + Path + 'index.php?app=tweet&action=user&id=' + json.user_id + '"><img src="';
	if (json.user_avatar != '') {
		html += json.user_avatar;
	} else {
		html += Path + 'theme/style/avatar.jpg';
	}
	html += '"/><div class="notice-name">' + json.user_name + '</div></a>';
	html += '<div class="notice-time">' + json.lastdate + '</div><div class="notice-text">';
	if (json.flag == 0) {
		html += "<strong>提到了你</strong>";
	} else if (json.flag == 1) {
		html += "<strong>评论了我的动态</strong>";
	} else if (json.flag == 2) {
		html += "<strong>回复了我的评论</strong>";
	}
	html += json.content + '</div></li>';

	return html;

}

function isLogin() {
	var user = $('.user_info').data('user');
	if (user) {
		return true;
	} else {
		return false;
	}
}

function format(id) {
	var body = "\n" + document.getElementById(id).value;
	body = body.replace(/ |　/ig, "");
	body = body.replace(/\r\n/ig, "\n");
	body = body.replace(/\n\n/ig, "\n");
	body = body.replace(/\n\n/ig, "\n");
	body = body.replace(/\n\n/ig, "\n");
	body = body.replace(/\n\n/ig, "\n");
	body = body.replace(/\n/ig, "\n\n　　");
	body = body.replace("\n\n", "");
	document.getElementById(id).value = body;
}
// 前端公用提示，用于替换alert

function tip(html) {
	var showTip = $('#tip');
	showTip.clearQueue().animate({
		height: '40px'
	}, 300).html(html);
	setTimeout(function() {
		showTip.clearQueue().animate({
			height: '0px'
		}, 300).html('');
	}, 2000);
}

function getObjectURL(file) {
	var url = null;
	if (window.createObjectURL != undefined) { // basic
		url = window.createObjectURL(file);

	} else if (window.URL != undefined) { // mozilla(firefox)
		url = window.URL.createObjectURL(file);

	} else if (window.webkitURL != undefined) { // webkit or chrome
		url = window.webkitURL.createObjectURL(file);
	}

	return url;
}

function ImagesSize(size) {
	var i = Math.floor(Math.log(size) / Math.log(1024));
	return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
}

function add_file(id, file) {
	var template = '<div class="file-list" id="uploadFile' + id + '"><img class="file-image" title="' + file.name + '"/><div class="file-info"><div class="file-name">' + file.name + ImagesSize(file.size) + '</div><div class="file-bar"><div class="file-progress" style="width:0%"></div></div></div><div class="file-rotate" data-id="" title="照片旋转"><i class="icon icon-shuaxin"></i></div><div class="file-del" data-id="" title="删除这张"><i class="icon icon-cuowu"></i></div></div>';

	$('#fileList').prepend(template);
}

function update_file_progress(id, percent) {
	$('#uploadFile' + id).find('.file-progress').width(percent);
}

function emoji() {
	var emoji = '<img src="' + Path + 'theme/emot/0.gif"alt=""title="奸笑"/><img src="' + Path + 'theme/emot/1.gif"alt=""title="有内涵"/><img src="' + Path + 'theme/emot/2.gif"alt=""title="流汗"/><img src="' + Path + 'theme/emot/3.gif"alt=""title="嘘"/><img src="' + Path + 'theme/emot/4.gif" alt="" title="难过"/><img src="' + Path + 'theme/emot/5.gif" alt="" title="擦汗"/><img src="' + Path + 'theme/emot/6.gif" alt="" title="惊恐"/><img src="' + Path + 'theme/emot/7.gif" alt="" title="调皮"/><img src="' + Path + 'theme/emot/8.gif" alt="" title="冷汗"/><img src="' + Path + 'theme/emot/9.gif" alt="" title="酷毙了"/><img src="' + Path + 'theme/emot/10.gif" alt="" title="亲亲"/><img src="' + Path + 'theme/emot/11.gif" alt="" title="我睡了"/><img src="' + Path + 'theme/emot/12.gif" alt="" title="坏笑"/><img src="' + Path + 'theme/emot/13.gif" alt="" title="哼哼"/><img src="' + Path + 'theme/emot/14.gif" alt="" title="害羞"/><img src="' + Path + 'theme/emot/15.gif" alt="" title="微笑"/><img src="' + Path + 'theme/emot/16.gif" alt="" title="猪"/><img src="' + Path + 'theme/emot/17.gif" alt="" title="委屈"/><img src="' + Path + 'theme/emot/18.gif" alt="" title="流口水"/><img src="' + Path + 'theme/emot/19.gif" alt="" title="要哭了"/><img src="' + Path + 'theme/emot/20.gif" alt="" title="打你"/><img src="' + Path + 'theme/emot/21.gif" alt="" title="尴尬"/><img src="' + Path + 'theme/emot/22.gif" alt="" title="大哭"/><img src="' + Path + 'theme/emot/23.gif" alt="" title="可怜"/><img src="' + Path + 'theme/emot/24.gif" alt="" title="努力"/><img src="' + Path + 'theme/emot/25.gif" alt="" title="偷笑"/><img src="' + Path + 'theme/emot/26.gif" alt="" title="抠鼻"/><img src="' + Path + 'theme/emot/27.gif" alt="" title="大笑"/><img src="' + Path + 'theme/emot/28.gif" alt="" title="不给力"/><img src="' + Path + 'theme/emot/29.gif" alt="" title="给力"/><img src="' + Path + 'theme/emot/30.gif" alt="" title="憨笑"/><img src="' + Path + 'theme/emot/31.gif" alt="" title="疑惑"/><img src="' + Path + 'theme/emot/32.gif" alt="" title="欢呼"/><img src="' + Path + 'theme/emot/33.gif" alt="" title="鄙视"/><img src="' + Path + 'theme/emot/34.gif" alt="" title="白眼"/><img src="' + Path + 'theme/emot/35.gif" alt="" title="分手"/>';
	return emoji;
}

function editorInsert(content, id) {
	var o = document.getElementById(id);
	if (typeof document.selection != "undefined") {
		document.selection.createRange().text = content;
	} else {
		var l = o.value.length;
		o.value = o.value.substr(0, o.selectionStart) + content + o.value.substring(o.selectionStart, l);
	}
}

function scrolltop() {
	$.fn.scrollToTop = function() {
		var me = $(this);
		$(window).scroll(function() {
			if ($(window).scrollTop() < 10) {
				me.fadeOut();
			} else {
				me.fadeIn();
			}
		});
		me.click(function() {
			$("html,body").animate({
				scrollTop: 0
			});
			return false;
		});
	}
	$("#totop").scrollToTop();
}

function like(id) {
	$.ajax({
		type: "GET",
		url: Path + "?app=tweet&action=like&tid=" + id,
		dataType: 'json',
		success: function(json) {
			if (json.result == '0') {
				tip("<i class='icon icon-xinxi'></i>您还没有登录哦");
			} else if (json.result == '1') {
				tip("<i class='icon icon-like'></i>已取消喜欢");
				json.count > 0 ? $("#like-" + id).removeClass('liked').html('<i class="icon icon-like"></i>喜欢(' + json.count + ')') : $("#like-" + id).removeClass('liked').html('<i class="icon icon-like"></i>喜欢');

			} else if (json.result == '2') {
				tip('<i class="icon icon-likefill"></i>喜欢+1');
				$("#like-" + id).addClass('liked').addClass("am-animation-scale-down").html('<i class="icon icon-likefill"></i>已喜欢(' + json.count + ')');
			}
		}
	});
}

function TweetDel(id) {
	$.get(Path + "?app=tweet&action=delete&tid=" + id, function(e) {
		$('#tweet-' + id).fadeOut();
	});
}

function NoticeDel(id) {
	$.get(Path + "?app=tweet&action=notice&do=delete&nid=" + id, function(e) {
		$('#n_' + id).fadeOut();
	});
}

function ReplyDel(id) {
	var that = $("#delReply-" + id);
	that.removeAttr("onclick");
	that.html(that.html().replace("删除", "确认删除?"));
	that.click(function() {
		$.ajax({
			type: "GET",
			url: Path + "?app=tweet&action=delete_reply&rid=" + id,
			success: function(e) {
				$('#reply-' + id).fadeOut();
			}
		});
	});
}
// 评论回滚定位

function reply_hash() {
	var hash = window.location.hash;
	var hashID = hash.substr(7);
	if (hash.substring(0, 6) == "#reply") {
		$("html,body").animate({
			scrollTop: $("#reply-" + hashID).offset().top - 60
		}, 500);
		$("#reply-" + hashID).addClass('reply-current');
	}
}

function add_wap_reply(tid, name, child, loc, nid) {

	var template = '<div class="wap-reply-form"><div class="wap-head"><div class="wap-center-title">评论</div><div class="wap-left-ui"><i class="icon icon-fanhui"></i><span>取消</span></div><div id="wap-push-reply" class="wap-right-ui"><span>发表</span><i class="icon icon-xiangyou"></i></div></div><div class="wap-content"><textarea id="wap-reply-content" placeholder="评论给 ' + name + '"></textarea></div><div class="wap-tool"><div id="wap-tool-reply-emoji" class="box-col"><i class="icon icon-biaoqing"></i></div></div><div class="wap-emoji-hook"></div></div>';

	$('.wap-reply-form').remove();

	$('.wap-box').html(template).show();

	$("#wap-reply-content").focus();

	$(".wap-emoji-hook").html(emoji).find("img").each(function(i) {
		$(this).on("click", function(e) {
			e.preventDefault();
			editorInsert('[e:' + i + ']', 'wap-reply-content');
		});
	});

	$('#wap-tool-reply-emoji').addSwipeEvents().on('touch', function() {

		if ($('.wap-emoji-hook').css("display") == "none") {
			$("#wap-reply-content").blur();
			$(this).find('i').removeClass("icon-biaoqing").addClass('icon-jianpan');
			$('.wap-emoji-hook').show();
			return false;
		} else {
			$(".wap-emoji-hook").hide();
			$("#wap-reply-content").focus();
			$(this).find('i').removeClass("icon-jianpan").addClass('icon-biaoqing');
			return false;
		}
	});

	$('.wap-left-ui').addSwipeEvents().on('touch', function() {

		if (loc == 0) {
			if (nid) {
				$("html,body").animate({
					scrollTop: $("#n_" + nid).offset().top - 60
				});
			} else {
				$("html,body").animate({
					scrollTop: $("#tweet-" + tid).offset().top - 60
				});
			}
			$('.wap-box').fadeOut(1000);
		} else {
			$("html,body").animate({
				scrollTop: $("#reply-" + child).offset().top - 80
			});
			$('.wap-box').fadeOut(1000);
		}

		$("#wap-reply-content").blur();
	});

	$("#wap-push-reply").addSwipeEvents().on('touch', function() {

		var content = $.trim($("#wap-reply-content").val());
		var data = {
			content: content,
			tid: tid,
			parent_id: child,
		};
		if (content != '') {
			$(this).html('<span>发表中...</span><i class="icon icon-xiangyou"></i>');
			loc == 1 ? postReply(data, tid, loc, 2) : postReply(data, tid, loc, 1, nid);
		}

	});
}

function postReply(data, tid, loc, mode, nid) {

	var url = Path + "?app=tweet&action=reply";

	$.post(url, data, function(json) {

		if (json.result.error == 0) {
			tip("<i class='icon icon-xinxi'></i>请先登录~");
			return false;
		}
		if (json.result.error == 1) {
			tip("<i class='icon icon-xinxi'></i>内容不能空着哦~");
			return false;
		}
		if (json.result.error == 2) {
			tip("<i class='icon icon-xinxi'></i>您的内容已被屏蔽！");
			return false;
		}
		if (json.result.error == 3) {
			tip("<i class='icon icon-xinxi'></i>评论过于频繁！");
			return false;
		}
		if (json.success) {
			//tip(JSON.stringify(json));
			tip("<i class='icon icon-zhengque1'></i>回应成功！");
			if (loc == 0) {

				if (json.result.child != 0) {

					var html = '<li id="reply-' + json.result.rid + '"><a href="' + Path + 'index.php?app=tweet&action=user&id=' + json.result.user_id + '">' + json.result.user_name + '</a> ' + '回复 <a href="' + Path + 'index.php?app=tweet&action=user&id=' + json.result.reply_user_id + '">' + json.result.reply_user_name + '</a> :' + json.result.content + '</li>';

				} else {

					var html = '<li id="reply-' + json.result.rid + '"><a href="' + Path + 'index.php?app=tweet&action=user&id=' + json.result.user_id + '">' + json.result.user_name + '</a> :' + json.result.content + '</li>';

				}

				$("#reply-" + tid).append(html);

				// mode 1:手机 0:电脑
				if (mode == 1) {

					if ($('#bind').attr("data-menu") == 'notice') {

						$("html,body").animate({
							scrollTop: $("#n_" + nid).offset().top - 80
						});

					} else {

						$("html,body").animate({
							scrollTop: $("#reply-" + json.result.rid).offset().top - 130
						});

					}

					$('.wap-box').fadeOut(1000);

				} else {

					$("#reply-content").val("");
					$('#reply-form').remove();

				}

			} else if (loc == 1) {
				var html = '<li id="reply-' + json.result.rid + '" class="reply-view"><a class="reply-avatar" href="' + Path + 'index.php?app=tweet&action=user&id=' + json.result.user_id + '">  <img src="' + json.result.user_avatar + '"/></a><div class="reply-main"><div class="reply-name"><a href="' + Path + 'index.php?app=tweet&action=user&id=' + json.result.user_id + '">' + json.result.user_name + '</a></div><p>';

				if (json.result.child != 0) html += '回复 <a href="' + Path + 'index.php?app=tweet&action=user&id=' + json.result.reply_user_id + '">' + json.result.reply_user_name + '</a> :';

				html += json.result.content + '</p><div class="reply-data"><span>' + json.result.lastdate + '</span></div></div></li>';

				$(".tweet-comment-list").append(html);

				if (mode == 1) {

					$("#reply-content").val("");
					$('#reply-form').remove();
					$("html,body").animate({
						scrollTop: $("#reply-" + json.result.rid).offset().top
					});

				} else if (mode == 2) {

					$("html,body").animate({
						scrollTop: $("#reply-" + json.result.rid).offset().top
					});
					$('.wap-box').fadeOut(1000);

				} else {

					$("#reply-this-content").val("");

				}
			}
		} // post success
	}); // post end
}