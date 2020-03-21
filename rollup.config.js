import babel from 'rollup-plugin-babel';
import { terser } from 'rollup-plugin-terser';
import joinArray from 'join-array';
import composer from './composer.json';

const AUTHORS = joinArray({
    array: composer.authors.map(author => author.name),
    max: 4
});

const BANNER_CONTENT = `/*!
 * ${composer.extra.neos['package-key']} - created by ${AUTHORS}
 * @link ${composer.homepage}
 * Copyright ${parseInt(new Date().getFullYear(), 10)} ${AUTHORS}
 * Licensed under ${composer.license}
 */`;

export default [
    {
        input: 'Resources/Private/Assets/Main.js',
        plugins: [babel(), terser()],
        output: {
            banner: BANNER_CONTENT,
            sourcemap: true,
            file: 'Resources/Public/Main.js',
            format: 'iife'
        }
    }
];
