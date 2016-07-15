const Utils = (() => {

  var toInitialCase = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
  };

  var hasClass = (el, cls) => {
    return el.className && new RegExp("(\\s|^)" + cls + "(\\s|$)").test(el.className);
  };

  return {
    toInitialCase: toInitialCase,
    hasClass: hasClass
  };

})();

export default Utils;
