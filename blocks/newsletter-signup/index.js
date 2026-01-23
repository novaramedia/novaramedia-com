(() => {
  'use strict';
  var e = {
      20: (e, r, t) => {
        var n = t(609),
          o = Symbol.for('react.element'),
          s = (Symbol.for('react.fragment'), Object.prototype.hasOwnProperty),
          l =
            n.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED
              .ReactCurrentOwner,
          a = { key: !0, ref: !0, __self: !0, __source: !0 };
        function i(e, r, t) {
          var n,
            i = {},
            c = null,
            d = null;
          for (n in (void 0 !== t && (c = '' + t),
          void 0 !== r.key && (c = '' + r.key),
          void 0 !== r.ref && (d = r.ref),
          r))
            s.call(r, n) && !a.hasOwnProperty(n) && (i[n] = r[n]);
          if (e && e.defaultProps)
            for (n in (r = e.defaultProps)) void 0 === i[n] && (i[n] = r[n]);
          return {
            $$typeof: o,
            type: e,
            key: c,
            ref: d,
            props: i,
            _owner: l.current,
          };
        }
        ((r.jsx = i), (r.jsxs = i));
      },
      609: (e) => {
        e.exports = window.React;
      },
      848: (e, r, t) => {
        e.exports = t(20);
      },
    },
    r = {};
  function t(n) {
    var o = r[n];
    if (void 0 !== o) return o.exports;
    var s = (r[n] = { exports: {} });
    return (e[n](s, s.exports, t), s.exports);
  }
  ((t.n = (e) => {
    var r = e && e.__esModule ? () => e.default : () => e;
    return (t.d(r, { a: r }), r);
  }),
    (t.d = (e, r) => {
      for (var n in r)
        t.o(r, n) &&
          !t.o(e, n) &&
          Object.defineProperty(e, n, { enumerable: !0, get: r[n] });
    }),
    (t.o = (e, r) => Object.prototype.hasOwnProperty.call(e, r)));
  const n = window.wp.blocks,
    o = window.wp.element,
    s = window.wp.i18n,
    l = window.wp.blockEditor,
    a = window.wp.apiFetch;
  var i = t.n(a);
  const c = window.wp.url;
  var d = t(848);
  const p = JSON.parse('{"UU":"flavor3/newsletter-signup"}');
  (0, n.registerBlockType)(p.UU, {
    edit: function (e) {
      let { attributes: r, setAttributes: t } = e;
      const [n, a] = (0, o.useState)(null),
        p = (0, l.useBlockProps)({
          className: 'wp-block-flavor3-newsletter-signup',
          style: {
            color: '#222222',
            backgroundColor: '#eeeeee',
            borderRadius: '5px',
            padding: '2rem',
          },
        });
      (0, o.useEffect)(() => {
        (async () => {
          const e = await i()({
            path: (0, c.addQueryArgs)('/wp/v2/newsletter', { per_page: 100 }),
          });
          a(e);
        })();
      }, []);
      const u = r.newsletter?.id || '';
      return (0, d.jsxs)('div', {
        ...p,
        children: [
          (0, d.jsx)('h3', {
            style: { marginTop: 0 },
            children: (0, s.__)('Newsletter Signup', 'novaramedia-com'),
          }),
          (0, d.jsx)('p', {
            children: (0, s.__)(
              'Select a newsletter. Displays an inline signup form in the content.',
              'novaramedia-com'
            ),
          }),
          (0, d.jsx)('form', {
            children: (0, d.jsxs)('select', {
              className: 'nm-block-newsletter-signup__select',
              onChange: (e) => {
                const r = n.find((r) => r.id === parseInt(e.target.value));
                t({ newsletter: r });
              },
              value: u,
              children: [
                (0, d.jsx)('option', {
                  value: '',
                  children: (0, s.__)(
                    '-- Select newsletter --',
                    'novaramedia-com'
                  ),
                }),
                n &&
                  n.map((e) =>
                    (0, d.jsx)(
                      'option',
                      { value: e.id, children: e.title.rendered },
                      e.id
                    )
                  ),
              ],
            }),
          }),
          r.newsletter &&
            (0, d.jsxs)('p', {
              style: { marginTop: '1rem', fontSize: '0.875rem', color: '#666' },
              children: [
                (0, s.__)('Selected:', 'novaramedia-com'),
                ' ',
                r.newsletter.title?.rendered,
              ],
            }),
        ],
      });
    },
    save: function () {
      return null;
    },
  });
})();
