document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.tryfont-container').forEach(container => {
    const fontFamily = container.dataset.fontFamily;
    const sampleText = container.querySelector('.sample-text');
    const controls = container.querySelector('.controls');

    const initialSize = parseInt(container.dataset.size) || 32;
    sampleText.style.fontFamily = `'${fontFamily}', sans-serif`;
    sampleText.style.fontSize = initialSize + 'px';
    sampleText.style.letterSpacing = '0em';
    sampleText.style.lineHeight = '1.4';

    function addControl(labelText, min, max, step, value, onChange) {
      const wrapper = document.createElement('label');
      wrapper.innerHTML = `${labelText}: <input type="range" min="${min}" max="${max}" step="${step}" value="${value}">`;
      const input = wrapper.querySelector('input');
      input.addEventListener('input', () => onChange(input.value));
      controls.appendChild(wrapper);
    }

    if (container.dataset.showSize === 'yes') {
      addControl("Font size", 10, 100, 1, initialSize, val => {
        sampleText.style.fontSize = val + 'px';
      });
    }

    if (container.dataset.showSpacing === 'yes') {
      addControl("Letter spacing", 0, 0.2, 0.01, 0, val => {
        sampleText.style.letterSpacing = val + 'em';
      });
    }

    if (container.dataset.showLineheight === 'yes') {
      addControl("Line height", 1, 3, 0.1, 1.4, val => {
        sampleText.style.lineHeight = val;
      });
    }
  });
});

