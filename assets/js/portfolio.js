const $ = require('jquery');

$(() => {
    // Show allocation percent field when unallocated checkbox unchecked
    setAllocationPercentVisibility();

    $('#portfolio_unallocated').click(setAllocationPercentVisibility);

    function setAllocationPercentVisibility()
    {
        const $input = $('#portfolio_allocationPercent');
        const $formGroup = $input.closest('.form-group');
        if ($('#portfolio_unallocated').is(':checked')) {
            $formGroup.hide();
            $input.removeAttr('required');
        } else {
            $formGroup.show();
            $input.attr('required', true);
        }
    }
});
