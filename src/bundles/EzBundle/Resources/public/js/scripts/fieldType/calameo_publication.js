(function (global) {
    const SELECTOR_FIELD = '.ibexa-field-edit--calameo_publication';
    const SELECTOR_LABEL_WRAPPER = '.ibexa-field-edit__label-wrapper';

    class CalameoPublicationPreviewField extends global.eZ.BasePreviewField {
        /**
         * Loads dropped file preview
         *
         * @param {Event} event
         */
        loadDroppedFilePreview(event) {
            const preview = this.fieldContainer.querySelector('.ibexa-field-edit__preview');
            const nameContainer = preview.querySelector('.ibexa-field-edit-preview__file-name');
            const files = [].slice.call(event.target.files);

            nameContainer.innerHTML = files[0].name;
            nameContainer.title = files[0].name;

            preview.querySelector('.ibexa-field-edit-preview__action--preview').href = URL.createObjectURL(files[0]);
        }
    }

    [...document.querySelectorAll(SELECTOR_FIELD)].forEach(fieldContainer => {
        const validator = new global.eZ.BaseFileFieldValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: `input[type="file"]`,
                    eventName: 'change',
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
                {
                    isValueValidator: false,
                    selector: `input[type="file"]`,
                    eventName: 'invalidFileSize',
                    callback: 'showFileSizeError',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
            ],
        });
        const previewField = new CalameoPublicationPreviewField({
            validator,
            fieldContainer
        });

        previewField.init();

        global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
            [...global.eZ.fieldTypeValidators, validator] :
            [validator];
    })
})(window);
