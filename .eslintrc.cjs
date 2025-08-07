module.exports = {
    root: true,
    env: {
        node: true,
        jest: true,
    },
    extends: [
        "plugin:vue/essential",
        "eslint:recommended",
        "plugin:prettier/recommended",
    ],
    parserOptions: {
        parser: "babel-eslint",
    },
    rules: {
        "no-console": process.env.NODE_ENV === "production" ? "warn" : "off",
        "no-debugger": process.env.NODE_ENV === "production" ? "warn" : "off",
        "max-len": ["error", 120],
        quotes: [
            "error",
            "double",
            {
                avoidEscape: true,
                allowTemplateLiterals: true,
            },
        ],
        "no-unused-vars": "error",
        "no-empty": "error",
        "comma-dangle": [
            "error",
            {
                arrays: "always-multiline",
                objects: "always-multiline",
                imports: "always-multiline",
                exports: "always-multiline",
                functions: "only-multiline",
            },
        ],
        "array-bracket-spacing": ["error", "never"],
        "object-curly-spacing": ["error", "always"],
        "space-before-function-paren": [
            "error",
            {
                anonymous: "always",
                named: "never",
                asyncArrow: "always",
            },
        ],
        "object-shorthand": ["error", "always"],
    },
};
