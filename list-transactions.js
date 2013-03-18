function restoreCheckboxes()
{
    $(':checkbox').each(function() {
        var isTransactionSuccessful = $(this).data('success');
        if (isTransactionSuccessful) {
            var utime = $(this).data('utime');
            var isCheckboxSet = localStorage.getItem(utime) == 'true';
            $(this).prop('checked', isCheckboxSet);
        } else {
            $(this).prop('disabled', true);
            $(this).prop('checked', true);
            $(this).closest('tr').addClass('unsuccessful');
        }
    })
}

$(function() {
    restoreCheckboxes();

    $(':checkbox').click(function() {
        var utime = $(this).data('utime');
        var isChecked = $(this).is(':checked');
        localStorage.setItem(utime, isChecked);
    });
});
