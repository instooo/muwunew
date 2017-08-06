var W360 = W360 || {};

W360.api = function(e, t) {
    function u(e, t, n) {
        return n = n || i[e][t], n && !i[e][t] && (i[e][t] = n) || n;
    }
    function a(t) {
        if (r[t]) return r[t];
        var i = arguments.callee.caller, u = i.arguments, a = t == "played" ? n[t] + (W360.api.gkey || "") + "&cate=" + (W360.api.cate || "") : n[t];
        a = a.replace(/{count}/g, o);
        if (++s > 3) {
            s = 0;
            return;
        }
        e.ajax(a, {
            "dataType": "jsonp",
            "jsonp": "_jp",
            "jsonpCallback": "wan360u8_gfront_callback" + Math.round(Math.random() * 1e16),
            "success": function(e) {
                s = 0;
                if (e && e.errno === 0) {
                    if (!e.data) return !1;
                    r[t] = e.data, i.apply(null, u);
                }
            },
            "error": function() {
                setTimeout(function() {
                    i.apply(null, u);
                }, 3e3 * s);
            }
        });
    }
    function f(t, n) {
        if (typeof n == "number" || t < 10) W360.api.cate = n, W360.api.gkey = "";
        var r = i[t].id, s = i[t].temp, o = a(t), u = "", f = 0;
        if (!o) return;
        switch (t) {
          case "profile":
            if (o.profile) {
                if (!o.profile.name) {
                    var l = e.isPlainObject(n) ? !!n.noportrait : !!n;
                    if (l) return;
                    o.profile.name = "[完善昵称]";
                }
                return e(r).html(s.replace(/{(.*?)}/g, function(e, t) {
                    if (t) return o.profile[t] || "";
                })), e.isPlainObject(n) && e.isFunction(n.callback) && n.callback(o), e(r);
            }
            break;
          case "played":
            e.isFunction(n) && (o = n(o)), e.isPlainObject(n) && e.isFunction(n.data_filter) && (o = n.data_filter(o));
            for (var c = null; f < o.count; f++) c = o.items[o.index[f]], u += s.replace(/{(.*?)}/g, function(e, t) {
                return t && c[t] || "";
            });
            e(r).html(u), e.isPlainObject(n) && e.isFunction(n.callback) && n.callback(o);
            break;
          case "score":
            if (o) return e(r).html(s.replace(/{(.*?)}/g, o || 0)), e.isPlainObject(n) && e.isFunction(n.callback) && n.callback(o), e(r);
            break;
          case "csurm":
            if (o) {
                var h = o.groups.length;
                return e(r).html(s.replace(/{(.*?)}/g, h)), h > 0 && e(r).show(), e.isPlainObject(n) && e.isFunction(n.callback) && n.callback(o), e(r);
            }
            break;
          default:
            alert(t);
        }
    }
    function l(t, r, i, s) {
        var a = t;
        e.isPlainObject(s) && (s.count && (o = s.count), s.url && (n[t] = s.url)), r = u(a, "id", r), i = u(a, "temp", i);
        if (!r || !i) return;
        return f(a, s);
    }
    var n = {
        "profile": "http://gate.wan.360.cn/me/profile?",
        "played": "http://gate.wan.360.cn/me/played?start=0&size={count}&gkey=",
        "score": "http://gate.wan.360.cn/me/score",
        "csurm": "http://u8.wan.360.cn/getGroupUrm.html"
    }, r = {
        "profile": null,
        "played": null,
        "score": null
    }, i = {
        "profile": {
            "id": "",
            "temp": ""
        },
        "played": {
            "id": "",
            "temp": ""
        },
        "score": {
            "id": "",
            "temp": ""
        },
        "csurm": {
            "id": "",
            "temp": ""
        }
    }, s = 0, o = 3;
    return {
        "showProfile": function(e, t, n) {
            return l("profile", e, t, n);
        },
        "showPlayed": function(e, t, n) {
            return l("played", e, t, n);
        },
        "showScore": function(e, t, n) {
            return l("score", e, t, n);
        },
        "showCSUrm": function(e, t, n) {
            return l("csurm", e, t, n);
        }
    };
}(jQuery), function(e) {
    function t(t, n) {
        function m() {
            return r.update(), y(), r;
        }
        function g() {
            f.obj.css(c, p / u.ratio), o.obj.css(c, -p), v.start = f.obj.offset()[c];
            var e = h.toLowerCase();
            u.obj.css(e, a[n.axis]), a.obj.css(e, a[n.axis]), f.obj.css(e, f[n.axis]);
        }
        function y() {
            f.obj.bind("mousedown", b), f.obj[0].ontouchstart = function(e) {
                return e.preventDefault(), f.obj.unbind("mousedown"), b(e.touches[0]), !1;
            }, a.obj.bind("mouseup", S), n.scroll && this.addEventListener ? (i[0].addEventListener("DOMMouseScroll", w, !1), i[0].addEventListener("mousewheel", w, !1)) : n.scroll && (i[0].onmousewheel = w);
        }
        function b(t) {
            v.start = l ? t.pageX : t.pageY;
            var n = parseInt(f.obj.css(c));
            return d.start = n == "auto" ? 0 : n, e(document).bind("mousemove", S), document.ontouchmove = function(t) {
                e(document).unbind("mousemove"), S(t.touches[0]);
            }, e(document).bind("mouseup", E), f.obj.bind("mouseup", E), f.obj[0].ontouchend = document.ontouchend = function(t) {
                e(document).unbind("mouseup"), f.obj.unbind("mouseup"), E(t.touches[0]);
            }, !1;
        }
        function w(t) {
            if (!(o.ratio >= 1)) {
                var t = t || window.event, r = t.wheelDelta ? t.wheelDelta / 120 : -t.detail / 3;
                p -= r * n.wheel, p = Math.min(o[n.axis] - s[n.axis], Math.max(0, p)), f.obj.css(c, p / u.ratio), o.obj.css(c, -p);
                if (n.lockscroll || p !== o[n.axis] - s[n.axis] && p !== 0) t = e.event.fix(t), t.preventDefault();
            }
        }
        function E(t) {
            return e(document).unbind("mousemove", S), e(document).unbind("mouseup", E), f.obj.unbind("mouseup", E), document.ontouchmove = f.obj[0].ontouchend = document.ontouchend = null, !1;
        }
        function S(e) {
            return o.ratio >= 1 || (d.now = Math.min(a[n.axis] - f[n.axis], Math.max(0, d.start + ((l ? e.pageX : e.pageY) - v.start))), p = d.now * u.ratio, o.obj.css(c, -p), f.obj.css(c, d.now)), !1;
        }
        var r = this, i = t, s = {
            "obj": e(".viewport", t)
        }, o = {
            "obj": e(".overview", t)
        }, u = {
            "obj": e(".scrollbar", t)
        }, a = {
            "obj": e(".track", u.obj)
        }, f = {
            "obj": e(".thumb", u.obj)
        }, l = n.axis == "x", c = l ? "left" : "top", h = l ? "Width" : "Height", p, d = {
            "start": 0,
            "now": 0
        }, v = {};
        return this.update = function(e) {
            s[n.axis] = s.obj[0]["offset" + h], o[n.axis] = o.obj[0]["scroll" + h], o.ratio = s[n.axis] / o[n.axis], u.obj.toggleClass("disable", o.ratio >= 1), a[n.axis] = n.size == "auto" ? s[n.axis] : n.size, f[n.axis] = Math.min(a[n.axis], Math.max(0, n.sizethumb == "auto" ? a[n.axis] * o.ratio : n.sizethumb)), u.ratio = n.sizethumb == "auto" ? o[n.axis] / a[n.axis] : (o[n.axis] - s[n.axis]) / (a[n.axis] - f[n.axis]), p = e == "relative" && o.ratio <= 1 ? Math.min(o[n.axis] - s[n.axis], Math.max(0, p)) : 0, p = e == "bottom" && o.ratio <= 1 ? o[n.axis] - s[n.axis] : isNaN(parseInt(e)) ? p : parseInt(e), g();
        }, m();
    }
    e.tiny = e.tiny || {}, e.tiny.scrollbar = {
        "options": {
            "axis": "y",
            "wheel": 40,
            "scroll": !0,
            "lockscroll": !0,
            "size": "auto",
            "sizethumb": "auto"
        }
    }, e.fn.tinyscrollbar = function(n) {
        var n = e.extend({}, e.tiny.scrollbar.options, n);
        return this.each(function() {
            e(this).data("tsb", new t(e(this), n));
        }), this;
    }, e.fn.tinyscrollbar_update = function(t) {
        return e(this).data("tsb").update(t);
    };
}(jQuery), function() {
    function t(t) {
        var n = new Image;
        n.onload = n.onerror = n.onabort = function() {
            n.onload = n.onerror = n.onabort = null, n = null, e.length > 10 && (e.length = 10);
        }, n.src = "http://log.s.1360.cn/online.gif?uid=" + t.qid + "&gkey=" + t.gkey + "&skey=" + t.skey + "&t=" + +(new Date), e.push(n);
    }
    function n(e) {
        if (!e || !e.qid) return;
        t(e), window.setTimeout(function() {
            n(e);
        }, 24e4);
    }
    var e = [];
    window.SC = {
        "startTrack": n
    };
}();