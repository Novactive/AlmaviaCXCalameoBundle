(function (global) {
    console.log('calameo_publication.js')
    const ibexa = global.ibexa || global.eZ;

    if (!ibexa || !ibexa.BasePreviewField || !ibexa.BaseFileFieldValidator) {
        console.warn('[Calameo] Ibexa file field helpers are not available.');
        return;
    }

    const SELECTOR_FIELD = '.ibexa-field-edit--calameo_publication';
    const SELECTOR_LABEL_WRAPPER = '.ibexa-field-edit__label-wrapper';

    class CalameoPublicationPreviewField extends ibexa.BasePreviewField {
        loadDroppedFilePreview(event) {
            const preview = this.fieldContainer.querySelector('.ibexa-field-edit__preview');

            if (!preview || !event.target.files || !event.target.files.length) {
                return;
            }

            const file = event.target.files[0];
            const nameContainer = preview.querySelector('.ibexa-field-edit-preview__file-name');
            const previewLink = preview.querySelector('.ibexa-field-edit-preview__action--preview');

            if (nameContainer) {
                nameContainer.innerHTML = file.name;
                nameContainer.title = file.name;
            }

            if (previewLink) {
                previewLink.href = URL.createObjectURL(file);
            }
        }
    }

    [...document.querySelectorAll(SELECTOR_FIELD)].forEach((fieldContainer) => {
        const validator = new ibexa.BaseFileFieldValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap: [
                {
                    selector: 'input[type="file"]',
                    eventName: 'change',
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
                {
                    isValueValidator: false,
                    selector: 'input[type="file"]',
                    eventName: 'invalidFileSize',
                    callback: 'showFileSizeError',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                },
            ],
        });

        const previewField = new CalameoPublicationPreviewField({
            validator,
            fieldContainer,
        });

        previewField.init();

        ibexa.fieldTypeValidators = ibexa.fieldTypeValidators
          ? [...ibexa.fieldTypeValidators, validator]
          : [validator];
    });
})(window);
