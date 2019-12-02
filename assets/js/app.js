/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

const $ = require('jquery');
import accessibleAutocomplete from 'accessible-autocomplete'

require('bootstrap');

$(() => {
    let suggestions = {};

    $('[data-autocomplete="true"]').each((i, field) => {
        let $field = $(field);
        const id = $field.attr('id');
        $field.attr('id', `${id}_autocomplete`);
        const url = $field.data('url');
        const $div = $field.closest('form').find('[data-autocomplete-container="true"]');
        $div.attr('id', `${id}-container`);
        $field.hide();

        accessibleAutocomplete({
            element: $div[0],
            id,
            source: (query, callback) => {
                $.post(url, {query}, data => {
                    callback(data.results);
                    suggestions = data.suggestions;
                });
            },
            onConfirm: (result) => {
                if (result) {
                    // Cannot set value of hidden fields, so detach first
                    const $clone = $field.clone();
                    $clone.show().val(result).hide();
                    $field.after($clone).remove();
                    $field = $clone;
                }
            },
            templates: {
                suggestion: query => {
                    return suggestions[query];
                }
            }
        });
    });
});
