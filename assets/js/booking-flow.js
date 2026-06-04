/**
 * Two-step booking flow.
 *
 * Any form with class `booking-flow` containing `.bk-step--info` and
 * `.bk-step--payment` is wired so that:
 *   1. The "info" step is visible first.
 *   2. Clicking `.bk-next` validates info fields, then swaps to payment.
 *   3. `.bk-back` (optional) returns to step 1.
 *   4. The form's normal submit (now the "Payer" button) posts to PHP.
 */
(function () {
  function setup(form) {
    var info = form.querySelector('.bk-step--info');
    var pay  = form.querySelector('.bk-step--payment');
    var next = form.querySelector('.bk-next');
    var back = form.querySelector('.bk-back');
    if (!info || !pay || !next) return;

    function show(step) {
      info.hidden = step !== 'info';
      pay.hidden  = step !== 'payment';
    }
    show('info');

    next.addEventListener('click', function () {
      // Validate just the inputs in the info step (don't ask for empty
      // payment fields yet).
      var bad = false;
      info.querySelectorAll('input, textarea, select').forEach(function (el) {
        if (el.disabled || el.type === 'hidden') return;
        if (!el.checkValidity()) { el.reportValidity(); bad = true; }
      });
      if (bad) return;
      show('payment');
      pay.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    if (back) {
      back.addEventListener('click', function () { show('info'); });
    }
  }

  function init() {
    document.querySelectorAll('form.booking-flow').forEach(setup);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
