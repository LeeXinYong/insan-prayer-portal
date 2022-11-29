class ColorPicker {
    #templates = [];
    #selfPickInput;

    colorPalette = [
        '#6610F2',
        '#6732E7',
        '#6F42C1',
        '#D63384',
        '#DC3545',
        '#EF3961',
        '#FD7E14',
        '#FFC107',
        '#FFC000',
        '#198754',
        '#20C997',
        '#46C67E',
        '#0DCAF0',
    ];

    constructor(element, options = {})
    {
        const defaultColor = (options?.defaultColor ?? "#000000").toLowerCase();

        this.colorPicker = element instanceof HTMLElement ? element : document.querySelector(element);
        this.colorPicker.style.display = 'none';

        const wrapper = document.createElement('div');
        wrapper.classList.add('color-picker', 'color-picker-wrapper');

        // prepare user choose color
        const selfPickWrapper = document.createElement('div');
        selfPickWrapper.classList.add('self-pick-wrapper');
        const selfPickInput = this.#selfPickInput = this.#createTemplate();
        const input = document.createElement('input');
        input.type = 'color';
        input.style.visibility = 'hidden';
        input.value = "#000000";
        this.#selfPickInput.value = "#000000";
        this.#selfPickInput.style.backgroundColor = "#000000";
        input.addEventListener('input', function () {
            selfPickInput.value = this.value;
            selfPickInput.style.backgroundColor = this.value;
        });
        const instance = this;
        input.addEventListener('change', function () {
            instance.#updateColor({target: selfPickInput});
        });
        this.#selfPickInput.append(input);
        this.#selfPickInput.addEventListener('click', input.click.bind(input));
        this.#selfPickInput.addEventListener('click', this.#updateColor.bind(this));
        selfPickWrapper.append("Pick a color:", this.#selfPickInput);

        // prepare color palette
        const colorPalette = (options?.colorPalette ?? this.colorPalette ?? []).map(color => color.toLowerCase());
        const palleteWrapper = document.createElement('div');
        palleteWrapper.classList.add('pallete-wrapper');
        if (colorPalette.length > 0) {
            palleteWrapper.append("Or choose from:");
            colorPalette.forEach(color => {
                const template = this.#createTemplate();
                template.style.backgroundColor = color;
                template.value = color;

                if (color === defaultColor) {
                    this.#selectColor(template);
                }

                template.addEventListener('click', this.#updateColor.bind(this));
                palleteWrapper.appendChild(template);
            });
        }

        if (!colorPalette.includes(defaultColor)) {
            input.value = defaultColor;
            this.#selfPickInput.value = defaultColor;
            this.#selfPickInput.style.backgroundColor = defaultColor;
            this.#selectColor(this.#selfPickInput);
        }


        this.colorPicker.replaceWith(wrapper);
        wrapper.append(selfPickWrapper, palleteWrapper, this.colorPicker);
    }

    #updateColor(event) {
        this.#selectColor(event.target);
        this.colorPicker.value = event.target.value;
    }

    #selectColor(element) {
        this.#templates.forEach(template => {
            template.classList.remove('selected');
            if (template.dataset.id === element.dataset.id) {
                template.classList.add('selected');
            }
        });
    }

    #createTemplate() {
        const template = document.createElement('div');
        template.classList.add('color-picker-template');
        template.dataset.id = this.#generateId();

        this.#templates.push(template);

        return template;
    }

    #generateId() {
        return Array.from((window.crypto || window.msCrypto).getRandomValues(new Uint8Array(16))).map(i => (i).toString(16)).join("");
    }

    pickColor(color) {
        const template = this.#templates.find(template => template.value === color);
        if (template) {
            this.#updateColor({target: template});
        } else {
            this.#selfPickInput.children[0].value = color;
            this.#selfPickInput.value = color;
            this.#selfPickInput.style.backgroundColor = color;
            this.#selectColor(this.#selfPickInput);
        }
        return this;
    }
}
