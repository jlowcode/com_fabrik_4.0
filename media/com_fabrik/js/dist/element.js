/*! Fabrik */

define(["jquery"], function (jQuery) {
    return (
        (window.FbElement = new Class({
            Implements: [Events, Options],
            options: { element: null, defaultVal: "", value: "", label: "", editable: !1, isJoin: !1, joinId: 0, changeEvent: "change", hasAjaxValidation: !1 },
            initialize: function (t, e) {
                var i = this;
                if ((this.setPlugin(""), (e.element = t), (this.strElement = t), (this.loadEvents = []), (this.events = $H({})), this.setOptions(e), this.options.advanced)) {
                    var n = this.getChangeEvent();
                    jQuery("#" + this.options.element).on("change", { changeEvent: n }, function (t) {
                        document.id(this.id).fireEvent(t.data.changeEvent, new Event.Mock(document.id(this.id), t.data.changeEvent));
                    });
                }
                return (
                    Fabrik.on("fabrik.form.element.added", function (t, e, n) {
                        n === i &&
                            i.addNewEvent(i.getFocusEvent(), function () {
                                i.removeTipMsg();
                            });
                    }),
                    this.setElement()
                );
            },
            destroy: function () {},
            setPlugin: function (t) {
                ("null" !== typeOf(this.plugin) && "" !== this.plugin) || (this.plugin = t);
            },
            getPlugin: function () {
                return this.plugin;
            },
            setElement: function () {
                return !!document.id(this.options.element) && ((this.element = document.id(this.options.element)), this.setorigId(), !0);
            },
            get: function (t) {
                if ("value" === t) return this.getValue();
            },
            getFormElementsKey: function (t) {
                return (this.baseElementId = t);
            },
            attachedToForm: function () {
                this.setElement(),
                    Fabrik.bootstrapped
                        ? ((this.alertImage = new Element("i." + this.form.options.images.alert)), (this.successImage = new Element("i.icon-checkmark", { styles: { color: "green" } })))
                        : ((this.alertImage = new Asset.image(this.form.options.images.alert)), this.alertImage.setStyle("cursor", "pointer"), (this.successImage = new Asset.image(this.form.options.images.action_check))),
                    jQuery(this.form.options.images.ajax_loader).data("isicon")
                        ? (this.loadingImage = new Element("span").set("html", this.form.options.images.ajax_loader))
                        : (this.loadingImage = new Asset.image(this.form.options.images.ajax_loader)),
                    this.form.addMustValidate(this);
            },
            fireEvents: function (t) {
                this.hasSubElements()
                    ? this._getSubElements().each(
                          function (e) {
                              Array.from(t).each(
                                  function (t) {
                                      e.fireEvent(t);
                                  }.bind(this)
                              );
                          }.bind(this)
                      )
                    : Array.from(t).each(
                          function (t) {
                              this.element && this.element.fireEvent(t);
                          }.bind(this)
                      );
            },
            getElement: function () {
                return "null" === typeOf(this.element) && (this.element = document.id(this.options.element)), this.element;
            },
            _getSubElements: function () {
                var t = this.getElement();
                return "null" !== typeOf(t) && ((this.subElements = t.getElements(".fabrikinput")), this.subElements);
            },
            hasSubElements: function () {
                return this._getSubElements(), ("array" === typeOf(this.subElements) || "elements" === typeOf(this.subElements)) && 0 < this.subElements.length;
            },
            unclonableProperties: function () {
                return ["form"];
            },
            cloneUpdateIds: function (t) {
                (this.element = document.id(t)), (this.options.element = t);
            },
            runLoadEvent: function (js, delay) {
                (delay = delay || 0),
                    "function" === typeOf(js)
                        ? js.delay(delay)
                        : 0 === delay
                        ? eval(js)
                        : function () {
                              console.log("delayed calling runLoadEvent for " + delay), eval(js);
                          }
                              .bind(this)
                              .delay(delay);
            },
            removeCustomEvents: function () {},
            renewEvents: function () {
                this.events.each(
                    function (t, e) {
                        this.element.removeEvents(e),
                            t.each(
                                function (t) {
                                    this.addNewEventAux(e, t);
                                }.bind(this)
                            );
                    }.bind(this)
                );
            },
            addNewEventAux: function (action, js) {
                this.element.addEvent(
                    action,
                    function (e) {
                        "function" === typeOf(js) ? js.delay(0, this, this) : eval(js);
                    }.bind(this)
                );
            },
            addNewEvent: function (t, e) {
                "load" === t
                    ? (this.loadEvents.push(e), this.runLoadEvent(e))
                    : (this.element || (this.element = document.id(this.strElement)), this.element && (Object.keys(this.events).contains(t) || (this.events[t] = []), this.events[t].push(e), this.addNewEventAux(t, e)));
            },
            addEvent: function (t, e) {
                this.addNewEvent(t, e);
            },
            validate: function () {},
            addAjaxValidationAux: function () {
                var e = this;
                if (this.element && this.options.hasAjaxValidation) {
                    var t = jQuery(this.element);
                    if (t.hasClass("fabrikSubElementContainer"))
                        return void t.find(".fabrikinput").on(this.getChangeEvent(), function (t) {
                            e.form.doElementValidation(t, !0);
                        });
                    t.on(this.getChangeEvent(), function (t) {
                        e.form.doElementValidation(t, !1);
                    });
                }
            },
            addAjaxValidation: function () {
                this.element || (this.element = document.id(this.strElement)), this.element && ((this.options.hasAjaxValidation = !0), this.addAjaxValidationAux());
            },
            addNewOption: function (t, e) {
                var n,
                    i = document.id(this.options.element + "_additions").value,
                    s = { val: t, label: e };
                (n = "" !== i ? JSON.parse(i) : []).push(s);
                for (var a = "[", o = 0; o < n.length; o++) a += JSON.stringify(n[o]) + ",";
                (a = a.substring(0, a.length - 1) + "]"), (document.id(this.options.element + "_additions").value = a);
            },
            getLabel: function () {
                return this.options.label;
            },
            setLabel: function (t) {
                this.options.label = t;
                var e = this.getLabelElement();
                e && (e[0].textContent = t);
            },
            update: function (t) {
                this.getElement() && (this.options.editable ? (this.element.value = t) : (this.element.innerHTML = t));
            },
            updateByLabel: function (t) {
                this.update(t);
            },
            set: function (t) {
                this.update(t);
            },
            getValue: function () {
                return !!this.element && (this.options.editable ? this.element.value : this.options.value);
            },
            reset: function () {
                !0 === this.options.editable && this.update(this.options.defaultVal), this.resetEvents();
            },
            resetEvents: function () {
                this.loadEvents.each(
                    function (t) {
                        this.runLoadEvent(t, 100);
                    }.bind(this)
                );
            },
            clear: function () {
                this.update("");
            },
            onsubmit: function (t) {
                t && t(!0);
            },
            afterAjaxValidation: function () {},
            cloned: function (t) {
                this.renewEvents(), this.resetEvents(), this.addAjaxValidationAux();
                var e = this.getChangeEvent();
                this.element.hasClass("chzn-done") &&
                    (this.element.removeClass("chzn-done"),
                    this.element.addClass("chzn-select"),
                    this.element.getParent().getElement(".chzn-container").destroy(),
                    jQuery("#" + this.element.id).chosen(),
                    jQuery(this.element).addClass("chzn-done"),
                    jQuery("#" + this.options.element).on("change", { changeEvent: e }, function (t) {
                        document.id(this.id).fireEvent(t.data.changeEvent, new Event.Mock(t.data.changeEvent, document.id(this.id)));
                    }));
            },
            decloned: function (t) {},
            getContainer: function () {
                var t = jQuery(this.element).closest(".fabrikElementContainer");
                return (t = 0 !== t.length && t[0]), "null" !== typeOf(this.element) && t;
            },
            getErrorElement: function () {
                return this.getContainer().getElements(".fabrikErrorMessage");
            },
            getLabelElement: function () {
                return this.getContainer().getElements(".fabrikLabel");
            },
            getValidationFx: function () {
                return this.validationFX || (this.validationFX = new Fx.Morph(this.getErrorElement()[0], { duration: 500, wait: !0 })), this.validationFX;
            },
            tips: function () {
                var n = this;
                return jQuery(Fabrik.tips.elements).filter(function (t, e) {
                    if (e === n.getContainer() || e.getParent() === n.getContainer()) return !0;
                });
            },
            addTipMsg: function (t, e) {
                e = e || "error";
                var n,
                    i,
                    s,
                    a,
                    o = this.tips();
                if (0 !== o.length) {
                    void 0 === (o = jQuery(o[0])).attr(e) &&
                        (o.attr(e, t),
                        (n = this._tipContent(o, !1)),
                        (i = jQuery("<div>")).html(n.html()),
                        (s = jQuery("<li>").addClass(e)).html(t),
                        jQuery("<i>").addClass(this.form.options.images.alert).prependTo(s),
                        0 === i.find('li:contains("' + jQuery(t).text() + '")').length && i.find("ul").append(s),
                        (a = unescape(i.html())),
                        void 0 === o.data("fabrik-tip-orig") && o.data("fabrik-tip-orig", n.html()),
                        this._recreateTip(o, a));
                    try {
                        o.data("popover").show();
                    } catch (t) {
                        o.popover("show");
                    }
                }
            },
            _recreateTip: function (e, n) {
                try {
                    e.data("content", n), e.data("popover").setContent(), (e.data("popover").options.content = n);
                } catch (t) {
                    e.attr("data-content", n), e.popover("show");
                }
            },
            _tipContent: function (e, n) {
                var i;
                try {
                    e.data("popover").show(), (i = e.data("popover").tip().find(".popover-content"));
                } catch (t) {
                    i = void 0 !== e.data("fabrik-tip-orig") && n ? jQuery("<div>").append(jQuery(e.data("fabrik-tip-orig"))) : jQuery("<div>").append(jQuery(e.data("content")));
                }
                return i;
            },
            removeTipMsg: function () {
                var t,
                    e = e || "error",
                    n = this.tips();
                if (void 0 !== (n = jQuery(n[0])).attr(e)) {
                    (t = this._tipContent(n, !0)), this._recreateTip(n, t.html()), n.removeAttr(e);
                    try {
                        n.data("popover").hide();
                    } catch (t) {
                        n.popover("hide");
                    }
                }
            },
            moveTip: function (t, e) {
                var n,
                    i,
                    s,
                    a = this.tips();
                0 < a.length &&
                    (s = (a = jQuery(a[0])).data("popover")) &&
                    (n = s.$tip) &&
                    (void 0 === (i = n.data("origPos")) && ((i = { top: parseInt(a.data("popover").$tip.css("top"), 10) + t, left: parseInt(a.data("popover").$tip.css("left"), 10) + e }), n.data("origPos", i)),
                    n.css({ top: i.top - t, left: i.left - e }));
            },
            setErrorMessage: function (t, e) {
                var n,
                    i,
                    s = ["fabrikValidating", "fabrikError", "fabrikSuccess"],
                    a = this.getContainer();
                if (!1 !== a) {
                    s.each(function (t) {
                        e === t ? a.addClass(t) : a.removeClass(t);
                    });
                    var o = this.getErrorElement();
                    switch (
                        (o.each(function (t) {
                            t.empty();
                        }),
                        e)
                    ) {
                        case "fabrikError":
                            Fabrik.loader.stop(this.element);
                            var r = this.tips();
                            if (
                                (Fabrik.bootstrapped && 0 !== r.length
                                    ? this.addTipMsg(t)
                                    : ((n = new Element("a", {
                                          href: "#",
                                          title: t,
                                          events: {
                                              click: function (t) {
                                                  t.stop();
                                              },
                                          },
                                      }).adopt(this.alertImage)),
                                      Fabrik.tips.attach(n)),
                                o[0].adopt(n),
                                a.removeClass("success").removeClass("info").addClass("error"),
                                a.addClass("has-error").removeClass("has-success"),
                                1 < o.length)
                            )
                                for (i = 1; i < o.length; i++) o[i].set("html", t);
                            var l = this.getTabDiv();
                            if (l) {
                                var h = this.getTab(l);
                                h && h.addClass("fabrikErrorGroup");
                            }
                            break;
                        case "fabrikSuccess":
                            if ((a.addClass("success").removeClass("info").removeClass("error"), a.addClass("has-success").removeClass("has-error"), Fabrik.bootstrapped)) Fabrik.loader.stop(this.element), this.removeTipMsg();
                            else {
                                o[0].adopt(this.successImage);
                                (function () {
                                    o[0].addClass("fabrikHide"), a.removeClass("success");
                                }.delay(700));
                            }
                            break;
                        case "fabrikValidating":
                            a.removeClass("success").addClass("info").removeClass("error"), Fabrik.loader.start(this.element, t);
                    }
                    this.getErrorElement().removeClass("fabrikHide");
                    var d = this.form;
                    ("fabrikError" !== e && "fabrikSuccess" !== e) || d.updateMainError();
                    var u = this.getValidationFx();
                    switch (e) {
                        case "fabrikValidating":
                        case "fabrikError":
                            u.start({ opacity: 1 });
                            break;
                        case "fabrikSuccess":
                            u.start({ opacity: 1 }).chain(function () {
                                a.hasClass("fabrikSuccess") &&
                                    (a.removeClass("fabrikSuccess"),
                                    this.start.delay(700, this, {
                                        opacity: 0,
                                        onComplete: function () {
                                            a.addClass("success").removeClass("error"),
                                                d.updateMainError(),
                                                s.each(function (t) {
                                                    a.removeClass(t);
                                                });
                                        },
                                    }));
                            });
                    }
                } else console.log("Notice: couldn not set error msg for " + t + " no container class found");
            },
            setorigId: function () {
                if (this.options.inRepeatGroup) {
                    var t = this.options.element;
                    this.origId = t.substring(0, t.length - 1 - this.options.repeatCounter.toString().length);

                    /**
                     * Begin - Toogle Submit in solicitações
                     * Adding auto-complete element in formElements at JS
                     * 
                     * Id Task: 116
                     */
                    if(this.options.element.indexOf('-auto-complete') >= 0 ) {
                        var bits = Array.from(this.options.element.split('_'));
                        bits.pop();
                        this.origId = bits.join('_');
                    }
					// END - Toogle Submit in solicitações
                }
            },
            decreaseName: function (e) {
                var t = this.getElement();
                return (
                    "null" !== typeOf(t) &&
                    (this.hasSubElements()
                        ? this._getSubElements().each(
                              function (t) {
                                  (t.name = this._decreaseName(t.name, e)), (t.id = this._decreaseId(t.id, e));
                              }.bind(this)
                          )
                        : "null" !== typeOf(this.element.name) && (this.element.name = this._decreaseName(this.element.name, e)),
                    "null" !== typeOf(this.element.id) && (this.element.id = this._decreaseId(this.element.id, e)),
                    this.options.repeatCounter > e && this.options.repeatCounter--,
                    this.element.id)
                );
            },
            _decreaseId: function (t, e, n) {
                var i = !1;
                !1 !== (n = n || !1) && t.contains(n) && ((t = t.replace(n, "")), (i = !0));
                var s = Array.from(t.split("_")),
                    a = s.getLast();
                if ("null" === typeOf(a.toInt())) return s.join("_");
                1 <= a && e < a && a--, s.splice(s.length - 1, 1, a);
                var o = s.join("_");
                return i && (o += n), (this.options.element = o);
            },
            _decreaseName: function (t, e, n) {
                var i = !1;
                !1 !== (n = n || !1) && t.contains(n) && ((t = t.replace(n, "")), (i = !0));
                var s = t.split("["),
                    a = s[1].replace("]", "").toInt();
                1 <= a && e < a && a--, (a += "]"), (s[1] = a);
                var o = s.join("[");
                return i && (o += n), o;
            },
            getRepeatNum: function () {
                return !1 !== this.options.inRepeatGroup && this.element.id.split("_").getLast();
            },
            getBlurEvent: function () {
                return "select" === this.element.get("tag") ? "change" : "blur";
            },
            getFocusEvent: function () {
                return "select" === this.element.get("tag") ? "click" : "focus";
            },
            getChangeEvent: function () {
                return this.options.changeEvent;
            },
            select: function () {},
            focus: function () {
                this.removeTipMsg();
            },
            hide: function () {
                var t = this.getContainer();
                t && jQuery(t).hide();
            },
            show: function () {
                var t = this.getContainer();
                t && jQuery(t).show();
            },
            toggle: function () {
                var t = this.getContainer();
                t && t.toggle();
            },
            getCloneName: function () {
                return this.options.element;
            },
            doTab: function (t) {
                (function () {
                    this.redraw(),
                        Fabrik.bootstrapped ||
                            this.options.tab_dt.removeEvent(
                                "click",
                                function (t) {
                                    this.doTab(t);
                                }.bind(this)
                            );
                }
                    .bind(this)
                    .delay(500));
            },
            getTab: function (t) {
                var e;
                Fabrik.bootstrapped ? (e = jQuery("a[href$=#" + t.id + "]").closest("[data-role=fabrik_tab]")) : (e = t.getPrevious(".tabs"));
                return e || !1;
            },
            getTabDiv: function () {
                var t = Fabrik.bootstrapped ? ".tab-pane" : ".current",
                    e = this.element.getParent(t);
                return e || !1;
            },
            watchTab: function () {
                var t,
                    e = Fabrik.bootstrapped ? ".tab-pane" : ".current",
                    n = this.element.getParent(e);
                n &&
                    (Fabrik.bootstrapped
                        ? (t = document.getElement("a[href$=#" + n.id + "]").getParent("ul.nav")).addEvent(
                              "click:relay(a)",
                              function (t, e) {
                                  this.doTab(t);
                              }.bind(this)
                          )
                        : (t = n.getPrevious(".tabs")) &&
                          ((this.options.tab_dd = this.element.getParent(".fabrikGroup")),
                          "none" === this.options.tab_dd.style.getPropertyValue("display") &&
                              ((this.options.tab_dt = t.getElementById("group" + this.groupid + "_tab")),
                              this.options.tab_dt &&
                                  this.options.tab_dt.addEvent(
                                      "click",
                                      function (t) {
                                          this.doTab(t);
                                      }.bind(this)
                                  ))));
            },
            updateUsingRaw: function () {
                return !1;
            },
        })),
        window.FbElement
    );
});
