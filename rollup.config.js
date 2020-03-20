import babel from 'rollup-plugin-babel';
import { terser } from 'rollup-plugin-terser';
import license from 'rollup-plugin-license';
import composer from './composer.json';

const AUTHOR = composer.authors[0].name;
const BANNER_CONTENT = `${composer.extra.neos['package-key']} - created by ${AUTHOR}
@link ${composer.homepage}
Copyright ${parseInt(new Date().getFullYear(), 10)} ${AUTHOR}
Licensed under ${composer.license}`;

export default [
    {
        input: 'Resources/Private/Assets/Main.js',
        plugins: [
            babel(),
            terser({
                output: {
                    comments: false
                }
            }),
            license({
                banner: {
                    content: BANNER_CONTENT,
                    commentStyle: 'ignored'
                }
            })
        ],
        output: {
            sourcemap: true,
            file: 'Resources/Public/Main.js',
            format: 'iife'
        }
    }
];
