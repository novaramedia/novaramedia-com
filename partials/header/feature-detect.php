<script typo="text/javascript">
  function addClass(className) {
    document.documentElement.classList.add(className);
  }
  var avif = new Image();
  avif.src = "data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgANogQEAwgMg8f8D///8WfhwB8+ErK42A=";
  avif.onload = function () {
    addClass("avif");
  };
  avif.onerror = function () {
  check_webp_feature(function (isSupported) {
    if (isSupported) {
    return addClass("webp");
    }
    return addClass("fallback");
  });
  };
  function check_webp_feature(callback) {
    var img = new Image();
    img.onload = function () {
      var result = img.width > 0 && img.height > 0;
      callback(result);
    };
    img.onerror = function () {
      callback(false);
    };
    img.src = "data:image/webp;base64,UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==";
  }
</script>
