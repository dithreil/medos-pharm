export const highlightRequiredFields = (element = 'div.q-field__label') => {
    document.querySelectorAll(element).forEach((selector) => {
        if (!selector.textContent.endsWith('*')) return;
        const star = selector.textContent.slice(-1);
        const text = selector.textContent.slice(0, -2);
        selector.innerHTML = `${text} <span class="text-negative">${star}</span>`;
    });
};
