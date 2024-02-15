/**
 * Checks if an element is visible in the viewport
 * @param {*} element 
 * @author Thomas Cardon, https://stackoverflow.com/a/7557433
 * @returns {boolean}
 */
function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
}

/**
 * Scrolls to the next schedule
 * @param {Array} schedules
 * @param {number} index
 * @param {margin} margin
 * @author Thomas Cardon
*/
function scrollStep(schedules, i, margin = 145) {
  if (schedules[i] == null) i = 0;
  console.log(i);
  
  let dims = schedules[i].getBoundingClientRect();
  window.scrollTo({
    top: dims.top - margin,
    left: window.scrollX,
    behavior: 'smooth'
  });

  setTimeout(() => scrollStep(schedules, i + 1), 1000);
}

/**
 * Starts the scrolling animation
 * @author Thomas Cardon
*/
function startScrollAnimation() {
  let schedules = Array.from(document.querySelectorAll('.table-responsive'));
  
  if (schedules.length > 0 && !isInViewport(schedules[schedules.length - 1]))
    scrollStep(schedules, 0);
  else console.log('No schedules to scroll');
}

docReady(startScrollAnimation);