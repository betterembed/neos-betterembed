const composer = require('./composer');
const joinArray = require('join-array');

const AUTHORS = joinArray({
    array: composer.authors.map(author => author.name),
    max: 4
});

const BANNER_CONTENT = `${composer.extra.neos['package-key']} - created by ${AUTHORS}
@link ${composer.homepage}
Copyright ${parseInt(new Date().getFullYear(), 10)} ${AUTHORS}
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
