// Auto-submit quantity changes in the cart
document.querySelectorAll('.auto-submit').forEach(function (el) {
  el.addEventListener('change', function () { el.form.submit(); });
});
// Image preview on the sell page
var input = document.getElementById('imgInput'), preview = document.getElementById('imgPreview');
if (input && preview) {
  input.addEventListener('change', function () {
    var f = input.files[0];
    if (f) { preview.src = URL.createObjectURL(f); preview.hidden = false; } else { preview.hidden = true; }
  });
}
// Fade out flash messages
var flash = document.querySelector('.flash');
if (flash) setTimeout(function () { flash.style.opacity = '0'; }, 3500);
