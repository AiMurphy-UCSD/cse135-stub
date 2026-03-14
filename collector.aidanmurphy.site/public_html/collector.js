/* collector.js
 * Host this at: https://collector.aidanmurphy.site/collector.js
 * Receives at:  https://collector.aidanmurphy.site/log  (configure in COLLECT_ENDPOINT)
 */ 

(function () {
  "use strict";

  // =========================
  // CONFIG
  // =========================
  const COLLECT_ENDPOINT = "https://collector.aidanmurphy.site/log/";
  const FLUSH_INTERVAL_MS = 5000;
  const IDLE_THRESHOLD_MS = 2000;
  const MOUSE_THROTTLE_MS = 100;   // mousemove throttle
  const SCROLL_THROTTLE_MS = 200;  // scroll throttle
  const MAX_QUEUE = 50;

  // =========================
  // HELPERS
  // =========================
  function nowMs() {
    return Date.now();
  }

  function isoNow() {
    return new Date().toISOString();
  }

  function safeString(x, maxLen = 500) {
    try {
      const s = String(x);
      return s.length > maxLen ? s.slice(0, maxLen) + "…" : s;
    } catch {
      return "";
    }
  }

  function getCookie(name) {
    const m = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
    return m ? decodeURIComponent(m[2]) : null;
  }

  function setCookie(name, value, days = 1) {
    // Domain cookie so it is shared across test.* and collector.* for correlation
    const maxAge = days * 24 * 60 * 60;
    document.cookie =
      `${encodeURIComponent(name)}=${encodeURIComponent(value)}; ` +
      `Max-Age=${maxAge}; Path=/; Domain=.aidanmurphy.site; SameSite=Lax; Secure`;
  }

  function randomId() {
    // Prefer crypto UUID if available
    if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
    // Fallback
    return "sess_" + Math.random().toString(16).slice(2) + "_" + nowMs();
  }

  // =========================
  // SESSIONING
  // =========================
  const SESSION_STORAGE_KEY = "_collector_session_id";
  const SESSION_COOKIE_KEY = "cse135_session";

  function getSessionId() {
    let sid = sessionStorage.getItem(SESSION_STORAGE_KEY);
    if (!sid) {
      sid = randomId();
      sessionStorage.setItem(SESSION_STORAGE_KEY, sid);
    }
    // Also set a domain cookie so Apache access logs can be correlated with collector beacons
    // (cookie will be sent to test.aidanmurphy.site AND collector.aidanmurphy.site)
    const existing = getCookie(SESSION_COOKIE_KEY);
    if (existing !== sid) setCookie(SESSION_COOKIE_KEY, sid, 1);
    return sid;
  }

  const sessionId = getSessionId();
  const pageEnterMs = nowMs();

  // =========================
  // QUEUE + SEND
  // =========================
  let queue = [];
  let flushTimer = null;

  function buildBasePayload(type) {
    return {
      type,
      session: sessionId,
      url: location.href,
      path: location.pathname,
      title: document.title,
      referrer: document.referrer || null,
      ts: isoNow()
    };
  }

  function enqueue(evt) {
    queue.push(evt);
    if (queue.length >= MAX_QUEUE) flush("max_queue");
  }

  function flush(reason = "interval") {
    if (queue.length === 0) return;

    const batch = queue;
    queue = [];

    const payload = {
      reason,
      sentAt: isoNow(),
      session: sessionId,
      events: batch
    };

    const body = JSON.stringify(payload);
    const blob = new Blob([body], { type: "application/json" });

    // Prefer sendBeacon (especially for unload)
    if (navigator.sendBeacon) {
      navigator.sendBeacon(COLLECT_ENDPOINT, blob);
      return;
    }

    // Fallback: fetch keepalive
    fetch(COLLECT_ENDPOINT, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body,
      keepalive: true,
      credentials: "include"
    }).catch(() => {
      // If sending fails, you could optionally re-queue; HW doesn’t require it.
    });
  }

  function startFlushLoop() {
    if (flushTimer) return;
    flushTimer = setInterval(() => flush("interval"), FLUSH_INTERVAL_MS);
  }

  // =========================
  // STATIC (after load)
  // =========================
  function detectNetworkType() {
    const c = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    if (!c) return null;
    return {
      effectiveType: c.effectiveType || null,
      downlink: typeof c.downlink === "number" ? c.downlink : null,
      rtt: typeof c.rtt === "number" ? c.rtt : null,
      saveData: !!c.saveData
    };
  }

  function detectImagesEnabled() {
    // If images are blocked, this will usually error
    return new Promise((resolve) => {
      const img = new Image();
      let done = false;

      const finish = (val) => {
        if (done) return;
        done = true;
        resolve(val);
      };

      img.onload = () => finish(true);
      img.onerror = () => finish(false);

      // A 1x1 data URL should load if images are allowed
      img.src =
        "data:image/gif;base64,R0lGODlhAQABAAAAACwAAAAAAQABAAA=";
      // Safety timeout
      setTimeout(() => finish(false), 800);
    });
  }

  function detectCssEnabled() {
    return new Promise((resolve) => {
      // Create an element + inject a style rule; if CSS is blocked entirely, computed style won’t reflect it
      const el = document.createElement("div");
      el.id = "__cse135_css_test";
      el.style.position = "absolute";
      el.style.left = "-9999px";
      document.body.appendChild(el);

      const style = document.createElement("style");
      style.textContent = "#__cse135_css_test { width: 13px !important; }";
      document.head.appendChild(style);

      setTimeout(() => {
        const w = window.getComputedStyle(el).width;
        // widths like "13px" indicates CSS applied
        const ok = typeof w === "string" && w.indexOf("13px") !== -1;
        el.remove();
        style.remove();
        resolve(ok);
      }, 50);
    });
  }

  async function collectStatic() {
    const imagesEnabled = await detectImagesEnabled();
    const cssEnabled = await detectCssEnabled();

    return {
      userAgent: navigator.userAgent,
      language: navigator.language || null,
      cookiesEnabled: !!navigator.cookieEnabled,
      javascriptEnabled: true, // if this script is running, JS is enabled
      imagesEnabled,
      cssEnabled,
      screen: {
        width: window.screen?.width ?? null,
        height: window.screen?.height ?? null
      },
      window: {
        innerWidth: window.innerWidth,
        innerHeight: window.innerHeight
      },
      connection: detectNetworkType()
    };
  }

  // =========================
  // PERFORMANCE (after load)
  // =========================
  function collectPerformance() {
    const nav = performance.getEntriesByType && performance.getEntriesByType("navigation")
      ? performance.getEntriesByType("navigation")[0]
      : null;

    if (nav) {
      const start = nav.startTime; // usually 0
      const end = nav.loadEventEnd;
      const total = (typeof end === "number" && end >= 0) ? Math.round(end - start) : null;

      return {
        navigationEntry: {
          // Keep a smaller subset to avoid huge payloads; you can store full if you want.
          type: nav.type || null,
          startTime: nav.startTime,
          domContentLoadedEventEnd: nav.domContentLoadedEventEnd,
          loadEventEnd: nav.loadEventEnd
        },
        pageStartMsFromNavStart: start,
        pageEndMsFromNavStart: end,
        totalLoadTimeMs: total
      };
    }

    // Fallback: older timing object snapshot
    const t = performance.timing || null;
    if (t) {
      const start = t.navigationStart;
      const end = t.loadEventEnd || 0;
      const total = (end && start) ? (end - start) : null;
      return {
        timing: {
          navigationStart: t.navigationStart,
          responseStart: t.responseStart,
          domContentLoadedEventEnd: t.domContentLoadedEventEnd,
          loadEventEnd: t.loadEventEnd
        },
        pageStartEpochMs: start || null,
        pageEndEpochMs: end || null,
        totalLoadTimeMs: total
      };
    }

    return null;
  }

  // =========================
  // ERRORS
  // =========================
  function initErrorTracking() {
    window.addEventListener("error", (e) => {
      enqueue({
        ...buildBasePayload("error"),
        message: safeString(e.message),
        filename: safeString(e.filename),
        lineno: e.lineno ?? null,
        colno: e.colno ?? null
      });
    });

    window.addEventListener("unhandledrejection", (e) => {
      enqueue({
        ...buildBasePayload("unhandledrejection"),
        reason: safeString(e.reason && (e.reason.stack || e.reason.message || e.reason))
      });
    });
  }

  // =========================
  // ACTIVITY + IDLE
  // =========================
  let lastActivityMs = nowMs();
  let idleStartMs = null;

  function markActivity() {
    const t = nowMs();
    // If we WERE idle, close the idle block
    if (idleStartMs !== null) {
      const idleEnd = t;
      const duration = idleEnd - idleStartMs;
      enqueue({
        ...buildBasePayload("idle_end"),
        idleStartMs: idleStartMs,
        idleEndMs: idleEnd,
        idleDurationMs: duration
      });
      idleStartMs = null;
    }
    lastActivityMs = t;
  }

  function startIdleMonitor() {
    setInterval(() => {
      const t = nowMs();
      if (idleStartMs === null && (t - lastActivityMs) >= IDLE_THRESHOLD_MS) {
        idleStartMs = lastActivityMs; // start at the last moment we saw activity
        enqueue({
          ...buildBasePayload("idle_start"),
          idleStartMs: idleStartMs
        });
      }
    }, 250);
  }

  function throttle(fn, waitMs) {
    let last = 0;
    return function (...args) {
      const t = nowMs();
      if (t - last >= waitMs) {
        last = t;
        fn.apply(this, args);
      }
    };
  }

  function initActivityTracking() {
    document.addEventListener("mousemove", throttle((e) => {
      markActivity();
      enqueue({
        ...buildBasePayload("mousemove"),
        x: e.clientX,
        y: e.clientY
      });
    }, MOUSE_THROTTLE_MS), { passive: true });

    document.addEventListener("click", (e) => {
      markActivity();
      enqueue({
        ...buildBasePayload("click"),
        button: e.button,
        x: e.clientX,
        y: e.clientY
      });
    }, { passive: true });

    document.addEventListener("scroll", throttle(() => {
      markActivity();
      enqueue({
        ...buildBasePayload("scroll"),
        scrollX: window.scrollX,
        scrollY: window.scrollY
      });
    }, SCROLL_THROTTLE_MS), { passive: true });

    document.addEventListener("keydown", (e) => {
      markActivity();
      enqueue({
        ...buildBasePayload("keydown"),
        key: safeString(e.key, 50),
        code: safeString(e.code, 50)
      });
    });

    document.addEventListener("keyup", (e) => {
      markActivity();
      enqueue({
        ...buildBasePayload("keyup"),
        key: safeString(e.key, 50),
        code: safeString(e.code, 50)
      });
    });
  }

  // =========================
  // PAGE ENTER / LEAVE
  // =========================
  function sendPageLeave() {
    const leaveMs = nowMs();
    const duration = leaveMs - pageEnterMs;

    enqueue({
      ...buildBasePayload("page_leave"),
      pageEnterMs,
      pageLeaveMs: leaveMs,
      timeOnPageMs: duration
    });

    flush("page_leave");
  }

  // Fire on visibility change (mobile/tab close) + unload
  function initLeaveHooks() {
    document.addEventListener("visibilitychange", () => {
      if (document.visibilityState === "hidden") {
        sendPageLeave();
      }
    });

    window.addEventListener("pagehide", () => {
      sendPageLeave();
    });

    window.addEventListener("beforeunload", () => {
      sendPageLeave();
    });
  }

  // =========================
  // BOOTSTRAP
  // =========================
  function init() {
    startFlushLoop();
    initErrorTracking();
    initActivityTracking();
    startIdleMonitor();
    initLeaveHooks();

    // page_enter immediately
    enqueue({
      ...buildBasePayload("page_enter"),
      pageEnterMs
    });

    // After window load, send static + performance (HW requirement)
    window.addEventListener("load", async () => {
      const staticData = await collectStatic();
      const perfData = collectPerformance();

      enqueue({
        ...buildBasePayload("pageview"),
        static: staticData,
        performance: perfData
      });

      flush("initial");
    });
  }

  init();
})();