/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/**
 * Detects swipe gestures on a specified target element and invokes a callback function with the swipe direction.
 * {@link https://gist.github.com/SleepWalker/da5636b1abcbaff48c4d}
 * @param {string} target - The CSS selector for the target element.
 * @param {function} callback - The function to invoke with the swipe direction.
 * @returns {void}
 */
const swipeDetect = (target, callback) => {
  let touchstartX = 0;
  let touchstartY = 0;
  let touchendX = 0;
  let touchendY = 0;

  const handleGesture = (touchstartX, touchstartY, touchendX, touchendY) => {
    const delx = touchendX - touchstartX;
    const dely = touchendY - touchstartY;
    if (Math.abs(delx) > Math.abs(dely)) {
      if (delx > 0) return 'right';
      else return 'left';
    } else if (Math.abs(delx) < Math.abs(dely)) {
      if (dely > 0) return 'down';
      else return 'up';
    } else return 'tap';
  };

  const gestureZone = document.querySelector(target);

  gestureZone.addEventListener(
    'touchstart',
    (event) => {
      touchstartX = event.changedTouches[0].screenX;
      touchstartY = event.changedTouches[0].screenY;
    }
  );

  gestureZone.addEventListener(
    'touchend',
    (event) => {
      touchendX = event.changedTouches[0].screenX;
      touchendY = event.changedTouches[0].screenY;
      callback(handleGesture(touchstartX, touchstartY, touchendX, touchendY));
    }
  );
};

export default swipeDetect;
