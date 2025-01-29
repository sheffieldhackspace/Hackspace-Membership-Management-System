import eslint from '@eslint/js';
import eslintPluginVue from 'eslint-plugin-vue';
import globals from 'globals';
import typescriptEslint from 'typescript-eslint';
import {includeIgnoreFile} from "@eslint/compat";
import path from "node:path";
import {fileURLToPath} from "node:url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const gitignorePath = path.resolve(__dirname, ".gitignore");

/** @type {import('eslint').Linter.Config[]} */
export default [
	includeIgnoreFile(gitignorePath),
	{
		files: [".resources/js/**/*.{js,mjs,cjs,ts,vue}"],
	},
	{
		files: [".resources/js/**/*.js"], languageOptions: {sourceType: "script"},
	},
	eslint.configs.recommended,
	...typescriptEslint.configs.recommended,
	...eslintPluginVue.configs['flat/recommended'],
	{
		files: ['**/*.{ts,vue}'],
			languageOptions: {
            ecmaVersion: 'latest',
                sourceType: 'module',
                globals: {
					...globals.browser,
					...globals.commonjs,
					...{
						"App": 'readonly',
						"route": 'readonly',
					}
				},
                parserOptions: {
                parser: typescriptEslint.parser,
            },
        },
		rules: {
			"vue/html-indent": ["error", 4],
			"vue/multi-word-component-names": ["off"]
		}
	}
];
