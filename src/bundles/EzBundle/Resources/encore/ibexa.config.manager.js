const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
  ibexaConfigManager.add({
    ibexaConfig,
    entryName: 'ibexa-admin-ui-content-edit-parts-css',
    newItems: [
      path.resolve(__dirname, '../public/scss/fieldType/edit/calameo_publication.scss'),
    ],
  });

  ibexaConfigManager.add({
    ibexaConfig,
    entryName: 'ibexa-admin-ui-content-edit-parts-js',
    newItems: [
      path.resolve(__dirname, '../public/js/scripts/fieldType/calameo_publication.js'),
    ],
  });
};
