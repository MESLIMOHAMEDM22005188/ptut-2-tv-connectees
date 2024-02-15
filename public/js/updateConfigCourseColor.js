document.addEventListener('DOMContentLoaded', function () {
    var colorInputs = document.querySelectorAll('.course-config-color-selector');

    colorInputs.forEach(function (colorInput) {
        colorInput.addEventListener('input', function () {
            var selectedColor = colorInput.value;
            var courseConfigForm = colorInput.closest('.course-config');
            courseConfigForm.style.backgroundColor = selectedColor;
        });
    });
});