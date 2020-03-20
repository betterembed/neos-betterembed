import babel from 'rollup-plugin-babel';
import { terser } from 'rollup-plugin-terser';
import license from 'rollup-plugin-license';
import joinArray from 'join-array';
import composer from './composer.json';

const AUTHORS = joinArray({
    array: composer.authors.map(author => author.name),
    max: 4
});

const BANNER_CONTENT = `${composer.extra.neos['package-key']} - created by ${AUTHORS}
@link ${composer.homepage}
Copyright ${parseInt(new Date().getFullYear(), 10)} ${AUTHORS}
Licensed under ${composer.license}`;

export default [
    {
        input: 'Resources/Private/Assets/Main.js',
        onwarn: message => {
            if (message.code == 'EVAL') {
                return;
            }
        },
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
