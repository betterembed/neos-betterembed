const composer = require('./composer');

const AUTHOR = composer.authors[0].name;
const BANNER_CONTENT = `${composer.extra.neos['package-key']} - created by ${AUTHOR}
@link ${composer.homepage}
Copyright ${parseInt(new Date().getFullYear(), 10)} ${AUTHOR}
Licensed under ${composer.license}`;

module.exports = {
    plugins: {
        autoprefixer: true,
        cssnano: {
            preset: ['default', { discardComments: { removeAll: true } }]
        },
        'postcss-banner': {
            important: true,
            banner: BANNER_CONTENT
        }
    }
};
